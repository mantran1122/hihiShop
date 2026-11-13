# Deploy HIHI Shop lên Vercel

Tài liệu này để đưa bản demo lên Vercel và giao cho admin bên đối tác test.

## 1. Kiến trúc

```
Vercel
├── Static (CDN)      : /build/*  /images/*  (CSS, JS, ảnh sản phẩm)
└── Serverless Function: api/index.php  →  public/index.php (Laravel 12)
                                │
                                └── MySQL cloud (bắt buộc)
Storage ghi tạm: /tmp/hihi-shop/{views,sessions,cache,logs}
```

- Runtime PHP: `vercel-php@0.7.4` (PHP 8.3) — runtime cộng đồng, không phải runtime chính thức của Vercel.
- Filesystem trên Vercel là **read-only** (trừ `/tmp`), nên [api/index.php](../api/index.php) tự tạo thư mục tạm và trỏ `VIEW_COMPILED_PATH` vào đó trước khi boot Laravel.

## 2. Chuẩn bị database — TiDB Cloud

> **Trạng thái hiện tại:** database `hihi_shop` đã được tạo trên TiDB Cloud Serverless (region Singapore, TiDB v8.5.3), đã chạy **22 migration + seed dữ liệu demo** và kiểm tra kết nối TLS thành công. Thông tin đăng nhập nằm trong `.env.production` ở máy dev (file này đã gitignore) và cần nhập lại vào Vercel → Environment Variables.

Vercel **không** có MySQL sẵn và không dùng được SQLite file → dùng **TiDB Cloud Serverless** (MySQL-compatible, có free tier).

### 2.1. Tạo cluster và lấy thông số

