const { chromium } = require('playwright');

(async () => {
  const base = 'http://127.0.0.1:8128';
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });
  const page = await ctx.newPage();

  // Đăng nhập khách hàng
  await page.goto(base + '/login', { waitUntil: 'networkidle' });
  await page.fill('#b-form input[name="email"]', 'customer@example.com');
  await page.fill('#b-form input[name="password"]', 'customer123');
  await Promise.all([
    page.waitForLoadState('networkidle'),
    page.locator('#b-form button[type="submit"]').click({ force: true }),
  ]);

  // Mở chatbot
  await page.click('#chat-toggle-btn');
  await page.waitForSelector('#chat-window:not(.hidden)');

  // Câu hỏi lần 1 → bot trả lời bằng rule, lưu vào bộ nhớ
  await page.fill('#chat-user-input', 'chính sách giao hàng như thế nào?');
  await page.press('#chat-user-input', 'Enter');
  await page.locator('[data-feedback-box]').last().waitFor({ state: 'visible', timeout: 30000 });
  await page.waitForTimeout(600);

  const firstReply = await page.locator('#chat-messages-container').innerText();
  await page.screenshot({ path: 'tests/screenshots/chatbot_learned_reply.png' });

  // Bấm 👍
  await page.locator('[data-feedback-box]').last().locator('button[data-feedback="thumbs_up"]').click();
  await page.waitForTimeout(900);

  // Hỏi lại y câu đó → phải lấy từ bộ nhớ
  await page.fill('#chat-user-input', 'chính sách giao hàng như thế nào?');
  await page.press('#chat-user-input', 'Enter');
  await page.waitForTimeout(2500);

  const boxes = await page.locator('[data-feedback-box]').count();
  const secondReply = await page.locator('#chat-messages-container').innerText();

  console.log('feedback boxes:', boxes);
  console.log('thanks visible:', (await page.locator('[data-thanks]:not(.hidden)').count()) > 0);
  console.log('reply has policy text:', secondReply.includes('Vận chuyển'));

  await page.screenshot({ path: 'tests/screenshots/chatbot_feedback.png' });

  await browser.close();
})();
