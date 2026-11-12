const { chromium } = require('playwright');

(async () => {
  const base = 'http://127.0.0.1:8127';
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });
  const page = await ctx.newPage();

  // Đăng nhập admin
  await page.goto(base + '/login', { waitUntil: 'networkidle' });
  await page.fill('#b-form input[name="email"]', 'admin@example.com');
  await page.fill('#b-form input[name="password"]', 'admin123');
  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.locator('#b-form button[type="submit"]').click({ force: true }),
  ]);

  // Danh sách sản phẩm admin
  await page.goto(base + '/admin/products', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_products_list.png' });

  // Form thêm sản phẩm
  await page.goto(base + '/admin/products/create', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'tests/screenshots/admin_product_form.png', fullPage: true });

  // Tạo thử một sản phẩm
  await page.fill('#name', 'Sản phẩm kiểm thử UI');
  await page.selectOption('#category_id', { index: 1 });
  await page.fill('#price', '456000');
  await page.fill('#stock_qty', '12');
  await page.fill('#short_desc', 'Tạo từ form admin để kiểm thử');
  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.locator('form[action$="/admin/products"] button[type="submit"]').click(),
  ]);

  const bodyText = await page.locator('body').innerText();
  console.log('created visible:', bodyText.includes('Sản phẩm kiểm thử UI'));
  console.log('url:', page.url());

  await page.screenshot({ path: 'tests/screenshots/admin_products_after_create.png' });

  await browser.close();
})();
