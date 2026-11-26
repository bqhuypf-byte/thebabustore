export class ScrollStats {
    constructor() { this.reset(); }
    reset() { this.up = 0; this.down = 0; this.distance = [0, 0, 0]; this.last = null; }
    record(deltaY, deltaMode = 0) {
        if (!Number.isFinite(deltaY) || ![0, 1, 2].includes(deltaMode) || deltaY === 0) return false;
        if (deltaY < 0) this.up += 1;
        else this.down += 1;
        this.distance[deltaMode] += Math.abs(deltaY);
        this.last = { deltaY, deltaMode };
        return true;
    }
}

export class KeyboardImeMonitor {
    constructor() { this.reset(); }
    reset() { this.letters = ''; this.lastLetterAt = -Infinity; this.conversionAt = -Infinity; }
    observe(event, now) {
        const text = String(event.key || event.data || '').normalize('NFC');
        const ime = event.isComposing || event.keyCode === 229 || ['Process', 'Dead'].includes(event.key) || event.type === 'compositionstart' || event.type === 'compositionupdate';
        const vietnamese = /[ĂăĐđÂâÊêÔôƠơƯưẠ-ỹ̀-ͯ]/u.test(text);
        if (ime || vietnamese) return true;
        if (event.type !== 'keydown' || event.repeat || event.ctrlKey || event.altKey || event.metaKey) return false;
        if (event.code === 'Backspace' || event.key === 'Backspace') {
            const recentConversion = now >= this.conversionAt && now - this.conversionAt <= 250;
            this.letters = ''; this.conversionAt = -Infinity;
            return recentConversion;
        }
        if (/^[a-z]$/i.test(text)) {
            this.letters = now - this.lastLetterAt <= 500 ? (this.letters + text.toLowerCase()).slice(-4) : text.toLowerCase();
            this.lastLetterAt = now;
            if (/(aa|aw|ee|oo|ow|uw|dd|[aeiouy][sfrxj])$/.test(this.letters)) this.conversionAt = now;
        } else { this.letters = ''; this.conversionAt = -Infinity; }
        return false;
    }
}

export function resolveKeyCode(event) {
    if (event.code && event.code !== 'Unidentified') return event.code;
    return mediaAliases[event.key] ?? null;
}

const key = (code, label, units = 1) => ({ code, label, units });
const mainRows = [
    [key('Escape', 'Esc'), ...Array.from({ length: 12 }, (_, i) => key(`F${i + 1}`, `F${i + 1}`))],
    [key('Backquote', '` ~'), ...'1234567890'.split('').map(n => key(`Digit${n}`, n)), key('Minus', '- _'), key('Equal', '= +'), key('Backspace', 'Backspace', 2)],
    [key('Tab', 'Tab', 1.5), ...'QWERTYUIOP'.split('').map(n => key(`Key${n}`, n)), key('BracketLeft', '[ {'), key('BracketRight', '] }'), key('Backslash', '\\ |', 1.5)],
    [key('CapsLock', 'Caps Lock', 1.75), ...'ASDFGHJKL'.split('').map(n => key(`Key${n}`, n)), key('Semicolon', '; :'), key('Quote', "' \""), key('Enter', 'Enter', 2.25)],
    [key('ShiftLeft', 'Shift', 2.25), ...'ZXCVBNM'.split('').map(n => key(`Key${n}`, n)), key('Comma', ', <'), key('Period', '. >'), key('Slash', '/ ?'), key('ShiftRight', 'Shift', 2.75)],
    [key('ControlLeft', 'Ctrl', 1.25), key('MetaLeft', 'Win', 1.25), key('AltLeft', 'Alt', 1.25), key('Space', 'Space', 6.25), key('AltRight', 'Alt', 1.25), key('MetaRight', 'Win', 1.25), key('ContextMenu', 'Menu', 1.25), key('ControlRight', 'Ctrl', 1.25)],
];
const navTop = [key('PrintScreen', 'PrtSc'), key('ScrollLock', 'Scroll'), key('Pause', 'Pause')];
const navKeys = [key('Insert', 'Ins'), key('Home', 'Home'), key('PageUp', 'PgUp'), key('Delete', 'Del'), key('End', 'End'), key('PageDown', 'PgDn')];
const arrows = [key('ArrowUp', '↑'), key('ArrowLeft', '←'), key('ArrowDown', '↓'), key('ArrowRight', '→')];
const numpad = [
    ['NumLock', 'Num', 1, 1], ['NumpadDivide', '/', 2, 1], ['NumpadMultiply', '*', 3, 1], ['NumpadSubtract', '-', 4, 1],
    ['Numpad7', '7', 1, 2], ['Numpad8', '8', 2, 2], ['Numpad9', '9', 3, 2], ['NumpadAdd', '+', 4, 2, 1, 2],
    ['Numpad4', '4', 1, 3], ['Numpad5', '5', 2, 3], ['Numpad6', '6', 3, 3],
    ['Numpad1', '1', 1, 4], ['Numpad2', '2', 2, 4], ['Numpad3', '3', 3, 4], ['NumpadEnter', 'Enter', 4, 4, 1, 2],
    ['Numpad0', '0', 1, 5, 2], ['NumpadDecimal', '.', 3, 5],
];

