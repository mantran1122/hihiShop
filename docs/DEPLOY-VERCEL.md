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

## 2. Chuẩn bị database (bắt buộc)

Vercel **không** có MySQL sẵn và không dùng được SQLite file. Chọn 1 trong:

| Nhà cung cấp | Ghi chú |
|---|---|
| **Railway MySQL** (khuyến nghị) | Tạo project → New → Database → MySQL. Lấy `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`. Kết nối TCP thường, không cần TLS. |
| Aiven / TiDB Cloud | Có free tier nhưng **bắt buộc TLS** → cần cấu hình thêm SSL cho PDO, dễ lỗi hơn khi test lần đầu. |

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
- `DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD` của MySQL cloud
- `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` (giữ nguyên trong `.env` local)

Nạp schema + dữ liệu demo lên DB cloud:

```powershell
php artisan migrate --seed --force --env=production
```

Lệnh này tạo: 4 danh mục, 4 sản phẩm + biến thể, 7 voucher, 6 đánh giá mẫu, 3 tài khoản demo (admin/staff/customer).

> Kiểm tra nhanh: `php artisan tinker --env=production --execute="echo App\Models\SanPham::count();"` → phải ra `4`.

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
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
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

**Cách A — Git integration (khuyến nghị):** push code lên GitHub → Vercel → Add New Project → Import repo. Mỗi lần push là một deployment, có preview URL.

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

## 7. Tài khoản demo (đổi mật khẩu trước khi cho người ngoài dùng)

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | `admin@example.com` | `admin123` |
| Staff | `staff@example.com` | `staff123` |
| Customer | `customer@example.com` | `customer123` |

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
- [ ] Bật/tắt sản phẩm, đổi quyền người dùng

**Chatbot**: bấm widget góc phải, hỏi "giao hàng bao lâu", "có nước hoa nào" → trả lời theo dữ liệu DB.

## 9. Troubleshooting

| Triệu chứng | Nguyên nhân / cách xử lý |
|---|---|
| 500 ngay trang đầu | Xem **Vercel → Deployments → Functions → Logs**. Thường do thiếu `APP_KEY` hoặc không kết nối được MySQL. |
| Trang hiện không có CSS/JS | Chưa commit `public/build` hoặc manifest cũ. Chạy `npm run build` rồi commit lại. |
| `redirect_uri_mismatch` | URI Google nhận được không khớp 100% với Console. Kiểm tra `APP_URL` (đúng `https`, không có `/` cuối) và đã thêm URI chưa. |
| Đăng nhập xong bị đá về `/login` | Session không lưu được: kiểm tra `SESSION_DRIVER=database` và bảng `sessions` đã migrate. |
| Ảnh sản phẩm 404 | Ảnh phải nằm trong `public/images/...` và được commit; đường dẫn lưu trong DB là tương đối (`images/...`). |
| Sửa code không thấy đổi | Chưa redeploy, hoặc đang xem preview cũ. |

## 10. Giới hạn cần biết

- **Không upload file bền vững**: `storage/` read-only, `storage:link` không dùng được. Ảnh sản phẩm phải nằm trong `public/images` hoặc CDN ngoài (S3/Cloudinary).
- **Cron / queue**: `QUEUE_CONNECTION=sync`; muốn queue thật phải dùng Redis/Upstash + worker ngoài.
- **Cold start** ~250ms cho request đầu.
- **Giới hạn function**: 30s/request (đã set `maxDuration`), 1024MB RAM.
- **Chi phí**: gói Hobby giới hạn thời gian chạy function; demo test thì thoải mái.

## 11. Phương án B (nếu Vercel gặp trục trặc)

Laravel cần filesystem ghi được và MySQL — Railway / Render / VPS sẽ ổn định hơn nhiều cho demo dài hạn. Khi cần, có thể bổ sung `Dockerfile` + `docker-compose.yml` (PHP-FPM + Nginx + MySQL) để chạy 1 lệnh trên Railway/Render; cấu trúc project hiện tại không cần sửa gì thêm để đóng gói Docker.
