import { chromium } from 'playwright-core';
import fs from 'node:fs';
const CHROME='C:/Program Files (x86)/Google/Chrome/Application/chrome.exe';
const BASE='http://localhost/cargo-management/public';
const OUT='tests/visual/screenshots/after';
const browser=await chromium.launch({executablePath:CHROME,headless:true,args:['--no-sandbox','--font-render-hinting=none']});
const ctx=await browser.newContext({viewport:{width:375,height:812}});
const page=await ctx.newPage();
await page.goto(BASE+'/login');
await page.fill('input[name="email"]','admin@sl.com');
await page.fill('input[name="password"]','12345678');
await Promise.all([page.waitForNavigation(),page.click('button[type="submit"]')]);
// destinations: scroll to pagination
await page.goto(BASE+'/destinations');
await page.waitForLoadState('networkidle');
await page.locator('.pagination').scrollIntoViewIfNeeded();
await page.screenshot({path:OUT+'/targeted__destinations-pagination__mobile-375.png'});
// drivers-create footer at 320
await page.setViewportSize({width:320,height:720});
await page.goto(BASE+'/drivers/create');
await page.waitForLoadState('networkidle');
await page.locator('.form-actions').scrollIntoViewIfNeeded();
await page.screenshot({path:OUT+'/targeted__drivers-create-footer__mobile-320.png'});
// reports filter at 1024
await page.setViewportSize({width:1024,height:768});
await page.goto(BASE+'/reports');
await page.waitForLoadState('networkidle');
await page.locator('.filter-bar').scrollIntoViewIfNeeded();
await page.screenshot({path:OUT+'/targeted__reports-filter__tablet-1024.png'});
// login pw-toggle at 375
await page.setViewportSize({width:375,height:812});
await page.goto(BASE+'/login');
await page.waitForLoadState('networkidle');
await page.screenshot({path:OUT+'/targeted__login__mobile-375.png'});
await browser.close();
console.log('done');
