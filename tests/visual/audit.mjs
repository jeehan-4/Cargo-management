import { chromium } from 'playwright-core';
import fs from 'fs';
import path from 'path';
import os from 'os';

const BASE = 'http://localhost/cargo-management/public';
const CHROME = 'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe';
const OUT = process.argv[2] || 'tests/visual/screenshots/before';
const ONLY = process.argv[3] ? process.argv[3].split(',') : null;
fs.mkdirSync(OUT, { recursive: true });
const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'cm-auth-'));

const VIEWPORTS = [
  ['mobile-320', 320, 720], ['mobile-360', 360, 800], ['mobile-375', 375, 812],
  ['mobile-390', 390, 844], ['mobile-430', 430, 932],
  ['tablet-768', 768, 1024], ['tablet-820', 820, 1180], ['tablet-1024', 1024, 768],
  ['desktop-1280', 1280, 800], ['desktop-1366', 1366, 768], ['desktop-1440', 1440, 900],
  ['desktop-1920', 1920, 1080],
];
const ADMIN = [
  ['dashboard', 'dashboard'], ['shipments', 'shipments'], ['shipments-create', 'shipments/create'],
  ['shipments-edit', 'shipments/8/edit'], ['shipments-show', 'shipments/4'], ['board', 'shipments/board'],
  ['drivers', 'drivers'], ['drivers-show', 'drivers/2'], ['drivers-create', 'drivers/create'],
  ['drivers-edit', 'drivers/2/edit'], ['trucks', 'trucks'], ['trucks-create', 'trucks/create'],
  ['destinations', 'destinations'], ['destinations-show', 'destinations/30'], ['destinations-create', 'destinations/create'],
  ['reports', 'reports'], ['activity-logs', 'activity-logs'], ['settings', 'settings'],
];
const DRIVER = [
  ['driver-dashboard', 'driver'], ['driver-shipments', 'driver/shipments'],
  ['driver-shipment-show', 'driver/shipments/4'], ['driver-profile', 'driver/profile'],
];
const PUBLIC = [['login', 'login'], ['welcome', '']];

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
      if (!scrollable) offenders.push({ t: el.tagName, c: (el.className.baseVal ?? el.className ?? '').toString().slice(0, 50), l: Math.round(r.left), r: Math.round(r.right) });
    }
  }
  const tiny = [];
  if (vw <= 768) for (const el of document.querySelectorAll('.btn, button, a.btn')) {
    const r = el.getBoundingClientRect();
    if (r.width > 0 && r.height > 0 && r.height < 32) tiny.push({ c: (el.className || '').toString().slice(0, 40), h: Math.round(r.height) });
  }
  return { vw, pageOverflow, offenderCount: offenders.length, offenders: offenders.slice(0, 12), tiny: tiny.slice(0, 8), dir: de.dir || document.body.dir, font: getComputedStyle(document.body).fontFamily.slice(0, 40) };
};

async function login(browser, email, password, file) {
  const ctx = await browser.newContext({ viewport: { width: 1366, height: 768 } });
  const page = await ctx.newPage();
  await page.goto(BASE + '/login', { waitUntil: 'networkidle' });
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle' }), page.click('button[type="submit"]')]);
  await ctx.storageState({ path: file });
  const url = page.url();
  await ctx.close();
  return url;
}

const browser = await chromium.launch({ executablePath: CHROME, headless: true, args: ['--no-sandbox', '--font-render-hinting=none'] });
const adminState = path.join(tmp, 'admin.json');
const driverState = path.join(tmp, 'driver.json');
const adminUrl = await login(browser, 'admin@sl.com', '12345678', adminState);
const driverUrl = await login(browser, 'driver@cargo.test', '12345678', driverState);
console.log('LOGIN admin ->', adminUrl, '| driver ->', driverUrl);

const results = [];
async function shoot(role, stateFile, pages, vp, sizes) {
  const [name, width, height] = vp;
  if (ONLY && !ONLY.includes(name)) return;
  const ctx = await browser.newContext({ viewport: { width, height }, storageState: stateFile, deviceScaleFactor: 1 });
  const page = await ctx.newPage();
  const errors = [], failed = [];
  page.on('console', m => { if (m.type() === 'error') errors.push(m.text().slice(0, 160)); });
  page.on('pageerror', e => errors.push('PAGEERROR: ' + e.message.slice(0, 160)));
  page.on('response', r => { if (r.status() >= 400) failed.push(r.status() + ' ' + r.url().split('/public/')[1]); });
  for (const [slug, url] of pages) {
    errors.length = 0; failed.length = 0;
    const full = BASE + '/' + url;
    let status = 0;
    try {
      const resp = await page.goto(full, { waitUntil: 'networkidle', timeout: 30000 });
      status = resp ? resp.status() : 0;
      await page.waitForTimeout(400);
    } catch (e) { errors.push('NAV: ' + e.message.slice(0, 120)); }
    const m = await page.evaluate(METRICS).catch(() => ({ error: 'eval-failed' }));
    const file = path.join(OUT, `${role}__${slug}__${name}.png`);
    await page.screenshot({ path: file, fullPage: false }).catch(() => {});
    results.push({ role, slug, url, viewport: name, width, height, status, ...m, consoleErrors: [...errors], failedRequests: [...failed], screenshot: file });
    const flag = (m.pageOverflow > 0 || m.offenderCount > 0 || status >= 400 || errors.length) ? ' <<ISSUE' : '';
    console.log(`[${role}/${name}] ${slug} http=${status} overflow=${m.pageOverflow} offenders=${m.offenderCount} tiny=${(m.tiny||[]).length} errs=${errors.length}${flag}`);
  }
  await ctx.close();
}

const publicSizes = [['mobile-375', 375, 812], ['tablet-768', 768, 1024], ['desktop-1440', 1440, 900]];
for (const vp of VIEWPORTS) {
  await shoot('admin', adminState, ADMIN, vp);
  await shoot('driver', driverState, DRIVER, vp);
}
for (const vp of publicSizes) await shoot('public', null, PUBLIC, vp);

await browser.close();
fs.writeFileSync(path.join(OUT, 'results.json'), JSON.stringify(results, null, 2));
const issues = results.filter(r => r.pageOverflow > 0 || r.offenderCount > 0 || r.status >= 400 || (r.consoleErrors || []).length);
console.log('\n==== SUMMARY ====');
console.log('total shots:', results.length, '| shots with issues:', issues.length);
console.log('output:', OUT);
fs.rmSync(tmp, { recursive: true, force: true });