const mediaKeys = [
    key('BrowserBack', '←\nQuay lại'), key('BrowserForward', '→\nTiến tới'), key('BrowserRefresh', '↻\nTải lại'),
    key('BrowserStop', 'Dừng tải'), key('BrowserSearch', 'Tìm kiếm'), key('BrowserFavorites', 'Ưa thích'),
    key('BrowserHome', 'Trang chủ'), key('LaunchApp1', 'Máy tính'), key('LaunchMail', 'Email'), key('LaunchApp2', 'Máy tính số'),
    key('MediaSelect', 'Nhạc'), key('MediaPlayPause', 'Phát / nghỉ'), key('MediaStop', 'Dừng nhạc'),
    key('MediaTrackPrevious', 'Bài trước'), key('MediaTrackNext', 'Bài sau'), key('AudioVolumeMute', 'Tắt tiếng'),
    key('AudioVolumeDown', 'Âm −'), key('AudioVolumeUp', 'Âm +'),
];
const mediaCodes = new Set(mediaKeys.map(item => item.code));
const mediaAliases = Object.fromEntries(mediaKeys.map(item => [item.code, item.code]));
Object.assign(mediaAliases, {
    VolumeMute: 'AudioVolumeMute', VolumeDown: 'AudioVolumeDown', VolumeUp: 'AudioVolumeUp',
    LaunchApplication1: 'LaunchApp1', LaunchApplication2: 'LaunchApp2', LaunchMediaPlayer: 'MediaSelect',
});

