import { chromium } from 'playwright-core';
import fs from 'node:fs';
const CHROME='C:/Program Files (x86)/Google/Chrome/Application/chrome.exe';
const BASE='http://localhost/cargo-management/public';
const results=[];
const log=(name,ok,detail='')=>{results.push({name,ok,detail});console.log((ok?'PASS':'FAIL')+' | '+name+(detail?' | '+detail:''));};
const browser=await chromium.launch({executablePath:CHROME,headless:true,args:['--no-sandbox','--font-render-hinting=none']});
const adminCtx=await browser.newContext({viewport:{width:1366,height:768}});
const admin=await adminCtx.newPage();
const nav=(p,url)=>p.waitForURL(url,{timeout:15000});
const submit=async(p,sel,url)=>{await Promise.all([url?nav(p,url):p.waitForNavigation(),p.click(sel)]);};

// 1) Admin login via UI
await admin.goto(BASE+'/login');
await admin.fill('input[name="email"]','admin@sl.com');
await admin.fill('input[name="password"]','12345678');
await submit(admin,'button[type="submit"]','**/dashboard');
log('admin login -> dashboard',admin.url().endsWith('/dashboard'));

// 2) Sidebar navigation
for(const [path,frag] of [['shipments','shipments'],['shipments/board','board'],['drivers','drivers'],['reports','reports']]){
  await admin.click(`.sidebar a[href$="/${path}"]`);
  await admin.waitForLoadState('networkidle');
  log('nav '+path,admin.url().includes(path));
}

// 3) Mobile offcanvas sidebar open/close
await admin.setViewportSize({width:375,height:812});
await admin.goto(BASE+'/dashboard');
const closedBefore=await admin.locator('#sidebar').evaluate(e=>!e.classList.contains('open'));
await admin.click('#sidebarToggle');
const opened=await admin.locator('#sidebar').evaluate(e=>e.classList.contains('open'));
const overlay=await admin.locator('#sidebarOverlay').evaluate(e=>e.classList.contains('open'));
await admin.click('#sidebarOverlay',{position:{x:10,y:400}});
const closedAfter=await admin.locator('#sidebar').evaluate(e=>!e.classList.contains('open'));
log('offcanvas sidebar open/close',closedBefore&&opened&&overlay&&closedAfter);
await admin.setViewportSize({width:1366,height:768});

// 4) Create shipment via UI
await admin.goto(BASE+'/shipments/create');
await admin.selectOption('select[name="cargo_type"]',{index:1});
await admin.selectOption('select[name="destination_id"]',{index:1});
await admin.fill('input[name="quantity"]','5');
await admin.selectOption('select[name="unit"]',{label:'حاوية'});
await admin.fill('input[name="weight"]','100');
await admin.fill('input[name="origin_port"]','ميناء الحديدة');
await admin.fill('textarea[name="notes"]','شحنة اختبار وظيفي - ستحذف بعد الاختبار');
await submit(admin,'form[action$="/shipments"] button[type="submit"]',/\/shipments\/\d+$/);
const shipId=admin.url().match(/shipments\/(\d+)$/)[1];
const shipUrl=BASE+'/shipments/'+shipId;
const body1=await admin.textContent('body');
log('create shipment',/SHIP-\d{4}-\d+/.test(body1)&&body1.includes('تم إنشاء الشحنة'),'id='+shipId);

// 5) Edit shipment
await admin.goto(shipUrl+'/edit');
await admin.fill('input[name="quantity"]','7');
await submit(admin,'form[action$="/shipments/'+shipId+'"] button[type="submit"]');
await admin.goto(shipUrl+'/edit');
log('edit shipment quantity 5->7',(await admin.inputValue('input[name="quantity"]'))==='7');

// 6) Assign to driver 1 via modal UI
await admin.goto(shipUrl);
await admin.click('button[onclick="openDriverModal()"]');
await admin.check('input[name="driver_id"][value="1"]');
await admin.fill('#reassignForm textarea[name="reason"]','إسناد اختبار وظيفي');
await admin.click('#reassignForm button[type="submit"]');
await submit(admin,'#confirmOkBtn');
let body=await admin.textContent('body');
log('assign shipment to driver',(await admin.locator('.sbadge-assigned').count())>0&&body.includes('محمد السائق')&&body.includes('تم إسناد الشحنة'));

// 7) Unassign via UI + unified confirm modal
await admin.click('form[action$="/unassign"] button');
await submit(admin,'#confirmOkBtn');
body=await admin.textContent('body');
log('unassign releases shipment',body.includes('بانتظار التوزيع'));

