param(
    [Parameter(Mandatory = $true)]
    [string]$RepoUrl
)

# Đẩy toàn bộ source lên GitHub để Vercel import.
# Dùng: powershell -ExecutionPolicy Bypass -File scripts/push-github.ps1 -RepoUrl "https://github.com/<tai-khoan>/hihi-shop.git"

$ErrorActionPreference = 'Stop'

Write-Host "1/4 Kiểm tra trạng thái git..." -ForegroundColor Cyan
$pending = git status --porcelain
if ($pending) {
    Write-Host "Còn file chưa commit:" -ForegroundColor Red
    $pending
    exit 1
}
Write-Host "   OK - working tree sạch"

Write-Host "2/4 Cấu hình remote origin..." -ForegroundColor Cyan
$remotes = git remote
if ($remotes -contains 'origin') {
    git remote set-url origin $RepoUrl
    Write-Host "   Đã cập nhật origin"
} else {
    git remote add origin $RepoUrl
    Write-Host "   Đã thêm origin"
}

Write-Host "3/4 Đặt nhánh chính là main..." -ForegroundColor Cyan
git branch -M main
Write-Host "   OK"

Write-Host "4/4 Push lên GitHub (đăng nhập bằng cửa sổ Git Credential Manager)..." -ForegroundColor Cyan
git push -u origin main

Write-Host ""
Write-Host "Đã push xong. Bước tiếp theo:" -ForegroundColor Green
Write-Host "  1. Vercel -> Add New Project -> Import repo vừa push"
Write-Host "  2. Nhập Environment Variables (xem docs/DEPLOY-VERCEL.md)"
Write-Host "  3. Deploy, sau đó chạy: powershell -ExecutionPolicy Bypass -File scripts/smoke-deploy.ps1 -BaseUrl https://<domain>"