function mount(root) {
    if (root.dataset.ready) return;
    root.dataset.ready = 'true';
    const find = selector => root.querySelector(selector);
    const set = (selector, value) => { find(selector).textContent = value; };
    const controller = new AbortController();
    const listen = (target, name, callback, options = {}) => target.addEventListener(name, callback, { ...options, signal: controller.signal });
    const pressed = new Set();
    const tested = new Set();
    const keyElements = new Map();
    const stage = find('#dt-keyboard-stage');
    const visual = find('#dt-keyboard-visual');
    const mediaVisual = find('#dt-media-visual');
    let activeTab = 'keyboard';
    let keyboardRunning = false;
    const imeMonitor = new KeyboardImeMonitor();
    function checkKeyboardIme(event) {
        if (!keyboardRunning || activeTab !== 'keyboard') return;
        if (imeMonitor.observe(event, performance.now())) find('#dt-ime-warning').hidden = false;
    }
    for (const type of ['compositionstart', 'compositionupdate', 'compositionend', 'beforeinput', 'input']) listen(window, type, checkKeyboardIme, { capture: true });
    let keyCount = 0;
    let maxHeld = 0;
    let audioContext = null;
    let audioBuffer = null;
    let audioLoading = null;
    function makeKey(data) {
        const element = document.createElement('kbd');
        element.className = 'dt-key';
        element.dataset.code = data.code;
        const macLabels = { MetaLeft: '⌘ Cmd', MetaRight: '⌘ Cmd', AltLeft: '⌥ Option', AltRight: '⌥ Option', ControlLeft: '⌃ Ctrl', ControlRight: '⌃ Ctrl', Backspace: '⌫ Delete', Enter: 'Return' };
        const label = find('#dt-platform').value === 'mac' ? (macLabels[data.code] || data.label) : data.label;
        element.textContent = label;
        element.dataset.defaultLabel = label;
        element.title = data.code;
        element.style.setProperty('--key-units', data.units);
        keyElements.set(data.code, element);
        return element;
    }
    function makeRow(keys) {
        const row = document.createElement('div');
        row.className = 'dt-key-row';
        keys.forEach(data => row.append(makeKey(data)));
        return row;
    }
    function buildKeyboard() {
        keyElements.clear();
        visual.replaceChildren();
        mediaVisual.replaceChildren();
        mediaKeys.forEach(data => mediaVisual.append(makeKey(data)));
        visual.dataset.layout = find('#dt-layout').value;
        stage.dataset.layout = visual.dataset.layout;
        const main = document.createElement('div');
        main.className = 'dt-key-main';
        mainRows.forEach((row, index) => {
            if (find('#dt-platform').value === 'mac' && index === mainRows.length - 1) {
                row = [key('ControlLeft', 'Ctrl', 1.25), key('AltLeft', 'Option', 1.25), key('MetaLeft', 'Cmd', 1.25), key('Space', 'Space', 6.25), key('MetaRight', 'Cmd', 1.25), key('AltRight', 'Option', 1.25), key('ControlRight', 'Ctrl', 1.25)];
            }
            main.append(makeRow(row));
        });
        const navigation = document.createElement('div');
        navigation.className = 'dt-key-nav';
        navigation.append(makeRow(navTop));
        for (const [className, keys] of [['dt-nav-grid', navKeys], ['dt-arrow-grid', arrows]]) {
            const grid = document.createElement('div');
            grid.className = className;
            keys.forEach(data => grid.append(makeKey(data)));
            navigation.append(grid);
        }
        visual.append(main, navigation);
        if (visual.dataset.layout === 'full') {
            const grid = document.createElement('div');
            grid.className = 'dt-key-numpad';
            for (const [code, label, x, y, width = 1, height = 1] of numpad) {
                const element = makeKey(key(code, label));
                element.style.gridColumn = `${x} / span ${width}`;
                element.style.gridRow = `${y} / span ${height}`;
                grid.append(element);
            }
            visual.append(grid);
        }
        renderKeyboard();
    }
    function renderKeyboard() {
        let visibleTested = 0;
        let mediaTested = 0;
        for (const [code, element] of keyElements) {
            element.classList.toggle('is-tested', tested.has(code));
            element.classList.toggle('is-pressed', pressed.has(code));
            if (tested.has(code)) {
                if (mediaCodes.has(code)) mediaTested += 1;
                else visibleTested += 1;
            }
        }
        set('#dt-key-tested', `${visibleTested} / ${keyElements.size - mediaCodes.size}`);
        set('#dt-media-tested', `${mediaTested} / ${mediaCodes.size}`);
        set('#dt-key-held', String(tested.size).padStart(3, '0'));
        set('#dt-key-max', maxHeld);
        set('#dt-key-count', keyCount);
    }
    function stopKeyboard(message = 'Đã tạm dừng. Bấm vào khung để tiếp tục.') {
        keyboardRunning = false;
        pressed.clear();
        stage.classList.remove('is-recording');
        set('[data-action="keyboard-toggle"]', 'Tiếp tục nhận phím');
        set('#dt-keyboard-status', message);
        renderKeyboard();
    }
    async function prepareSound() {
        const status = find('#dt-sound-status');
        status.hidden = !find('#dt-key-sound').checked;
        if (status.hidden) return;
        status.textContent = 'Đang bật âm thanh…';
        try {
            const Audio = window.AudioContext || window.webkitAudioContext;
            if (!Audio) throw new Error('Audio unavailable');
            audioContext ??= new Audio();
            await audioContext.resume();
            audioLoading ??= fetch(root.dataset.keySound).then(response => {
                if (!response.ok) throw new Error('Sound unavailable');
                return response.arrayBuffer();
            }).then(data => audioContext.decodeAudioData(data));
            const buffer = await audioLoading;
            audioBuffer = buffer;
            status.textContent = 'Đã bật tiếng click ngắn khi nhấn phím.';
        } catch {
            audioLoading = null;
            status.textContent = 'Chưa bật được âm thanh. Thử tắt rồi bật lại tùy chọn.';
        }
    }
    function playKeySound() {
        if (!find('#dt-key-sound').checked || !audioBuffer || audioContext?.state !== 'running') return;
        const source = audioContext.createBufferSource();
        const gain = audioContext.createGain();
        source.buffer = audioBuffer;
        gain.gain.value = 0.5;
        source.connect(gain).connect(audioContext.destination);
        source.onended = () => { source.disconnect(); gain.disconnect(); };
        source.start();
    }
    function updateKeyDetails(event, code, action) {
        set('#dt-event-code', code || event.code || 'Không có');
        set('#dt-event-number', event.keyCode ? `0x${event.keyCode.toString(16).toUpperCase().padStart(2, '0')} (${event.keyCode})` : 'Không có');
        set('#dt-event-location', ['Chuẩn', 'Bên trái', 'Bên phải', 'Numpad'][event.location] || 'Không rõ');
        const flags = [action];
        for (const [property, label] of [['ctrlKey', 'Ctrl'], ['shiftKey', 'Shift'], ['altKey', 'Alt'], ['metaKey', 'Meta']]) {
            if (event[property]) flags.push(label);
        }
        if (event.repeat) flags.push('Lặp');
        set('#dt-event-flags', flags.join(' · '));
        if (typeof event.getModifierState === 'function') {
            root.querySelectorAll('[data-lock]').forEach(element => {
                const on = event.getModifierState(element.dataset.lock);
                element.classList.toggle('is-on', on);
                element.querySelector('b').textContent = on ? 'Bật' : 'Tắt';
            });
        }
    }
    let lastKeyLogTime = null;
    function logKey(event, code, action) {
        const log = find('#dt-key-log');
        const row = document.createElement('tr');
        const flags = [];
        for (const [property,label] of [['ctrlKey','Ctrl'],['shiftKey','Shift'],['altKey','Alt'],['metaKey','Win']]) if (event[property]) flags.push(label);
        if (event.repeat) flags.push('Repeat');
        const dt = lastKeyLogTime === null || event.timeStamp < lastKeyLogTime ? '—' : Math.round(event.timeStamp-lastKeyLogTime);
        lastKeyLogTime = event.timeStamp;
        const arrow = event.type === 'keyup' ? '↑' : '↓';
        for (const value of [event.keyCode ? '0x'+event.keyCode.toString(16).toUpperCase().padStart(2,'0') : '—', '—', flags.join(' + ') || '—', (code || event.key) + ' ' + arrow, dt]) {
            const cell = document.createElement('td'); cell.textContent = value; row.append(cell);
        }
        log.prepend(row);
        while (log.children.length > 30) log.lastElementChild.remove();
        set('#dt-last-key', (code || event.key) + ' ' + arrow);
    }
    function resetKeyboard() {
        lastKeyLogTime = null;
        imeMonitor.reset();find('#dt-ime-warning').hidden = true;
        pressed.clear(); tested.clear(); keyCount = 0; maxHeld = 0;
        find('#dt-key-log').replaceChildren();
        set('#dt-last-key', 'Chưa nhận phím');
        for (const field of ['code', 'number', 'location', 'flags']) set(`#dt-event-${field}`, '—');
        renderKeyboard();
    }
    function startKeyboard() {
        keyboardRunning = true;
        stage.classList.add('is-recording');
        set('[data-action="keyboard-toggle"]', 'Tạm dừng');
        set('#dt-keyboard-status', 'Đang nhận phím · Bấm ngoài khung để tạm dừng');
        stage.focus({ preventScroll: true });
    }
    listen(find('#dt-log-visible'), 'change', () => { find('#dt-dock-table').hidden = !find('#dt-log-visible').checked; });
    listen(find('[data-action="keyboard-clear"]'), 'click', () => { resetKeyboard(); startKeyboard(); });
    listen(find('[data-action="keyboard-toggle"]'), 'click', () => {
        if (keyboardRunning) stopKeyboard();
        else startKeyboard();
    });
    listen(window, 'keydown', event => {
        if (!keyboardRunning || activeTab !== 'keyboard') return;
        checkKeyboardIme(event);
        event.preventDefault();
        event.stopImmediatePropagation();
        const code = resolveKeyCode(event);
        updateKeyDetails(event, code, 'Nhấn');
        if (find('#dt-reset-shortcut').checked && event.ctrlKey && code === 'Space' && !event.repeat) {
            resetKeyboard();
            set('#dt-keyboard-status', 'Đã đặt lại bằng Ctrl + Space. Vùng test vẫn đang bật.');
            return;
        }
        if (!code || event.repeat || pressed.has(code)) return;
        pressed.add(code);
        tested.add(code);
        keyCount += 1;
        maxHeld = Math.max(maxHeld, pressed.size);
        logKey(event, code, 'Nhấn');
        prepareSound().then(playKeySound);
        renderKeyboard();
    }, { capture: true });
    listen(window, 'keyup', event => {
        if (!keyboardRunning || activeTab !== 'keyboard') return;
        event.preventDefault();
        event.stopImmediatePropagation();
        const code = resolveKeyCode(event);
        updateKeyDetails(event, code, 'Thả');
        if (!code) return;
        const hadKeyDown = pressed.delete(code);
        tested.add(code);
        logKey(event, code, hadKeyDown ? 'Thả' : 'Thả (keyup)');
        if (!hadKeyDown) prepareSound().then(playKeySound);
        renderKeyboard();
    }, { capture: true });
    listen(document, 'pointerdown', event => {
        if (keyboardRunning && !stage.contains(event.target) && !find('[data-action="keyboard-toggle"]').contains(event.target)) {
            stopKeyboard('Đã kết thúc khi bấm ngoài khung test.');
        }
    }, { capture: true });
    listen(stage, 'pointerdown', () => startKeyboard());
    listen(stage, 'contextmenu', event => { if (keyboardRunning) event.preventDefault(); });
    listen(find('#dt-layout'), 'change', () => { pressed.clear(); buildKeyboard(); startKeyboard(); });
    listen(find('#dt-platform'), 'change', () => { pressed.clear(); buildKeyboard(); startKeyboard(); });
    listen(find('#dt-key-sound'), 'change', prepareSound);


    const pad = find('#dt-mouse-pad');
    const auto={last:new Map(),held:new Map(),clicks:0,dbl:0,dblByButton:[0,0,0,0,0],wheel:0,jitter:0,lastRelease:null,sign:0,run:0,tick:null,opposite:null};
    let horizontalLeft=0,horizontalRight=0;

    const names=['Trái','Nút cuộn','Phải','Hông 1','Hông 2'];
    const rows=[['basic','left','Chuột trái',()=>buttonCounts[0]],['basic','right','Chuột phải',()=>buttonCounts[2]],['basic','middle','Nút cuộn chuột',()=>buttonCounts[1]],['basic','up','Cuộn lên',()=>scroll.up],['basic','down','Cuộn xuống',()=>scroll.down],['extra','back','Nút quay lại (Back)',()=>buttonCounts[3]],['extra','forward','Nút tiến tới (Forward)',()=>buttonCounts[4]],['extra','tilt-left','Cuộn ngang trái',()=>horizontalLeft],['extra','tilt-right','Cuộn ngang phải',()=>horizontalRight]];
    function renderPreviousResults(){for(const[group,id,label,get]of rows){let row=find('[data-result="'+id+'"]');if(!row){row=document.createElement('div');row.className='dt-result-row';row.dataset.result=id;const icon=document.createElement('span');icon.className='dt-result-icon';const name=document.createElement('span');name.textContent=label;row.append(icon,name,document.createElement('small'));find('#dt-results-'+group).append(row);}const n=get();row.classList.toggle('is-received',n>0);row.querySelector('.dt-result-icon').textContent=n?'✓':'·';row.querySelector('small').textContent=n?'×'+n:group==='extra'?'Chưa nhận':'Chưa thử';}for(const[group,total]of[['basic',5],['extra',4]])set('#dt-results-'+group+'-count',rows.filter(r=>r[0]===group&&r[3]()>0).length+' / '+total);}
    function renderPreviousAuto(){set('#dt-auto-click-count',auto.dbl);const suspected=auto.dblByButton.map((count,button)=>count?names[button]+': '+count+' lần':null).filter(Boolean);set('#dt-auto-click-detail',suspected.length?suspected.join(' · '):auto.clicks+' lần nhấn · Chưa ghi nhận nghi vấn');set('#dt-auto-wheel-count',auto.jitter);set('#dt-auto-wheel-detail',auto.jitter?'Phát hiện '+auto.jitter+' lần cuộn ngược xen kẽ':auto.wheel+' sự kiện · Chưa ghi nhận nghi vấn');const held=[...auto.held].map(([b,t])=>names[b]+' '+Math.max(0,(performance.now()-t)/1000).toFixed(2)+' s');set('#dt-auto-hold-time',held.length?held.join(' / '):auto.lastRelease?auto.lastRelease.duration.toFixed(2)+' s':'—');set('#dt-auto-hold-detail',held.length?'Đang giữ · thả nút để lưu thời gian':auto.lastRelease?'Đã thả nút '+names[auto.lastRelease.button]:'Nhấn và giữ bất kỳ nút nào trong khung.');for(const[id,b]of[['click',auto.dbl>0],['wheel',auto.jitter>0]])find('#dt-auto-'+id).classList.toggle('has-warning',b);}
    function releasePreviousAuto(e){if(auto.held.has(e.button)){auto.lastRelease={button:e.button,duration:(performance.now()-auto.held.get(e.button))/1000};auto.held.delete(e.button);}renderPreviousAuto();}
    listen(window,'mouseup',releasePreviousAuto);
    listen(window,'pointerup',releasePreviousAuto);
    listen(window,'blur',()=>{auto.held.clear();auto.last.clear();auto.sign=0;auto.run=0;auto.opposite=null;renderPreviousAuto();});

    const canvas = find('#dt-mouse-canvas'), context=canvas.getContext('2d');
    const buttonCounts=[0,0,0,0,0],mousePressed=new Set();
    const scroll=new ScrollStats();
    let wheelFlashTimer,points=[],padWidth=1,padHeight=1;
    function renderMouseButtons() {
        renderPreviousResults();
        root.querySelectorAll('[data-mouse-zone]').forEach(zone => zone.classList.toggle('active', mousePressed.has(Number(zone.dataset.mouseZone))));
        root.querySelectorAll('[data-mouse-button]').forEach(element => {
            const button = Number(element.dataset.mouseButton);
            element.querySelector('b').textContent = buttonCounts[button];
            element.classList.toggle('is-tested', buttonCounts[button] > 0);
            element.classList.toggle('is-pressed', mousePressed.has(button));
        });
        set('#dt-mouse-total', `Tổng ${buttonCounts.reduce((sum, count) => sum + count, 0)} lượt bấm`);
    }
    let touchPointer = null;
    listen(pad, 'pointerdown', event => {
        if (event.pointerType === 'mouse') return;
        event.preventDefault();
        if (touchPointer !== null) return;
        touchPointer = event.pointerId;
        pressMouseButton(0, event);
    });
    function releaseTouch(event) {
        if (touchPointer !== event.pointerId) return;
        touchPointer = null;
        mousePressed.delete(0);
        renderMouseButtons();
    }
    listen(window, 'pointerup', releaseTouch, { capture: true });
    listen(window, 'pointercancel', releaseTouch, { capture: true });
    function mouseRipple(event, button) {
        if (!event || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const rect = pad.getBoundingClientRect();
        const ripple = document.createElement('i');
        ripple.className = 'dt-mouse-ripple';
        ripple.dataset.button = button;
        ripple.style.left = `${event.clientX - rect.left}px`;
        ripple.style.top = `${event.clientY - rect.top}px`;
        ripple.setAttribute('aria-hidden', 'true');
        ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
        pad.append(ripple);
        // Bound transient DOM even when animations are interrupted or throttled.
        const ripples = pad.querySelectorAll('.dt-mouse-ripple');
        if (ripples.length > 24) ripples[0].remove();
        window.setTimeout(() => ripple.remove(), 700);
    }
    function pressMouseButton(button,event){
        if(mousePressed.has(button))return;
        mousePressed.add(button);
        const now=performance.now();
        if(event?.type==='mousedown'&&!event.sourceCapabilities?.firesTouchEvents){const prev=auto.last.get(button);if(prev!==undefined&&now-prev<=80){auto.dbl++;auto.dblByButton[button]++;}auto.last.set(button,now);auto.held.set(button,now);auto.clicks++;renderPreviousAuto();}
        mouseRipple(event,button);buttonCounts[button]++;renderMouseButtons();
    }
    listen(pad, 'mousedown', event => {
        if (event.button < 0 || event.button > 4) return;
        event.preventDefault();
        pad.focus({ preventScroll: true });
        pressMouseButton(event.button, event);
    });
    // Side buttons must not trigger browser history while the mouse tester is open.
    for (const type of ['pointerup', 'mousedown', 'mouseup', 'auxclick']) {
        listen(window, type, event => {
            if (activeTab !== 'mouse' || (event.button !== 3 && event.button !== 4)) return;
            event.preventDefault();
            // Let the pad's mousedown handler count the press, then stop site-level shortcuts.
            if (type !== 'mousedown' && type !== 'pointerup') event.stopPropagation();
        }, { capture: true });
    }
    listen(window, 'mouseup', event => {
        if (mousePressed.has(event.button)) event.preventDefault();
        mousePressed.delete(event.button);
        renderMouseButtons();
    }, { capture: true });
    listen(window, 'pointerup', event => { mousePressed.delete(event.button); renderMouseButtons(); }, { capture: true });
    listen(window, 'pointercancel', () => { mousePressed.clear(); renderMouseButtons(); });
    listen(pad, 'mouseleave', () => { mousePressed.clear(); renderMouseButtons(); });
    for (const type of ['contextmenu', 'auxclick']) listen(pad, type, event => event.preventDefault());
    function renderScroll() {
        const units = ['px', 'dòng', 'trang'];
        const format = value => value.toLocaleString('vi-VN', { maximumFractionDigits: 2 });
        set('#dt-wheel-status', scroll.last ? (scroll.last.deltaY < 0 ? '▲ Cuộn lên' : '▼ Cuộn xuống') : 'Con lăn chưa có sự kiện');
        renderPreviousResults();
        set('#dt-scroll-up', scroll.up);
        set('#dt-scroll-down', scroll.down);
        set('#dt-scroll-delta', scroll.last ? `${scroll.last.deltaY} ${units[scroll.last.deltaMode]}` : '—');
        const totals = scroll.distance.map((value, mode) => value ? `${format(value)} ${units[mode]}` : null).filter(Boolean);
        set('#dt-scroll-distance', totals.length ? totals.join(' · ') : '0');
    }
    listen(pad, 'wheel', event => {
        event.preventDefault();
        if(event.ctrlKey)return;
        if(event.deltaX){if(event.deltaX<0)horizontalLeft++;else horizontalRight++;renderPreviousResults();}
        if(event.deltaY){const now=performance.now(),sign=Math.sign(event.deltaY);auto.wheel++;if(auto.tick!==null&&now-auto.tick>250){auto.sign=0;auto.run=0;auto.opposite=null;}if(sign===auto.sign){if(auto.opposite!==null&&now-auto.opposite<=150)auto.jitter++;auto.opposite=null;auto.run++;}else if(auto.run>=3&&auto.opposite===null){auto.opposite=now;}else{auto.sign=sign;auto.run=1;auto.opposite=null;}auto.tick=now;renderPreviousAuto();}

        if (!scroll.record(event.deltaY, event.deltaMode)) return;
        renderScroll();
        pad.dataset.scrollDirection = event.deltaY < 0 ? 'up' : 'down';
        const wheel = find('#dt-mouse-wheel');
        wheel.classList.add('is-scrolling');
        clearTimeout(wheelFlashTimer);
        wheelFlashTimer = window.setTimeout(() => wheel.classList.remove('is-scrolling'), 150);
    }, { passive: false });

    listen(pad, 'pointermove', event => {
        if (event.pointerType !== 'mouse') return;
        const rect = pad.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width;
        const y = (event.clientY - rect.top) / rect.height;
        points.push({ x, y });
        if (points.length > 500) points.shift();
        pad.classList.add('has-movement');
        set('#dt-mouse-position', `X ${Math.round(x * rect.width)} · Y ${Math.round(y * rect.height)}`);
    });
    function resizeCanvas() {
        const rect = pad.getBoundingClientRect();
        if (!rect.width || !rect.height) return;
        const scale = Math.min(window.devicePixelRatio || 1, 2);
        padWidth = rect.width;
        padHeight = rect.height;
        canvas.width = Math.round(padWidth * scale);
        canvas.height = Math.round(padHeight * scale);
        context?.setTransform(scale, 0, 0, scale, 0, 0);
    }
    function drawTrail() {
        if (!context) return;
        context.clearRect(0, 0, padWidth, padHeight);
        if (!points.length) return;
        context.beginPath();
        context.moveTo(points[0].x * padWidth, points[0].y * padHeight);
        for (const point of points.slice(1)) context.lineTo(point.x * padWidth, point.y * padHeight);
        context.strokeStyle = '#8498ed';
        context.lineWidth = 1.5;
        context.stroke();
        const last = points.at(-1);
        context.beginPath();
        context.arc(last.x * padWidth, last.y * padHeight, 4, 0, Math.PI * 2);
        context.fillStyle = '#182abb';
        context.fill();
    }
    const observer = new ResizeObserver(resizeCanvas);
    observer.observe(pad);
    function pauseInput(){
        auto.held.clear();renderPreviousAuto();
        if(keyboardRunning){pressed.clear();renderKeyboard();set('#dt-keyboard-status','Vùng test vẫn bật. Quay lại cửa sổ để tiếp tục.');}
        mousePressed.clear();touchPointer=null;renderMouseButtons();
    }
    listen(window, 'blur', pauseInput);
    listen(window, 'focus', () => {
        if (!keyboardRunning || activeTab !== 'keyboard') return;
        stage.focus({ preventScroll: true });
        set('#dt-keyboard-status', 'Đã trở lại vùng test · Bấm ngoài khung để kết thúc');
    });
    listen(document, 'visibilitychange', () => {
        if (document.hidden) pauseInput();
        else if (keyboardRunning && activeTab === 'keyboard') stage.focus({ preventScroll: true });
    });
    function switchTab(tab) {
        stopKeyboard('Sẵn sàng kiểm tra');
        pauseInput();
        activeTab = tab;
        for (const name of ['keyboard', 'mouse']) {
            find(`#dt-${name}`).hidden = name !== tab;
            const button = find(`[data-tab="${name}"]`);
            button.setAttribute('aria-selected', String(name === tab));
            button.tabIndex = name === tab ? 0 : -1;
        }
        if (tab === 'keyboard') startKeyboard();
        resizeCanvas();
    }
    root.querySelectorAll('[data-tab]').forEach(button => {
        listen(button, 'click', () => switchTab(button.dataset.tab));
        listen(button, 'keydown', event => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            const target = event.key === 'Home' ? 'keyboard' : event.key === 'End' ? 'mouse' : activeTab === 'keyboard' ? 'mouse' : 'keyboard';
            switchTab(target);
            find(`[data-tab="${target}"]`).focus();
        });
    });
    listen(find('[data-action="reset"]'),'click',()=>{
        stopKeyboard('Đã xóa kết quả. Bấm vào khung để tiếp tục.');resetKeyboard();
        auto.last.clear();auto.held.clear();auto.clicks=auto.dbl=auto.wheel=auto.jitter=0;auto.dblByButton.fill(0);auto.lastRelease=null;auto.sign=auto.run=0;auto.tick=auto.opposite=null;horizontalLeft=horizontalRight=0;
        buttonCounts.fill(0);mousePressed.clear();touchPointer=null;scroll.reset();points=[];delete pad.dataset.scrollDirection;clearTimeout(wheelFlashTimer);find('#dt-mouse-wheel').classList.remove('is-scrolling');pad.classList.remove('has-movement');set('#dt-mouse-position','X — · Y —');renderPreviousAuto();renderMouseButtons();renderScroll();drawTrail();if(activeTab==='keyboard')startKeyboard();
    });
    const timer = window.setInterval(() => {
        if (activeTab !== 'mouse' || document.hidden) return;
        renderPreviousAuto();
        drawTrail();
    }, 100);
    listen(window, 'pagehide', event => {
        pauseInput();
        if (event.persisted) return;
        clearInterval(timer);
        clearTimeout(wheelFlashTimer);
        observer.disconnect();
        audioContext?.close();
        controller.abort();
    });
    renderPreviousResults();renderPreviousAuto();
    find('#dt-platform').value = /mac/i.test(navigator.userAgentData?.platform || navigator.platform) ? 'mac' : 'win';
    buildKeyboard();
    startKeyboard();
}

const root = typeof document === 'undefined' ? null : document.getElementById('device-tester');
if (root) mount(root);
