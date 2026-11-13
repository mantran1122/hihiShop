# Báo cáo bảo mật — HIHI Shop

Ngày rà soát: 13/11/2026 · Phạm vi: toàn bộ mã nguồn (`app/`, `routes/`, `config/`, `resources/views/`, `api/index.php`), dependency PHP + JS, cấu hình production.

## 1. Kết quả tổng quan

| Hạng mục | Kết quả |
|---|---|
| Lỗ hổng nghiêm trọng trong code | **1** (webhook thanh toán) — đã sửa |
| Semgrep SAST | **176 rule / 331 file → 0 finding** (`p/php`, `p/security-audit`, `p/owasp-top-ten`, `p/secrets`) |
| Advisory dependency PHP | **52 → 0** (sau `composer update`) |
| Advisory dependency JS | **18 → 3** (chỉ devDependencies, không lên production) |
| SQL injection | Không phát hiện (không có raw SQL) |
| XSS | Không phát hiện (Blade escape; chỉ 1 chỗ `{!! !!}` đã bọc `e()`) |
| CSRF | Bật toàn bộ, chỉ miễn trừ `/payment/webhook` |
| Phân quyền | Admin dùng `role:Admin`; 131 test bao phủ IDOR |
| Bí mật trong git | Không có (chỉ `.env.example`) |
| Test | **131 passed / 410 assertions** |

## 2. Đã sửa

### 2.1. [CRITICAL] Webhook thanh toán có thể bị gọi bởi bất kỳ ai

**Trước:** nếu `PAYMENT_WEBHOOK_SECRET` để trống, endpoint `/payment/webhook` chấp nhận mọi request. Khách biết mã đơn của mình (`OD…` hiện trên trang QR) có thể tự gọi webhook để đánh dấu **đã thanh toán** → mất tiền. Số tiền cũng không được đối chiếu.

**Sau:**
- Production **bắt buộc** có secret, thiếu → trả `503`, ghi log.
- Có secret → kiểm tra HMAC-SHA256 bằng `hash_equals` (chống timing attack).
- Nếu đối tác gửi `amount` nhỏ hơn `grand_total` → trả `422`, không đánh dấu đã trả.

Test: `SecurityHardeningTest` (2 test) + `PaymentWebhookTest`.

### 2.2. Thiếu header bảo mật

Thêm middleware `SecurityHeaders` cho mọi response:

```
Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; ...
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```

CSP buộc phải cho `'unsafe-inline'`/`'unsafe-eval'` vì app dùng Alpine.js và script inline; vẫn chặn được script từ domain lạ.

### 2.3. Lộ phiên bản PHP

`api/php.ini`: `expose_php=Off` → bỏ header `X-Powered-By`.

### 2.4. Chống spam

| Endpoint | Giới hạn |
|---|---|
| `POST /product/{id}/review` | 10/phút |
| `GET /search` | 30/phút |
| `POST /chat/message`, `/chat/feedback` | 40/phút |
| Đăng nhập | 10/phút + 5 lần sai |
| Quên/đặt lại mật khẩu | 6/phút |

### 2.5. Mật khẩu mạnh hơn trên production

`Password::defaults()`: production yêu cầu ≥10 ký tự, có chữ hoa/thường và số (local/testing giữ 8 ký tự để chạy test).

### 2.6. Dependency

- PHP: `composer update` → **0 advisory** (trước: 52 advisory thuộc 15 gói, gồm guzzle, symfony/http-foundation, laravel/framework, league/commonmark…).
- JS: `npm audit fix` → 18 → **3 moderate**, chỉ trong devDependencies (Vite/launch-editor), không có trong bundle gửi cho khách.

## 3. Kiểm soát đã xác minh

| Hạng mục | Bằng chứng |
|---|---|
| IDOR đơn hàng | `OrderAccessTest`: khách khác không xem/báo chuyển khoản/xác nhận được |
| Xác nhận thanh toán thủ công | Chỉ Admin; `simulatePayment` chỉ local hoặc Admin |
| Phân quyền admin | Nhóm route `['auth','role:Admin']` |
| Nâng quyền qua profile | `SecurityHardeningTest`: gửi `role=Admin` không có tác dụng |
| Mass assignment | `User::$fillable` không chứa `is_active`/role; không có `update($request->all())` |
| SQL injection | Không dùng raw SQL; toàn bộ Eloquent binding |
| XSS | Blade `{{ }}`; `nl2br(e($p->description))`; chatbot escape phía client |
| CSRF | Bật mặc định; miễn trừ duy nhất `/payment/webhook` |
| Session | Cookie `secure`, `httpOnly`, `SameSite=Lax`; regenerate khi đăng nhập |
| Mật khẩu | Hash bcrypt; `password`/`remember_token` nằm trong `$hidden` |
| OAuth | Google/Facebook qua Socialite, state do package xử lý |
| Lộ file | `.env` không track; `storage/` ngoài `public/`; handler tĩnh chặn `.php` và path traversal |
| Debug | `APP_DEBUG=false` trên production |
| Bí mật trong git | `git log -S` không tìm thấy DB password / admin password |

## 4. Việc cần làm trước khi mở bán

| # | Việc | Mức độ |
|---|---|---|
| 1 | **Đặt `PAYMENT_WEBHOOK_SECRET`** trên Vercel (chuỗi ngẫu nhiên ≥32 ký tự) | Bắt buộc |
| 2 | **Đổi mật khẩu** `admin@example.com`, `staff@example.com`, `customer@example.com`; xoá tài khoản demo `*.demo@hihishop.vn` | Bắt buộc |
| 3 | **Chuyển repo sang Private** (đang public — lộ toàn bộ mã nguồn) | Bắt buộc |
| 4 | **Cấu hình SMTP thật** (hiện mail ghi vào log → không gửi được mail quên mật khẩu) | Bắt buộc |
| 5 | Bật **2FA** cho admin hoặc ít nhất đổi email admin sang tên miền thật | Nên |
| 6 | Cân nhắc `SESSION_DRIVER=cookie` hoặc `SESSION_ENCRYPT=true` | Tuỳ chọn |
| 7 | Giới hạn `trustProxies('*')` nếu phát hiện bypass rate-limit qua `X-Forwarded-For` | Tuỳ chọn |
| 8 | Sao lưu TiDB định kỳ và thử khôi phục | Nên |
| 9 | Đưa Semgrep vào CI (chạy lại mỗi lần push) | Nên |

## 5. Cách kiểm tra lại

```powershell
composer audit --no-dev          # phải: No security vulnerability advisories found
npm audit                        # còn 3 moderate trong devDependencies
php artisan test                 # 131 tests
semgrep scan --config=p/php --config=p/security-audit --config=p/owasp-top-ten --config=p/secrets --metrics=off --exclude=vendor --exclude=node_modules .
curl -I https://hihi-shop.vercel.app/   # kiểm tra CSP, X-Frame-Options...
```

> Semgrep chạy bằng Python (`pip install semgrep`), trên Windows cần thêm `%APPDATA%\Python\Python312\Scripts` vào PATH và đặt `PYTHONUTF8=1` để không lỗi encoding khi ghi JSON.
> Kết quả lần này: 46 rule (php + security-audit) và 130 rule (owasp-top-ten + secrets) trên 331 file → **0 finding**. Nên đưa lệnh này vào CI để chạy lại mỗi lần push.
