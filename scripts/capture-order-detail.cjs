const { chromium } = require('playwright');

(async () => {
  const base = 'http://127.0.0.1:8132';
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

  await page.goto(base + '/admin/orders', { waitUntil: 'networkidle' });
  const link = page.locator('a[href*="/admin/orders/"]').first();
  const href = await link.getAttribute('href');
  await page.goto(href, { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_order_detail_status.png' });

  console.log('cancel_reason field exists:', await page.locator('#cancel_reason').count());
  console.log('admin_note field exists:', await page.locator('#admin_note').count());

  await browser.close();
})();
