const { chromium } = require('playwright');

(async () => {
  const base = 'http://127.0.0.1:8129';
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

  await page.goto(base + '/admin/vouchers', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_vouchers_list.png' });

  await page.goto(base + '/admin/vouchers/create', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_voucher_form.png' });

  // Tạo thử voucher qua form
  await page.fill('#code', 'ui-test20');
  await page.fill('#title', 'Voucher kiểm thử UI');
  await page.selectOption('#category', 'order_discount');
  await page.selectOption('#type', 'fixed');
  await page.fill('#value', '20000');
  await page.fill('#min_order', '150000');
  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.locator('form[action$="/admin/vouchers"] button[type="submit"]').click(),
  ]);

  const body = await page.locator('body').innerText();
  console.log('created visible:', body.includes('UI-TEST20'));
  console.log('url:', page.url());

  await browser.close();
})();
