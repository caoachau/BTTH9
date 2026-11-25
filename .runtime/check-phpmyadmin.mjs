import fs from 'node:fs/promises';

const page = await (await fetch('http://127.0.0.1:9229/json/new?about:blank', { method: 'PUT' })).json();
const ws = new WebSocket(page.webSocketDebuggerUrl);
await new Promise((resolve, reject) => { ws.addEventListener('open', resolve, { once: true }); ws.addEventListener('error', reject, { once: true }); });
let nextId = 0;
const pending = new Map();
const errors = [];
const badRequests = [];
ws.addEventListener('message', ({ data }) => {
    const message = JSON.parse(data);
    if (message.id && pending.has(message.id)) {
        const { resolve, reject, timer } = pending.get(message.id);
        clearTimeout(timer); pending.delete(message.id);
        if (message.error) reject(new Error(JSON.stringify(message.error))); else resolve(message.result);
    }
    if (message.method === 'Runtime.exceptionThrown') errors.push(message.params.exceptionDetails.exception?.description ?? message.params.exceptionDetails.text);
    if (message.method === 'Network.responseReceived' && message.params.response.status >= 400) badRequests.push({ url: message.params.response.url, status: message.params.response.status });
});
function send(method, params = {}) {
    return new Promise((resolve, reject) => {
        const id = ++nextId;
        const timer = setTimeout(() => { pending.delete(id); reject(new Error('Timeout: ' + method)); }, 15000);
        pending.set(id, { resolve, reject, timer });
        ws.send(JSON.stringify({ id, method, params }));
    });
}
async function evaluate(expression) {
    const result = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
    if (result.exceptionDetails) throw new Error(result.exceptionDetails.exception?.description ?? result.exceptionDetails.text);
    return result.result.value;
}
async function waitFor(expression, label) {
    for (let attempt = 0; attempt < 40; attempt++) {
        if (await evaluate(expression)) return;
        await new Promise(resolve => setTimeout(resolve, 200));
    }
    throw new Error('Waiting for ' + label + ': ' + await evaluate('document.body.innerText.slice(-1800)'));
}
async function clickRoute(route, table = null, marker = '') {
    const target = await evaluate(`(() => {
        const a = [...document.querySelectorAll('a[href]')].find(a => {
            const u = new URL(a.href, location.href);
            return u.searchParams.get('route') === ${JSON.stringify(route)}
                && (${JSON.stringify(table)} === null || u.searchParams.get('table') === ${JSON.stringify(table)})
                && a.href.includes(${JSON.stringify(marker)})
                && a.getBoundingClientRect().width > 0 && a.getBoundingClientRect().height > 0;
        });
        if (!a) return null;
        a.scrollIntoView({block:'center'});
        const r = a.getBoundingClientRect();
        return {x:r.x+r.width/2,y:r.y+r.height/2,text:a.innerText,href:a.href};
    })()`);
    if (!target) throw new Error('Cannot find link for route ' + route + ': ' + JSON.stringify(await evaluate(`[...document.querySelectorAll('a[href]')].filter(a=>/Edit|Change/.test(a.textContent)).slice(-15).map(a=>({text:a.innerText,href:a.href}))`)));
    await send('Input.dispatchMouseEvent', { type: 'mousePressed', x: target.x, y: target.y, button: 'left', clickCount: 1 });
    await send('Input.dispatchMouseEvent', { type: 'mouseReleased', x: target.x, y: target.y, button: 'left', clickCount: 1 });
}
await send('Page.enable'); await send('Runtime.enable'); await send('Network.enable');
await send('Network.setCacheDisabled', { cacheDisabled: true });
await send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 1000, deviceScaleFactor: 1, mobile: false });
await send('Page.navigate', { url: 'http://localhost/phpmyadmin/index.php?route=/database/structure&server=2&db=online_shop' });
await new Promise(resolve => setTimeout(resolve, 3500));
const interactions = [];
await clickRoute('/sql', 'sach');
await waitFor(`document.body.innerText.includes('Vòng quanh thế giới - Các nước Đông Âu')`, 'Browse books');
interactions.push('Browse sach: OK');
await clickRoute('/table/structure', 'sach');
await waitFor(`document.body.innerText.includes('Move columns') && document.body.innerText.includes('hinhsach')`, 'Structure of sach');
interactions.push('Structure sach: OK');
await clickRoute('/table/sql', 'sach');
await waitFor(`!!document.querySelector('#sqlquery') && !!document.querySelector('#button_submit_query')`, 'SQL editor');
await evaluate(`(() => {
    const query = 'SELECT DATABASE() AS ten_csdl, @@port AS cong_mysql, (SELECT COUNT(*) FROM sach) AS so_sach;';
    const editor = document.querySelector('#sqlqueryform .CodeMirror')?.CodeMirror;
    if (editor) editor.setValue(query);
    document.querySelector('#sqlquery').value = query;
    document.querySelector('#button_submit_query').click();
})()`);
await waitFor(`[...document.querySelectorAll('table')].some(t=>t.innerText.includes('ten_csdl') && t.innerText.includes('3307') && t.innerText.includes('10'))`, 'SQL SELECT result');
interactions.push('Run SQL SELECT: online_shop / 3307 / 10 books: OK');
await clickRoute('/sql', 'loai');
await waitFor(`document.body.innerText.includes('ML001') && document.body.innerText.includes('Phát triển bản thân')`, 'Browse categories');
const editTarget = await evaluate(`(() => {
    const row = [...document.querySelectorAll('tr')].find(r=>r.innerText.includes('ML001') && r.innerText.includes('Công nghệ thông tin'));
    const a = [...row.querySelectorAll('a')].find(a=>a.textContent.trim()==='Edit');
    a.scrollIntoView({block:'center'});
    const r = a.getBoundingClientRect();
    return {x:r.x+r.width/2,y:r.y+r.height/2};
})()`);
await send('Input.dispatchMouseEvent', { type: 'mousePressed', x: editTarget.x, y: editTarget.y, button: 'left', clickCount: 1 });
await send('Input.dispatchMouseEvent', { type: 'mouseReleased', x: editTarget.x, y: editTarget.y, button: 'left', clickCount: 1 });
await waitFor(`[...document.querySelectorAll('input:not([type=hidden]),textarea')].some(e=>e.value==='Công nghệ thông tin' && e.getBoundingClientRect().height > 0)`, 'Edit category form');
interactions.push('Edit ML001 form opens with correct values: OK (no submission)');
console.log(JSON.stringify({
    state: await evaluate(`({url:location.href,title:document.title,jquery:typeof jQuery,messages:typeof Messages,pma:typeof PMA_commonParams,ready:document.readyState,text:document.body.innerText.slice(0,2500),scripts:[...document.scripts].map(s=>s.src).filter(Boolean),dialogs:[...document.querySelectorAll('.modal.show,.ui-dialog')].map(e=>e.innerText)})`),
    interactions, errors, badRequests
}, null, 2));
const screenshot = await send('Page.captureScreenshot', { format: 'png' });
await fs.writeFile(new URL('./phpmyadmin-working.png', import.meta.url), Buffer.from(screenshot.data, 'base64'));
await send('Browser.close');
ws.close();
