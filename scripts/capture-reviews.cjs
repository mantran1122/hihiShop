const { chromium } = require('playwright');

(async () => {
  const base = 'http://127.0.0.1:8130';
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });
  const page = await ctx.newPage();

  await page.goto(base + '/login', { waitUntil: 'networkidle' });
  await page.fill('#b-form input[name="email"]', 'admin@example.com');
  await page.fill('#b-form input[name="password"]', 'admin123');
  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.locator('#b-form button[type="submit"]').click({ force: true }),
  ]);

  await page.goto(base + '/admin/reviews', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_reviews_list.png' });

  await page.goto(base + '/admin/vouchers/create', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_voucher_form.png' });

  // Ẩn thử 1 đánh giá rồi mở lại để không thay đổi dữ liệu demo
  const before = await page.locator('[data-testid="review-row"]').count();
  console.log('review rows:', before);

  await browser.close();
})();
