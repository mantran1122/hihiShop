const { chromium } = require('playwright');

(async () => {
  const base = 'http://127.0.0.1:8123';
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 }, deviceScaleFactor: 1 });
  const page = await ctx.newPage();

  // 1. Khách: khối đánh giá sản phẩm
  await page.goto(base + '/product/nuoc-hoa-citrus-fresh-eau-de-parfum-pMB4S', { waitUntil: 'networkidle' });
  await page.locator('section#reviews').scrollIntoViewIfNeeded();
  await page.waitForTimeout(400);
  await page.screenshot({ path: 'tests/screenshots/product_reviews_viewport.png' });

  // 2. Đăng nhập
  await page.goto(base + '/login', { waitUntil: 'networkidle' });
  await page.fill('#b-form input[name="email"]', 'customer@example.com');
  await page.fill('#b-form input[name="password"]', 'customer123');
  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.locator('#b-form button[type="submit"]').click({ force: true }),
  ]);

  // 3. Sản phẩm Giày: ảnh placeholder + nút tim đã bật
  await page.goto(base + '/product/giay-sneaker-classic-canvas-white-dx9O3', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/product_shoes_wishlist.png' });

  // 4. Trang yêu thích
  await page.goto(base + '/wishlist', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/wishlist_page.png' });

  await browser.close();
  console.log('done');
})();
