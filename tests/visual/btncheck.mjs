import { chromium } from 'playwright-core';
const BASE = 'http://localhost/cargo-management/public';
const CHROME = 'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe';
const browser = await chromium.launch({ executablePath: CHROME, headless: true });
const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
const page = await ctx.newPage();
await page.goto(BASE + '/login', { waitUntil: 'networkidle' });
await page.fill('input[name="email"]', 'admin@sl.com');
await page.fill('input[name="password"]', '12345678');
await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle' }), page.click('form[action$="/login"] button[type="submit"]')]);
for (const url of ['reports', 'activity-logs', 'settings', 'shipments']) {
  await page.goto(BASE + '/' + url, { waitUntil: 'networkidle' });
  const data = await page.evaluate(() => {
    const btns = [...document.querySelectorAll('.btn-sm')].filter(b => b.getBoundingClientRect().height > 0);
    const heights = btns.map(b => Math.round(b.getBoundingClientRect().height));
    return { count: heights.length, min: Math.min(...heights), max: Math.max(...heights), under34: heights.filter(h => h < 34).length };
  });
  console.log(url, JSON.stringify(data));
}
await browser.close();