// 8) Re-assign then admin status API -> in_transit
await admin.click('button[onclick="openDriverModal()"]');
await admin.check('input[name="driver_id"][value="1"]');
await admin.click('#reassignForm button[type="submit"]');
await submit(admin,'#confirmOkBtn');
const token=await admin.locator('input[name="_token"]').first().getAttribute('value');
const st=await admin.request.post(shipUrl+'/status',{form:{_token:token,status:'in_transit'}});
await admin.goto(shipUrl);
log('admin update status -> in_transit',st.status()<400&&(await admin.locator('.sbadge-in_transit').count())>0,'HTTP '+st.status());
fs.writeFileSync('tests/visual/functional-state.json',JSON.stringify({shipId}));

// 9) Driver login via UI (mobile viewport)
const driverCtx=await browser.newContext({viewport:{width:375,height:812}});
const driver=await driverCtx.newPage();
await driver.goto(BASE+'/login');
await driver.fill('input[name="email"]','driver@cargo.test');
await driver.fill('input[name="password"]','12345678');
await submit(driver,'button[type="submit"]','**/driver');
log('driver login -> driver dashboard',/\/driver$/.test(driver.url()));

// 10) Driver navigation
for(const path of ['driver/shipments','driver/profile']){
  const r=await driver.goto(BASE+'/'+path);
  log('driver nav '+path,r.status()===200);
}

// 11) Driver delivers the test shipment via UI
await driver.goto(BASE+'/driver/shipments/'+shipId);
await submit(driver,'button.btn-clean-success');
body=await driver.textContent('body');
log('driver status update -> delivered',body.includes('تم التسليم'));

// 12) Permissions
const d403a=await driver.request.get(BASE+'/shipments');
const d403b=await driver.request.get(BASE+'/drivers');
const d403c=await driver.request.get(BASE+'/driver/shipments/1');
const a403=await admin.request.get(BASE+'/driver');
log('driver blocked from admin pages (403)',d403a.status()===403&&d403b.status()===403,d403a.status()+'/'+d403b.status());
log('driver blocked from others shipment (403)',d403c.status()===403,String(d403c.status()));
log('admin blocked from driver portal (403)',a403.status()===403,String(a403.status()));
const csrf=await admin.request.post(shipUrl+'/status',{form:{status:'pending'}});
log('CSRF protection (419 without token)',csrf.status()===419,String(csrf.status()));
const anon=await (await browser.newContext()).newPage();
await anon.goto(BASE+'/dashboard');
log('guest redirected to login',anon.url().includes('/login'));

// 13) Admin updates driver status via UI (driver 2: available -> inactive -> available)
await admin.goto(BASE+'/drivers/2/edit');
await admin.selectOption('select[name="status"]','inactive');
await submit(admin,'form[action$="/drivers/2"] button[type="submit"]','**/drivers');
await admin.goto(BASE+'/drivers/2');
body=await admin.textContent('body');
log('driver status -> inactive',body.includes('غير نشط'));
await admin.goto(BASE+'/drivers/2/edit');
await admin.selectOption('select[name="status"]','available');
await submit(admin,'form[action$="/drivers/2"] button[type="submit"]','**/drivers');
await admin.goto(BASE+'/drivers/2');
body=await admin.textContent('body');
log('driver status restored -> available',body.includes('متاح'));

// 14) Cleanup: reset test shipment to pending then delete via UI
await admin.goto(shipUrl);
const tok2=await admin.locator('input[name="_token"]').first().getAttribute('value');
await admin.request.post(shipUrl+'/status',{form:{_token:tok2,status:'pending'}});
await admin.goto(shipUrl);
await admin.click('form[action$="/'+shipId+'"] button[title*="حذف"]');
await submit(admin,'#confirmOkBtn','**/shipments');
const gone=await admin.request.get(shipUrl);
log('test shipment deleted (cleanup)',gone.status()===404,String(gone.status()));

// 15) Driver freed after delivery+reset, logout both portals
await admin.goto(BASE+'/drivers/1');
body=await admin.textContent('body');
log('driver 1 available after lifecycle',body.includes('متاح'));
await admin.goto(BASE+'/dashboard');
await submit(admin,'.sidebar form[action*="logout"] button');
log('admin logout',admin.url()===BASE+'/'||admin.url().includes('/login'),admin.url());
await driver.setViewportSize({width:1280,height:800});
await driver.goto(BASE+'/driver');
await submit(driver,'form[action*="logout"] button');
const afterLogout=await driver.goto(BASE+'/driver');
log('driver logout',afterLogout.url().includes('/login'),driver.url());

await browser.close();
fs.writeFileSync('tests/visual/functional-results.json',JSON.stringify(results,null,2));
const fails=results.filter(r=>!r.ok).length;
console.log(`\nTOTAL: ${results.length} | PASS: ${results.length-fails} | FAIL: ${fails}`);
process.exit(fails?1:0);
