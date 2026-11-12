import { chromium } from 'playwright';
import path from 'path';

const SCREENSHOT_DIR = path.resolve('tests/screenshots');

async function captureAuth() {
    console.log('📸 Bắt đầu chụp ảnh màn hình Login & Register...');
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage({
        viewport: { width: 1280, height: 800 }
    });

    try {
        // 1. Chụp Login Desktop
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle' });
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'auth_login.png'), fullPage: false });
        console.log('✅ Đã chụp auth_login.png');

        // 2. Chụp Register Desktop
        await page.goto('http://127.0.0.1:8000/register', { waitUntil: 'networkidle' });
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'auth_register.png'), fullPage: false });
        console.log('✅ Đã chụp auth_register.png');

        // 3. Chụp Forgot Password Desktop
        await page.goto('http://127.0.0.1:8000/forgot-password', { waitUntil: 'networkidle' });
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'auth_forgot_password.png'), fullPage: false });
        console.log('✅ Đã chụp auth_forgot_password.png');

    } catch (e) {
        console.error('Lỗi khi chụp:', e);
    } finally {
        await browser.close();
    }
}

captureAuth();