1. Vào [tidbcloud.com](https://tidbcloud.com) → **Create Cluster → Serverless** (chọn region gần VN, ví dụ Singapore).
2. Mở cluster → **Connect** → chọn **General connection / MySQL CLI**, ghi lại:

| Thông số | Ví dụ |
|---|---|
| Host | `gateway01.ap-southeast-1.prod.aws.tidbcloud.com` |
| Port | `4000` |
| Username | `3KmH2xyz.root` (có tiền tố, **không** phải chỉ `root`) |
| Password | chuỗi tạo ngay khi tạo cluster |

3. Tạo database trong **SQL Editor** của TiDB Cloud:

```sql
CREATE DATABASE IF NOT EXISTS hihi_shop
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Nếu cluster bật **Allowed IP Addresses**, thêm IP máy bạn (để chạy migrate) và để `0.0.0.0/0` cho Vercel (hoặc dải IP của Vercel) nếu chưa chạy được.

### 2.2. TLS — bắt buộc với TiDB

TiDB Cloud **bắt buộc TLS**, PDO mặc định không dùng TLS nên sẽ báo lỗi kết nối. Project đã xử lý sẵn:

- Bật bằng `DB_SSL=true`.
- CA của TiDB Cloud Serverless (Let's Encrypt **ISRG Root X1**) đã đóng gói tại [`storage/certs/tidb-ca.pem`](../storage/certs/tidb-ca.pem) → không cần tải file CA lên Vercel.
- `DB_SSL_VERIFY=true` để xác thực chứng chỉ server.
- Nếu dùng **TiDB Cloud Dedicated** (CA khác): tải `ca.pem` trong console, đặt vào `storage/certs/` và trỏ `MYSQL_ATTR_SSL_CA=/duong/dan/ca.pem`.

Kiểm tra nhanh TLS đã bật chưa:

```powershell
php artisan hihi:db-check --env=production
```


## 3. Tạo `.env.production` và nạp dữ liệu mẫu (chạy từ máy local)

```powershell
# 1. Tạo file cấu hình production từ mẫu (file này đã nằm trong .gitignore)
Copy-Item .env.production.example .env.production

# 2. Sinh APP_KEY rồi dán vào .env.production (mục APP_KEY)
php artisan key:generate --show
```

Mở `.env.production`, điền:
- `APP_KEY=base64:...` (lấy từ lệnh trên)
- `APP_URL=https://hihi-shop.vercel.app` (đổi theo domain thật)
- `DB_HOST / DB_PORT=4000 / DB_DATABASE=hihi_shop / DB_USERNAME / DB_PASSWORD` của TiDB Cloud
- `DB_SSL=true` (TiDB bắt buộc TLS), `DB_SSL_VERIFY=true`
- `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` (giữ nguyên trong `.env` local)

Nạp schema + dữ liệu demo lên TiDB:

```powershell
php artisan migrate --seed --force --env=production
```

Lệnh này tạo: 4 danh mục, 4 sản phẩm + biến thể, 7 voucher, 6 đánh giá mẫu, 3 tài khoản demo (admin/staff/customer).

> Kiểm tra bằng `php artisan hihi:db-check --env=production`: lệnh sẽ in ra phiên bản server, **trạng thái TLS**, danh sách bảng và số bản ghi. Nếu báo `TLS chưa bật` nghĩa là `DB_SSL` chưa được đọc.

## 4. Khai báo biến môi trường trên Vercel

Vercel → Project → **Settings → Environment Variables** → nhập **y hệt** nội dung `.env.production` (chọn cả 3 môi trường Production/Preview/Development).

Tối thiểu phải có:

```env
APP_NAME="HIHI Shop"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://hihi-shop.vercel.app
LOG_CHANNEL=stderr
DB_CONNECTION=mysql
DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com
DB_PORT=4000
DB_DATABASE=hihi_shop
DB_USERNAME=xxxxxxxx.root
DB_PASSWORD=...
DB_SSL=true
DB_SSL_VERIFY=true
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
MAIL_MAILER=log
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
```

⚠️ **Không** set `GOOGLE_REDIRECT_URI`. [config/services.php](../config/services.php) tự ghép `APP_URL + /auth/google/callback`, nhờ vậy đổi domain chỉ cần sửa `APP_URL`.
⚠️ Đừng copy nguyên chuỗi `${APP_URL}` — cú pháp đó chỉ được expand khi đọc file `.env`, không expand trong Environment Variables của Vercel.

## 5. Deploy

### 5.1. Đưa code lên Git (bắt buộc nếu deploy qua GitHub)

Tạo repo trống trên GitHub (khuyến nghị để **Private** trong giai đoạn test), rồi:

```powershell
git remote add origin https://github.com/<tai-khoan>/hihi-shop.git
git branch -M main
git push -u origin main
```

Lưu ý:
- `.env.production` **không** được commit (đã nằm trong `.gitignore`) → biến môi trường nhập trực tiếp trên Vercel.
- `public/build` đã commit sẵn để Vercel không cần chạy `npm run build`.
- Repo ~85MB (phần lớn là ảnh sản phẩm trong `public/images`) → lần push đầu có thể mất vài phút.
- Nếu chưa cài Git credential, dùng Personal Access Token thay mật khẩu: `https://<token>@github.com/<tai-khoan>/hihi-shop.git`.

### 5.2. Import vào Vercel

**Cách A — Git integration (khuyến nghị):** Vercel → Add New Project → Import repo vừa push → nhập Environment Variables → Deploy. Mỗi lần push là một deployment mới, có preview URL.

**Cách B — CLI:**

```powershell
npm i -g vercel
vercel login
vercel          # deploy preview
vercel --prod   # deploy production
```

`public/build` (CSS/JS đã compile) **được commit sẵn** trong repo để Vercel không phụ thuộc bước `npm run build`. Khi sửa CSS/JS phải chạy `npm run build` rồi commit lại thư mục này.

### Kiểm tra sau khi deploy

```powershell
powershell -ExecutionPolicy Bypass -File scripts/smoke-deploy.ps1 -BaseUrl "https://hihi-shop.vercel.app"
```

Script kiểm tra: trang chủ / login / register / products trả 200, CSS-JS-ảnh tải được, `/cart` `/checkout` `/wishlist` redirect 302 về login khi chưa đăng nhập, URL sai trả 404.

## 6. OAuth Google trên domain mới

Đã đăng ký sẵn 3 redirect URI trong Google Cloud Console:

- `http://127.0.0.1:8000/auth/google/callback` (local)
- `https://hihi-shop.vercel.app/auth/google/callback`
- `https://hihishop.vn/auth/google/callback`

Khi dùng domain khác (ví dụ `hihi-shop-git-main-xxx.vercel.app` hoặc custom domain mới) → phải thêm URI đó vào **Authorized redirect URIs**, nếu không Google trả `redirect_uri_mismatch`.

Lưu ý:
- Thêm `hihishop.vn` vào **Authorized domains** của OAuth consent screen.
- Preview deployment của Vercel có URL riêng và Google **không hỗ trợ wildcard** → đăng nhập Google sẽ lỗi trên preview, chỉ chạy ở domain đã đăng ký.
- Consent screen phải ở trạng thái **In production** thì người ngoài danh sách test mới đăng nhập được.

## 7. Tài khoản đăng nhập

**Production:** seeder **không** tạo tài khoản demo. Tạo admin thật bằng biến môi trường:

```env
ADMIN_EMAIL=ban@hihishop.vn
ADMIN_PASSWORD=<mật khẩu mạnh>
```

rồi chạy `php artisan db:seed --class=DemoUserAndRolesSeeder --force --env=production` (chỉ tạo/cập nhật role, không đụng dữ liệu khác).

**Local/testing:** tài khoản demo `admin@example.com`, `staff@example.com`, `customer@example.com` được tạo với mật khẩu lấy từ `DEMO_USER_PASSWORD` (mặc định chỉ khi chạy local/testing). Không dùng các tài khoản này trên production.

## 8. Checklist test cho admin

**Khách (chưa đăng nhập)**
- [ ] Trang chủ hiển thị đúng ảnh/CSS, danh mục, sản phẩm
- [ ] Bấm "Thêm vào giỏ" khi chưa đăng nhập → chuyển về `/login`, đăng nhập xong quay lại đúng trang
- [ ] Tìm kiếm, lọc theo danh mục, xem chi tiết sản phẩm
- [ ] Đọc đánh giá sản phẩm (không thấy form viết đánh giá)

**Khách hàng**
- [ ] Đăng ký mới, đăng nhập Google
- [ ] Thêm/sửa/xoá giỏ hàng
- [ ] Checkout: đổi tỉnh → cước ship đổi; mở "Kho Voucher" → áp `HIHI10` + `FREESHIP15`
- [ ] Thanh toán COD → trang thành công
- [ ] Thanh toán VietQR → trang QR có đếm ngược, bấm "Mô phỏng thanh toán" → tự chuyển sang trang thành công
- [ ] Viết đánh giá 1–5 sao, sửa lại, xoá
- [ ] Thả tim sản phẩm → badge "Yêu thích" tăng, trang `/wishlist` hiển thị
- [ ] Xem "Đơn mua của tôi"

**Admin**
- [ ] Đăng nhập `admin@example.com` → vào `/admin`
- [ ] Đổi trạng thái đơn hàng, xem chi tiết đơn
- [ ] **Thêm sản phẩm mới** (`/admin/products/create`): nhập tên, danh mục, giá, tồn kho → sản phẩm xuất hiện ở cửa hàng
- [ ] **Sửa sản phẩm**: đổi tên/giá/tồn kho → cập nhật đúng ở cả trang sản phẩm
- [ ] Bật/tắt (ẩn/hiện) sản phẩm
- [ ] **Quản lý voucher** (`/admin/vouchers`): tạo mã mới → thấy mã trong "Kho Voucher" ở trang thanh toán; tắt mã → không áp dụng được nữa
- [ ] **Quản lý đánh giá** (`/admin/reviews`): ẩn một đánh giá → đánh giá đó biến mất khỏi trang sản phẩm; bấm Duyệt để hiện lại
- [ ] **Quản lý danh mục / thương hiệu** (`/admin/categories`, `/admin/brands`): thêm danh mục mới → hiện ở menu cửa hàng; danh mục còn sản phẩm thì không cho ẩn
- [ ] **Cài đặt thanh toán** (`/admin/settings/payment`): đổi số tài khoản / ngân hàng → mã QR ở bước thanh toán dùng thông tin mới ngay
- [ ] Huỷ một đơn đã đặt → tồn kho được cộng lại
- [ ] Đổi quyền người dùng

**Chatbot** (tự huấn luyện, không dùng LLM — xem [AI-CHATBOT.md](AI-CHATBOT.md))
- [ ] Bấm widget góc phải, hỏi "giao hàng bao lâu", "có nước hoa nào" → trả lời theo dữ liệu DB
- [ ] Hỏi lại **đúng câu vừa rồi** → câu trả lời lấy từ bộ nhớ (nhanh, giống hệt)
- [ ] Bấm 👍 / 👎 dưới câu trả lời → hiện "Đã ghi nhận", điểm chất lượng cặp hỏi–đáp thay đổi

## 9. Troubleshooting

| Triệu chứng | Nguyên nhân / cách xử lý |
|---|---|
| 500 ngay trang đầu | Xem **Vercel → Deployments → Functions → Logs**. Thường do thiếu `APP_KEY` hoặc không kết nối được MySQL. |
| Trang hiện không có CSS/JS | Chưa commit `public/build` hoặc manifest cũ. Chạy `npm run build` rồi commit lại. |
| `redirect_uri_mismatch` | URI Google nhận được không khớp 100% với Console. Kiểm tra `APP_URL` (đúng `https`, không có `/` cuối) và đã thêm URI chưa. |
| `SQLSTATE[HY000] [2002]` / `SSL connection error` | Thiếu TLS: đặt `DB_SSL=true`. Nếu vẫn lỗi do không đọc được CA trên Vercel, tạm đặt `DB_SSL_VERIFY=false` (vẫn mã hoá TLS, chỉ bỏ bước xác thực CA). |
| `Access denied for user 'root'` | TiDB Cloud yêu cầu username **có tiền tố**, dạng `3KmH2xyz.root`, không phải `root`. |
| `Unknown collation: 'utf8mb4_unicode_ci'` | Đổi `DB_COLLATION=utf8mb4_general_ci` (hoặc `utf8mb4_bin`) rồi migrate lại. |
| `This connection is in read-only mode` | Đang trỏ nhầm vào TiDB **read-only replica / branch**; dùng connection của primary. |
| Đăng nhập xong bị đá về `/login` | Session không lưu được: kiểm tra `SESSION_DRIVER=database` và bảng `sessions` đã migrate. |
| Ảnh sản phẩm 404 | Ảnh phải nằm trong `public/images/...` và được commit; đường dẫn lưu trong DB là tương đối (`images/...`). |
| Sửa code không thấy đổi | Chưa redeploy, hoặc đang xem preview cũ. |

## 10. Giới hạn cần biết

- **TiDB không bắt buộc foreign key**: TiDB nhận cú pháp FK nhưng mặc định không enforce (cần `SET GLOBAL foreign_key_checks = ON` trước khi tạo bảng nếu muốn chặt). Ứng dụng đã tự kiểm tra tồn kho/ràng buộc ở tầng code nên demo vẫn đúng.
- **Webhook thanh toán**: đặt `PAYMENT_WEBHOOK_SECRET` trên production để bắt buộc chữ ký HMAC-SHA256 (header `X-Webhook-Signature`). Khi để trống, endpoint `/payment/webhook` chấp nhận mọi request — chỉ phù hợp cho demo.
- **Không upload file bền vững**: `storage/` read-only, `storage:link` không dùng được. Ảnh sản phẩm phải nằm trong `public/images` hoặc CDN ngoài (S3/Cloudinary).
- **Cron / queue**: `QUEUE_CONNECTION=sync`; muốn queue thật phải dùng Redis/Upstash + worker ngoài.
- **Cold start** ~250ms cho request đầu.
- **Giới hạn function**: 30s/request (đã set `maxDuration`), 1024MB RAM.
- **Chi phí**: gói Hobby giới hạn thời gian chạy function; demo test thì thoải mái.

## 11. Phương án B (nếu Vercel gặp trục trặc)

Laravel cần filesystem ghi được và MySQL — Railway / Render / VPS sẽ ổn định hơn nhiều cho demo dài hạn. Khi cần, có thể bổ sung `Dockerfile` + `docker-compose.yml` (PHP-FPM + Nginx + MySQL) để chạy 1 lệnh trên Railway/Render; cấu trúc project hiện tại không cần sửa gì thêm để đóng gói Docker.
