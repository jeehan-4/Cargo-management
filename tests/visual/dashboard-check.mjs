import { chromium } from 'playwright-core';
import fs from 'fs';
import path from 'path';
import os from 'os';

const BASE = 'http://127.0.0.1:8000';
const CHROME = 'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe';
const OUT = 'tests/visual/screenshots/dashboard-check';
fs.mkdirSync(OUT, { recursive: true });
const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'cm-dash-'));

const VIEWPORTS = [
  ['mobile-320', 320, 720], ['mobile-360', 360, 800], ['mobile-375', 375, 812],
  ['mobile-390', 390, 844], ['mobile-430', 430, 932],
  ['tablet-768', 768, 1024], ['tablet-820', 820, 1180], ['tablet-1024', 1024, 768],
  ['desktop-1280', 1280, 800], ['desktop-1366', 1366, 768], ['desktop-1440', 1440, 900],
];

const METRICS = () => {
  const vw = window.innerWidth, de = document.documentElement;
  const pageOverflow = Math.max(0, de.scrollWidth - vw);
  const offenders = [];
  for (const el of document.querySelectorAll('body *')) {
    const cs = getComputedStyle(el);
    if (cs.display === 'none' || cs.visibility === 'hidden') continue;
    if (el.closest('.sidebar:not(.open)') || el.classList.contains('sidebar-overlay')) continue;
    const r = el.getBoundingClientRect();
    if (r.width === 0 || r.height === 0) continue;
    if (r.right > vw + 2 || r.left < -2) {
      let p = el.parentElement, scrollable = false;
      while (p && p !== document.body) {
        const ox = getComputedStyle(p).overflowX;
        if (ox === 'auto' || ox === 'scroll') { scrollable = true; break; }
        p = p.parentElement;
      }
      if (!scrollable) offenders.push({ t: el.tagName, c: (el.className.baseVal ?? el.className ?? '').toString().slice(0, 60), l: Math.round(r.left), r: Math.round(r.right) });
    }
  }
  const tiny = [];
  for (const el of document.querySelectorAll('.btn, button, a.btn')) {
    const r = el.getBoundingClientRect();
    if (r.width > 0 && r.height > 0 && r.height < 32) tiny.push({ c: (el.className || '').toString().slice(0, 40), h: Math.round(r.height) });
  }
  // overlap detection among direct grid/card children
  return { vw, pageOverflow, offenderCount: offenders.length, offenders: offenders.slice(0, 12), tiny: tiny.slice(0, 8), dir: de.dir || document.body.dir, font: getComputedStyle(document.body).fontFamily.slice(0, 40) };
};

const browser = await chromium.launch({ executablePath: CHROME, headless: true });
const loginCtx = await browser.newContext({ viewport: { width: 1366, height: 768 } });
const lp = await loginCtx.newPage();
await lp.goto(BASE + '/login', { waitUntil: 'networkidle' });
await lp.fill('input[name="email"]', 'admin@sl.com');
await lp.fill('input[name="password"]', '12345678');
await Promise.all([lp.waitForNavigation({ waitUntil: 'networkidle' }), lp.click('form[action$="/login"] button[type="submit"]')]);
const stateFile = path.join(tmp, 'admin.json');
await loginCtx.storageState({ path: stateFile });
console.log('LOGIN ->', lp.url());
await loginCtx.close();

const results = [];
for (const [name, width, height] of VIEWPORTS) {
  const ctx = await browser.newContext({ viewport: { width, height }, storageState: stateFile, deviceScaleFactor: 1 });
  const page = await ctx.newPage();
  const errors = [], failed = [];
  page.on('console', m => { if (m.type() === 'error') errors.push(m.text().slice(0, 160)); });
  page.on('pageerror', e => errors.push('PAGEERROR: ' + e.message.slice(0, 160)));
  page.on('response', r => { if (r.status() >= 400) failed.push(r.status() + ' ' + r.url()); });
  const resp = await page.goto(BASE + '/dashboard', { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(500);
  const m = await page.evaluate(METRICS);
  const file = path.join(OUT, `dashboard__${name}.png`);
  await page.screenshot({ path: file, fullPage: true });
  results.push({ viewport: name, width, height, status: resp ? resp.status() : 0, ...m, consoleErrors: errors, failedRequests: failed, screenshot: file });
  const flag = (m.pageOverflow > 0 || m.offenderCount > 0 || m.tiny.length || errors.length) ? ' <<ISSUE' : '';
  console.log(`[${name}] http=${resp ? resp.status() : 0} overflow=${m.pageOverflow} offenders=${m.offenderCount} tiny=${m.tiny.length} errs=${errors.length} dir=${m.dir}${flag}`);
  if (m.offenderCount) console.log('   offenders:', JSON.stringify(m.offenders));
  if (m.tiny.length) console.log('   tiny:', JSON.stringify(m.tiny));
  await ctx.close();
}
await browser.close();
fs.writeFileSync(path.join(OUT, 'results.json'), JSON.stringify(results, null, 2));
const issues = results.filter(r => r.pageOverflow > 0 || r.offenderCount > 0 || r.tiny.length || r.consoleErrors.length || r.status >= 400);
console.log('==== SUMMARY ==== total=' + results.length + ' issues=' + issues.length);