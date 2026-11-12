const { chromium } = require('playwright');

(async () => {
  const base = 'http://127.0.0.1:8131';
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

  await page.goto(base + '/admin/categories', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_categories_list.png' });

  await page.goto(base + '/admin/categories/create', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_category_form.png' });

  await page.goto(base + '/admin/brands', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_brands_list.png' });

  console.log('category page title ok:', (await page.locator('body').innerText()).includes('Danh Sách Thương Hiệu'));
  await browser.close();
})();
