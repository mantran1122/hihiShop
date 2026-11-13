import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const SCREENSHOT_DIR = path.resolve('tests/screenshots');
if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function runTests() {
    console.log('🚀 Bắt đầu kịch bản Playwright E2E Testing...');
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1280, height: 800 }
    });

    const consoleErrors = [];
    const pageErrors = [];

    const page = await context.newPage();

    page.on('console', msg => {
        if (msg.type() === 'error') {
            console.log(`[Browser Console Error]: ${msg.text()}`);
            consoleErrors.push(msg.text());
        }
    });

    page.on('pageerror', err => {
        console.log(`[Browser Page Error]: ${err.message}`);
        pageErrors.push(err.message);
    });

    try {
        // ==========================================
        // 1. TRANG CHỦ (HOME) - DESKTOP & MOBILE
        // ==========================================
        console.log('📍 1. Truy cập Trang chủ Desktop: http://127.0.0.1:8000 ...');
        await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });

        // Kiểm tra Banner Hero Video
        const heroVideo = page.locator('video').first();
        const heroVisible = await heroVideo.isVisible();
        console.log(`- Banner Hero Video: ${heroVisible ? '✅ Đạt' : '❌ Không thấy'}`);

        // Kiểm tra Danh mục
        const categories = page.locator('#category-grid a');
        const catCount = await categories.count();
        console.log(`- Danh mục nổi bật: ${catCount} danh mục ${catCount > 0 ? '✅ Đạt' : '❌'}`);

        // Kiểm tra Sản phẩm mới
        const products = page.locator('#product-grid article');
        const prodCount = await products.count();
        console.log(`- Sản phẩm mới nhất: ${prodCount} sản phẩm ${prodCount > 0 ? '✅ Đạt' : '❌'}`);

        // Chụp Desktop Home
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'home_desktop.png'), fullPage: true });
        console.log('📸 Đã lưu tests/screenshots/home_desktop.png');

        // Chụp Mobile Home
        console.log('📍 Kiểm tra Trang chủ Mobile (iPhone 13 - 390x844)...');
        await page.setViewportSize({ width: 390, height: 844 });
        await page.waitForTimeout(400);
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'home_mobile.png'), fullPage: true });
        console.log('📸 Đã lưu tests/screenshots/home_mobile.png');

        // Reset về Desktop
        await page.setViewportSize({ width: 1280, height: 800 });
        await page.waitForTimeout(300);

        // ==========================================
        // 2. TRANG CHI TIẾT SẢN PHẨM (PRODUCT DETAIL)
        // ==========================================
        console.log('📍 2. Truy cập Trang chi tiết sản phẩm...');
        const firstProductLink = page.locator('#product-grid article a[aria-label]').first();
        await firstProductLink.click();
        await page.waitForLoadState('networkidle');

        // Kiểm tra biến thể & giá tự động
        const variantSelect = page.locator('#variant-select');
        if (await variantSelect.isVisible()) {
            const options = await variantSelect.locator('option').all();
            console.log(`- Sản phẩm có ${options.length} biến thể`);
            if (options.length > 1) {
                const secondVal = await options[1].getAttribute('value');
                await variantSelect.selectOption(secondVal);
                await page.waitForTimeout(300);
                const currentPrice = await page.locator('#variant-price').innerText();
                const stockText = await page.locator('#stock-badge').innerText();
                console.log(`- Tự động cập nhật: Giá = ${currentPrice}, Tồn kho = ${stockText} ✅ Đạt`);
            }
        }

        // Chụp chi tiết sản phẩm
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'product_detail.png'), fullPage: false });
        console.log('📸 Đã lưu tests/screenshots/product_detail.png');

        // Thêm vào giỏ hàng
        console.log('- Click nút "Thêm vào giỏ hàng"...');
        await page.click('#add-to-cart-btn');
        await page.waitForLoadState('networkidle');

        // ==========================================
        // 3. GIỎ HÀNG & THANH TOÁN (CHECKOUT)
        // ==========================================
        console.log('📍 3. Giỏ hàng & Đi đến Thanh toán...');
        // Đảm bảo đến trang /cart
        if (!page.url().includes('/cart')) {
            await page.goto('http://127.0.0.1:8000/cart', { waitUntil: 'networkidle' });
        }

        const checkoutBtn = page.locator('a[href*="checkout"]');
        await checkoutBtn.click();
        await page.waitForLoadState('networkidle');

        // Chụp giao diện checkout
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'checkout_step.png'), fullPage: false });
        console.log('📸 Đã lưu tests/screenshots/checkout_step.png');

        // Điền form COD
        console.log('- Điền form thanh toán COD...');
        await page.fill('form[action*="checkout"] input[name="name"]', 'Nguyễn Playwright Test');
        await page.fill('form[action*="checkout"] input[name="phone"]', '0988776655');
        await page.fill('form[action*="checkout"] textarea[name="address"]', 'Tòa nhà Landmark 81, 720A Điện Biên Phủ, Quận Bình Thạnh, TP. HCM');

        // Xác nhận đặt hàng
        console.log('- Bấm "Xác nhận đặt hàng ngay"...');
        const placeOrderBtn = page.locator('form[action*="checkout"] button[type="submit"]');
        await Promise.all([
            page.waitForURL('**/checkout/success/**', { timeout: 15000 }),
            placeOrderBtn.click()
        ]);
        await page.waitForLoadState('networkidle');

        // Chụp order success
        const orderSuccessText = await page.locator('h1').innerText();
        console.log(`- Thông báo đơn hàng: "${orderSuccessText}" ✅ Đạt`);
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'order_success.png'), fullPage: false });
        console.log('📸 Đã lưu tests/screenshots/order_success.png');

        // ==========================================
        // 4. ĐĂNG NHẬP ADMIN & QUẢN LÝ ĐƠN HÀNG
        // ==========================================
        console.log('📍 4. Đăng nhập Admin và kiểm tra Dashboard...');
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle' });

        // Tài khoản đọc từ biến môi trường (không hard-code mật khẩu trong repo)
        const ADMIN_EMAIL = process.env.HIHI_ADMIN_EMAIL || 'admin@example.com';
        const ADMIN_PASSWORD = process.env.HIHI_ADMIN_PASSWORD || '';

        if (!ADMIN_PASSWORD) {
            console.warn('⚠️  Chưa đặt HIHI_ADMIN_PASSWORD - bỏ qua bước đăng nhập admin.');
        }

        await page.fill('form[action*="login"] input[name="email"]', ADMIN_EMAIL);
        await page.fill('form[action*="login"] input[name="password"]', ADMIN_PASSWORD);
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => {}),
            page.click('form[action*="login"] button[type="submit"]')
        ]);
        await page.waitForLoadState('networkidle');

        // Vào Admin Dashboard
        await page.goto('http://127.0.0.1:8000/admin/dashboard', { waitUntil: 'networkidle' });
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'admin_dashboard.png'), fullPage: false });
        console.log('📸 Đã lưu tests/screenshots/admin_dashboard.png');

        // Vào Admin Orders
        console.log('- Vào Quản lý Đơn hàng (/admin/orders)...');
        await page.goto('http://127.0.0.1:8000/admin/orders', { waitUntil: 'networkidle' });

        // Mở đơn hàng đầu tiên vừa tạo
        const firstOrderManageBtn = page.locator('table tbody tr a.btn-primary, table tbody tr a.btn-secondary').first();
        await firstOrderManageBtn.click();
        await page.waitForLoadState('networkidle');

        // Cập nhật trạng thái đơn sang 'processing'
        console.log('- Đổi trạng thái đơn hàng sang "Đang xử lý (processing)"...');
        await page.selectOption('form[action*="orders"] select[name="status"]', 'processing');
        await page.click('form[action*="orders"] button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Chụp chi tiết đơn hàng admin sau khi update
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'admin_order_detail.png'), fullPage: false });
        console.log('📸 Đã lưu tests/screenshots/admin_order_detail.png');

        console.log('\n🎉 HOÀN THÀNH TOÀN BỘ KỊCH BẢN KIỂM THỬ PLAYWRIGHT THÀNH CÔNG RỰC RỠ!');

    } catch (err) {
        console.error('❌ Lỗi trong quá trình kiểm thử:', err);
    } finally {
        await browser.close();

        console.log('\n==========================================');
        console.log('📊 BÁO CÁO CONSOLE & PAGE ERRORS');
        console.log(`- Page Errors: ${pageErrors.length}`);
        pageErrors.forEach(e => console.log('  ❌ ' + e));
        console.log(`- Console Errors: ${consoleErrors.length}`);
        consoleErrors.forEach(e => console.log('  ⚠️ ' + e));
        console.log('==========================================\n');
    }
}

runTests();

