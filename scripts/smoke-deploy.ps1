param(
    [string]$BaseUrl = "https://hihi-shop.vercel.app"
)

# Smoke test sau khi deploy: kiểm tra các trang chính + tài nguyên tĩnh.
# Dùng: powershell -ExecutionPolicy Bypass -File scripts/smoke-deploy.ps1 -BaseUrl "https://domain-cua-ban"
# Yêu cầu: curl.exe (có sẵn trên Windows 10/11).

$BaseUrl = $BaseUrl.TrimEnd('/')
$script:fail = 0

function Test-Url {
    param(
        [string]$Path,
        [int[]]$Expect = @(200)
    )

    $url = "$BaseUrl$Path"
    $raw = (& curl.exe -s -o NUL -w "%{http_code}|%{redirect_url}" --max-time 45 $url 2>$null) -join ''

    if ([string]::IsNullOrWhiteSpace($raw)) {
        Write-Host ("[ERR ] {0} -> không gọi được (curl lỗi hoặc timeout)" -f $Path) -ForegroundColor Red
        $script:fail++
        return
    }

    $parts = $raw.Split('|')
    $code = 0
    [int]::TryParse($parts[0], [ref]$code) | Out-Null
    $redirect = if ($parts.Count -gt 1) { $parts[1] } else { '' }

    if ($Expect -contains $code) {
        $extra = if ($redirect) { "  ->  $redirect" } else { '' }
        Write-Host ("[ OK ] {0} -> {1}{2}" -f $Path, $code, $extra) -ForegroundColor Green
    } else {
        Write-Host ("[FAIL] {0} -> {1} (mong doi: {2})" -f $Path, $code, ($Expect -join '/')) -ForegroundColor Red
        $script:fail++
    }
}

Write-Host "Smoke test: $BaseUrl" -ForegroundColor Cyan
Write-Host ""

Test-Url "/"
Test-Url "/login"
Test-Url "/register"
Test-Url "/products"
Test-Url "/build/manifest.json"
Test-Url "/images/placeholder-giay.svg"

# Chưa đăng nhập thì các trang này phải redirect (302) về login
Test-Url "/cart" @(302)
Test-Url "/checkout" @(302)
Test-Url "/wishlist" @(302)

# Trang không tồn tại phải trả 404
Test-Url "/trang-khong-ton-tai" @(404)

Write-Host ""
if ($script:fail -eq 0) {
    Write-Host "PASS - tat ca kiem tra deu dat." -ForegroundColor Green
    exit 0
}

Write-Host ("FAIL - co {0} kiem tra that bai." -f $script:fail) -ForegroundColor Red
exit 1
