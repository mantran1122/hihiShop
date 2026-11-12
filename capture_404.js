import { chromium } from 'playwright';
import path from 'path';

async function capture() {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage({
        viewport: { width: 1280, height: 800 }
    });

    console.log('📸 Đang mở http://127.0.0.1:8000/404 ...');
    await page.goto('http://127.0.0.1:8000/404', { waitUntil: 'networkidle' });
    await page.waitForTimeout(500); // Đợi 1 nhịp animation

    await page.screenshot({ path: path.resolve('tests/screenshots/404_animation.png') });
    await page.screenshot({ path: path.resolve('404_animation.png') });
    console.log('✅ Đã lưu ảnh chụp 404_animation.png thành công!');

    // Test mobile
    await page.setViewportSize({ width: 390, height: 844 });
    await page.waitForTimeout(300);
    await page.screenshot({ path: path.resolve('tests/screenshots/404_animation_mobile.png') });
    await page.screenshot({ path: path.resolve('404_animation_mobile.png') });
    console.log('✅ Đã lưu ảnh chụp 404_animation_mobile.png thành công!');

    await browser.close();
}

capture();

