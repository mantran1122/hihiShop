# Checklist trước khi mở bán (go-live)

Danh sách việc **bắt buộc** phải xong trước khi cho khách thật mua hàng, xếp theo mức độ rủi ro.

## 1. Cấu hình bắt buộc

> **Đã xong:** database TiDB (`hihi_shop`) đã migrate + seed, có thể test luồng đặt hàng thật.
> **Còn lại:** nhập biến môi trường lên Vercel, SMTP thật, webhook thanh toán thật, đổi tài khoản demo.

| Việc | Cách làm |
|---|---|
| `APP_ENV=production`, `APP_DEBUG=false` | Nếu để `APP_DEBUG=true`, lỗi sẽ lộ stack trace + biến môi trường |
| `APP_KEY` riêng cho production | `php artisan key:generate --show`, dán vào Vercel, **không** dùng key của máy dev |
| `APP_URL` đúng domain `https` | Sai APP_URL → link trong email/QR/OAuth đều sai |
| `SESSION_SECURE_COOKIE=true` | Bắt buộc khi chạy HTTPS |
| HTTPS | Vercel tự cấp; nếu custom domain thì trỏ DNS + bật SSL |

## 2. Việc còn thiếu để bán thật

### 2.1. Email (đang là `MAIL_MAILER=log` → KHÔNG gửi được mail)
Khách quên mật khẩu sẽ **không nhận được mail**. Cần:
- Đăng ký SMTP (Resend / Mailgun / Gmail App Password / SMTP của hosting)
- Đặt `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`
- Kiểm tra: `php artisan hihi:mail-check ban@example.com` → nhận được mail là cấu hình đúng
- Test luồng: vào `/forgot-password`, gửi thử và kiểm tra hộp thư

### 2.2. Xác nhận thanh toán thật
Hiện có 2 đường: nút "Mô phỏng thanh toán" (chỉ để test) và webhook `/payment/webhook`.
- Nối Casso / PayOS / SePay hoặc webhook ngân hàng → trỏ về `https://<domain>/payment/webhook`
- Đặt `PAYMENT_WEBHOOK_SECRET` + cấu hình bên đối tác gửi header `X-Webhook-Signature` (HMAC-SHA256 của raw body)
- **Nút mô phỏng thanh toán đã tự động ẩn trên production**: chỉ hiện khi `APP_ENV=local` hoặc tài khoản Admin. Khách trên production chỉ thấy hướng dẫn chờ hệ thống tự xác nhận.
- Thử chuyển khoản thật 1 đơn nhỏ → kiểm tra đơn tự chuyển `paid`

### 2.3. Tài khoản demo
- Đổi mật khẩu hoặc **xoá** `admin@example.com / admin123`, `staff@example.com`, `customer@example.com` trước khi public
- Tạo tài khoản admin riêng với mật khẩu mạnh

### 2.4. Dữ liệu & sao lưu
- Bật backup tự động cho TiDB Cloud (hoặc `mysqldump` định kỳ ra nơi khác)
- Thử phục hồi 1 lần trước khi mở bán
- Ảnh sản phẩm: chuyển sang CDN/S3 nếu số lượng lớn (Vercel chỉ phục vụ file tĩnh trong repo)

### 2.5. Pháp lý & vận hành
- Thông tin liên hệ, chính sách đổi trả, bảo hành, vận chuyển hiển thị rõ ở footer
- Chính sách bảo mật dữ liệu cá nhân (Nghị định 13/2023/NĐ-CP) nếu thu thập SĐT/địa chỉ
- Hoá đơn/thuế nếu hộ kinh doanh yêu cầu

## 3. Bảo mật đã có sẵn trong code

- CSRF cho toàn bộ form (trừ webhook có chữ ký HMAC)
- XSS: Blade escape mặc định; chat widget escape trước khi render
- Chặn xem đơn hàng của người khác (success/QR/status/simulate chỉ chủ đơn hoặc Admin)
- Rate limit: login 10 lần/phút, chat 40 request/phút
- Chống sửa giá: mọi cước ship/voucher/tổng tiền tính lại ở server
- Chống oversell: khoá `lockForUpdate` khi trừ tồn kho trong transaction
- Huỷ đơn hoàn tồn kho + hoàn lượt voucher

## 4. Trước mỗi lần deploy

```powershell
php artisan test                        # hiện 96 tests
npm run build                           # nếu có sửa CSS/JS
git add public/build && git commit
vercel --prod
powershell -ExecutionPolicy Bypass -File scripts/smoke-deploy.ps1 -BaseUrl "https://<domain>"
php artisan hihi:db-check --env=production
```

## 5. Vận hành hằng ngày

- Xem đơn mới: `/admin/orders`
- Duyệt/ẩn đánh giá xấu: `/admin/reviews`
- Quản lý voucher: `/admin/vouchers` (nhớ đặt "Lượt dùng mỗi khách" cho mã khuyến mãi)
- Theo dõi lỗi: Vercel → Deployments → Functions → Logs (log ghi ra stderr)
- Chatbot: xem chất lượng bộ nhớ bằng `AiTrainingPair::orderByDesc('quality_score')`

## 6. Rủi ro còn lại (chấp nhận được cho bản đầu)

- Serverless trên Vercel: cold start ~250ms, không upload file, queue chạy `sync`
- Chatbot là rule + bộ nhớ, không phải LLM → câu hỏi quá khác cách diễn đạt có thể chưa khớp
- Chưa có mã giảm giá theo từng sản phẩm/danh mục (chỉ theo đơn hàng)
- Chưa có tích hợp đơn vị vận chuyển thật (mã vận đơn đang sinh tự động)
