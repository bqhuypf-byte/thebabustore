export function countsForSession(button, selected = 'all') {
    return Number.isInteger(button) && button >= 0 && button <= 4 && (selected === 'all' || button === Number(selected));
}

export function resolveKeyCode(event) {
    if (event.code && event.code !== 'Unidentified') return event.code;
    return mediaAliases[event.key] ?? null;
}

export function summarizeIntervals(timestamps, now, windowMs = 2000) {
    const samples = [];
    for (const time of timestamps) {
        if (!Number.isFinite(time) || time < now - windowMs || time > now) continue;
        if (!samples.length || time > samples[samples.length - 1]) samples.push(time);
    }
    if (samples.length < 10 || samples.at(-1) - samples[0] < 100) return null;
    const intervals = samples.slice(1).map((time, index) => time - samples[index]);
    const mean = (samples.at(-1) - samples[0]) / intervals.length;
    return { hz: 1000 / mean, mean, min: Math.min(...intervals), max: Math.max(...intervals) };
}

export class ClickSession {
    constructor() {
        this.duration = 5;
        this.cancel();
    }
    arm(duration, button = 'all') {
        this.cancel();
        this.button = button;
        this.duration = Number(duration) === 10 ? 10 : 5;
        this.phase = 'armed';
    }
    click(now, button = 0) {
        this.advance(now);
        if (!countsForSession(button, this.button)) return;
        if (this.phase === 'armed') {
            this.phase = 'running';
            this.startedAt = now;
        }
        if (this.phase === 'running') this.count += 1;
    }
    advance(now) {
        if (this.phase === 'running' && now >= this.startedAt + this.duration * 1000) this.phase = 'complete';
    }
    cancel() {
        this.phase = 'idle';
        this.count = 0;
        this.startedAt = null;
    }
    result(now) {
        this.advance(now);
        const elapsed = this.startedAt === null ? 0 : Math.max(0, Math.min(this.duration, (now - this.startedAt) / 1000));
        return {
            phase: this.phase, count: this.count, remaining: this.duration - elapsed,
            progress: elapsed / this.duration * 100,
            cps: this.phase === 'complete' ? this.count / this.duration : this.count / Math.max(1, elapsed),
        };
    }
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
    const keyboardPanel = find('#dt-keyboard');
    const lockOption = find('#dt-keyboard-lock');
    const visual = find('#dt-keyboard-visual');
    const mediaVisual = find('#dt-media-visual');
    const pad = find('#dt-mouse-pad');
    const canvas = find('#dt-mouse-canvas');
    const context = canvas.getContext('2d');
    const session = new ClickSession();
    const buttonCounts = [0, 0, 0, 0, 0];
    const mousePressed = new Set();
    let activeTab = 'keyboard';
    let keyboardRunning = false;
    let keyboardSession = 0;
    let keyboardLocked = false;
    let keyCount = 0;
    let maxHeld = 0;
    let wheelUp = 0;
    let wheelDown = 0;
    let points = [];
    let movementTimes = [];
    let lastMovementTime = -1;
    let padWidth = 1;
    let padHeight = 1;
    let nativeLabels = null;
    let layoutRequest = 0;
    let audioContext = null;
    let audioBuffer = null;
    let audioLoading = null;

    function makeKey(data) {
        const element = document.createElement('kbd');
        element.className = 'dt-key';
        element.dataset.code = data.code;
        element.textContent = data.label;
        element.dataset.defaultLabel = data.label;
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
        mainRows.forEach(row => main.append(makeRow(row)));
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
        renderKeyLabels();
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
        set('#dt-key-held', pressed.size);
        set('#dt-key-max', maxHeld);
        set('#dt-key-count', keyCount);
    }
    function stopKeyboard(message = 'Đã dừng. Chọn bắt đầu để tiếp tục.') {
        keyboardRunning = false;
        keyboardSession += 1;
        releaseKeyboardLock();
        lockOption.disabled = !supportsKeyboardLock;
        pressed.clear();
        stage.classList.remove('is-recording');
        set('[data-action="keyboard-toggle"]', 'Bắt đầu test');
        set('#dt-keyboard-status', message);
        renderKeyboard();
    }
    const supportsKeyboardLock = Boolean(navigator.keyboard?.lock && keyboardPanel.requestFullscreen);
    if (!supportsKeyboardLock) {
        lockOption.checked = false;
        lockOption.disabled = true;
        set('#dt-keyboard-lock-status', 'Trình duyệt này chưa hỗ trợ khóa phím toàn màn hình. Phiên test vẫn được giữ khi tạm mất focus.');
    }
    function releaseKeyboardLock() {
        if (keyboardLocked) navigator.keyboard?.unlock();
        keyboardLocked = false;
        if (document.fullscreenElement === keyboardPanel) document.exitFullscreen().catch(() => {});
    }
    async function requestKeyboardLock(session) {
        if (!lockOption.checked || !supportsKeyboardLock) return;
        set('#dt-keyboard-lock-status', 'Đang yêu cầu toàn màn hình và quyền khóa bàn phím…');
        try {
            await keyboardPanel.requestFullscreen();
            if (!keyboardRunning || session !== keyboardSession) {
                if ((!keyboardRunning || !lockOption.checked) && document.fullscreenElement === keyboardPanel) await document.exitFullscreen();
                return;
            }
            await navigator.keyboard.lock();
            keyboardLocked = true;
            if (!keyboardRunning || session !== keyboardSession) {
                if (!keyboardRunning || !lockOption.checked) releaseKeyboardLock();
                return;
            }
            stage.focus({ preventScroll: true });
            set('#dt-keyboard-lock-status', 'Đã bật khóa phím trong giới hạn trình duyệt. Bấm ngoài khung hoặc Dừng test để kết thúc.');
        } catch {
            if (!keyboardRunning || session !== keyboardSession) return;
            set('#dt-keyboard-lock-status', 'Chưa khóa được bàn phím. Vùng test vẫn bật; phím do Windows giữ có thể mở công cụ hệ thống.');
        }
    }
    listen(document, 'fullscreenchange', () => {
        if (!keyboardRunning || document.fullscreenElement === keyboardPanel) return;
        if (keyboardLocked) navigator.keyboard?.unlock();
        keyboardLocked = false;
        set('#dt-keyboard-lock-status', 'Đã rời toàn màn hình. Kết quả và phiên test vẫn được giữ.');
    });
    function renderKeyLabels() {
        for (const [code, element] of keyElements) {
            const mapped = nativeLabels?.get(code);
            const printable = /^(Key|Digit|Backquote|Minus|Equal|Bracket|Backslash|Semicolon|Quote|Comma|Period|Slash|Intl)/.test(code);
            element.textContent = printable && mapped ? mapped.toLocaleUpperCase() : element.dataset.defaultLabel;
        }
    }
    async function refreshLayout() {
        const request = ++layoutRequest;
        nativeLabels = null;
        if (!find('#dt-auto-layout').checked) {
            find('#dt-letter-layout').value = 'us';
            renderKeyLabels();
            set('#dt-layout-status', 'Bố trí chữ US · QWERTY.');
            return;
        }
        if (!navigator.keyboard?.getLayoutMap) {
            find('#dt-letter-layout').value = 'us';
            renderKeyLabels();
            set('#dt-layout-status', 'Trình duyệt chưa hỗ trợ tự nhận diện; đang dùng US · QWERTY.');
            return;
        }
        try {
            const labels = await navigator.keyboard.getLayoutMap();
            if (request !== layoutRequest || !find('#dt-auto-layout').checked) return;
            nativeLabels = labels;
            find('#dt-letter-layout').value = 'auto';
            renderKeyLabels();
            set('#dt-layout-status', 'Nhãn phím theo bố trí hiện tại của hệ điều hành.');
        } catch {
            if (request !== layoutRequest) return;
            find('#dt-letter-layout').value = 'us';
            renderKeyLabels();
            set('#dt-layout-status', 'Không đọc được bố trí hệ điều hành; đang dùng US · QWERTY.');
        }
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
            audioBuffer = await audioLoading;
            status.textContent = 'Đã bật tiếng nhấn phím từ app gốc.';
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
    function logKey(event, code, action) {
        const log = find('#dt-key-log');
        log.querySelector('.dt-empty')?.remove();
        const row = document.createElement('div');
        row.className = 'dt-event-row';
        const time = document.createElement('time');
        time.textContent = new Date().toLocaleTimeString('vi-VN', { hour12: false });
        const codeLabel = document.createElement('code');
        codeLabel.textContent = code || event.key;
        const type = document.createElement('span');
        type.textContent = action;
        row.append(time, codeLabel, type);
        log.prepend(row);
        while (log.children.length > 30) log.lastElementChild.remove();
        set('#dt-last-key', `${event.key === ' ' ? 'Space' : event.key} · ${code || 'Không có code'} · ${action}`);
    }
    function resetKeyboard() {
        pressed.clear(); tested.clear(); keyCount = 0; maxHeld = 0;
        find('#dt-key-log').replaceChildren();
        set('#dt-last-key', 'Chưa nhận phím');
        for (const field of ['code', 'number', 'location', 'flags']) set(`#dt-event-${field}`, '—');
        renderKeyboard();
    }
    listen(find('[data-action="keyboard-toggle"]'), 'click', () => {
        if (keyboardRunning) return stopKeyboard();
        keyboardRunning = true;
        keyboardSession += 1;
        lockOption.disabled = true;
        requestKeyboardLock(keyboardSession);
        stage.classList.add('is-recording');
        set('[data-action="keyboard-toggle"]', 'Dừng test');
        set('#dt-keyboard-status', 'Đang nhận phím · Bấm ngoài khung để kết thúc');
        stage.focus({ preventScroll: true });
        refreshLayout();
        prepareSound();
    });
    listen(window, 'keydown', event => {
        if (!keyboardRunning || activeTab !== 'keyboard') return;
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
        playKeySound();
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
        if (!hadKeyDown) playKeySound();
        renderKeyboard();
    }, { capture: true });
    listen(document, 'pointerdown', event => {
        if (keyboardRunning && !stage.contains(event.target) && !find('[data-action="keyboard-toggle"]').contains(event.target)) {
            stopKeyboard('Đã kết thúc khi bấm ngoài khung test.');
        }
    }, { capture: true });
    listen(stage, 'pointerdown', () => { if (keyboardRunning) stage.focus({ preventScroll: true }); });
    listen(stage, 'contextmenu', event => { if (keyboardRunning) event.preventDefault(); });
    listen(find('#dt-layout'), 'change', () => { stopKeyboard('Đã đổi layout. Chọn bắt đầu để thử.'); buildKeyboard(); });
    listen(find('#dt-auto-layout'), 'change', refreshLayout);
    listen(find('#dt-letter-layout'), 'change', () => {
        find('#dt-auto-layout').checked = find('#dt-letter-layout').value === 'auto';
        refreshLayout();
    });
    listen(find('#dt-key-sound'), 'change', prepareSound);

    function renderMouseButtons() {
        root.querySelectorAll('[data-mouse-button]').forEach(element => {
            const button = Number(element.dataset.mouseButton);
            element.querySelector('b').textContent = buttonCounts[button];
            element.classList.toggle('is-tested', buttonCounts[button] > 0);
            element.classList.toggle('is-pressed', mousePressed.has(button));
        });
        set('#dt-mouse-total', `Tổng ${buttonCounts.reduce((sum, count) => sum + count, 0)} lượt bấm`);
    }
    function renderCps() {
        const result = session.result(performance.now());
        set('#dt-cps-score', result.cps.toFixed(2));
        set('#dt-cps-count', `${result.count} click`);
        set('#dt-cps-time', `${result.remaining.toFixed(1)} giây`);
        find('#dt-cps-progress').value = result.progress;
        const busy = result.phase === 'armed' || result.phase === 'running';
        find('#dt-cps-duration').disabled = busy;
        find('#dt-cps-button').disabled = busy;
        set('[data-action="cps-start"]', busy ? 'Hủy lần đo' : 'Bắt đầu đo');
        if (result.phase === 'armed') set('#dt-cps-status', 'Sẵn sàng. Bấm nút đã chọn trong vùng test để bắt đầu tính giờ.');
        if (result.phase === 'running') set('#dt-cps-status', 'Đang đo. Tiếp tục bấm nút đã chọn trong vùng test.');
        if (result.phase === 'complete') set('#dt-cps-status', `Hoàn tất ${session.duration} giây · ${result.count} click.`);
    }
    function cancelCps(message) {
        session.cancel();
        renderCps();
        set('#dt-cps-status', message);
    }
    listen(find('[data-action="cps-start"]'), 'click', () => {
        if (session.phase === 'armed' || session.phase === 'running') return cancelCps('Đã hủy. Chọn bắt đầu để đo lại.');
        session.arm(find('#dt-cps-duration').value, find('#dt-cps-button').value);
        renderCps();
        pad.focus({ preventScroll: true });
    });
    listen(find('#dt-cps-duration'), 'change', () => {
        session.duration = Number(find('#dt-cps-duration').value);
        cancelCps('Chọn bắt đầu, sau đó bấm nút đã chọn trong vùng test.');
    });
    listen(find('#dt-cps-button'), 'change', () => cancelCps('Đã đổi nút đo. Chọn bắt đầu để thử.'));
    listen(pad, 'pointerdown', event => {
        if (event.pointerType !== 'mouse') event.preventDefault();
    });
    listen(pad, 'mousedown', event => {
        if (event.button < 0 || event.button > 4) return;
        event.preventDefault();
        pad.focus({ preventScroll: true });
        if (mousePressed.has(event.button)) return;
        mousePressed.add(event.button);
        buttonCounts[event.button] += 1;
        session.click(performance.now(), event.button);
        renderMouseButtons();
        renderCps();
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
    for (const type of ['contextmenu', 'auxclick']) listen(pad, type, event => event.preventDefault());
    listen(pad, 'wheel', event => {
        event.preventDefault();
        if (event.deltaY < 0) wheelUp += 1;
        if (event.deltaY > 0) wheelDown += 1;
        set('#dt-wheel-status', `Cuộn lên ${wheelUp} · Cuộn xuống ${wheelDown}${event.deltaX ? ' · Có cuộn ngang' : ''}`);
    }, { passive: false });

    const movementEvent = window.isSecureContext && 'onpointerrawupdate' in window ? 'pointerrawupdate' : 'pointermove';
    set('#dt-input-source', movementEvent === 'pointerrawupdate' ? 'Pointer raw update' : 'Pointer move');
    listen(pad, movementEvent, event => {
        if (event.pointerType !== 'mouse') return;
        const coalesced = typeof event.getCoalescedEvents === 'function' ? event.getCoalescedEvents() : [];
        const samples = coalesced.length ? coalesced : [event];
        for (const sample of samples) {
            const time = sample.timeStamp > 1e12 ? sample.timeStamp - performance.timeOrigin : sample.timeStamp;
            if (Number.isFinite(time) && time > lastMovementTime) {
                movementTimes.push(time);
                lastMovementTime = time;
            }
        }
        if (movementTimes.length > 20000) movementTimes = movementTimes.slice(-16000);
    });
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
    function pauseInput() {
        if (keyboardRunning) {
            pressed.clear();
            renderKeyboard();
            set('#dt-keyboard-status', 'Vùng test vẫn bật. Quay lại cửa sổ để tiếp tục.');
        }
        mousePressed.clear();
        movementTimes = [];
        lastMovementTime = -1;
        renderMouseButtons();
        if (session.phase === 'armed' || session.phase === 'running') cancelCps('Đã dừng khi rời cửa sổ. Chọn bắt đầu để đo lại.');
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
    listen(find('[data-action="reset"]'), 'click', () => {
        stopKeyboard('Đã xóa kết quả. Chọn bắt đầu để thử lại.');
        resetKeyboard();
        buttonCounts.fill(0); mousePressed.clear(); wheelUp = 0; wheelDown = 0;
        movementTimes = []; lastMovementTime = -1; points = [];
        pad.classList.remove('has-movement');
        set('#dt-mouse-position', 'X — · Y —');
        set('#dt-wheel-status', 'Con lăn chưa có sự kiện');
        cancelCps('Đã xóa kết quả. Chọn bắt đầu để đo.');
        renderMouseButtons();
        renderMovementStats();
        drawTrail();
    });
    function renderMovementStats() {
        const now = performance.now();
        movementTimes = movementTimes.filter(time => time >= now - 2000);
        const stats = summarizeIntervals(movementTimes, now);
        set('#dt-mouse-hz', stats ? Math.round(stats.hz).toLocaleString('vi-VN') : '—');
        set('#dt-mouse-interval', stats ? stats.mean.toFixed(2) : '—');
        set('#dt-mouse-min', stats ? stats.min.toFixed(2) : '—');
        set('#dt-mouse-max', stats ? stats.max.toFixed(2) : '—');
    }
    const timer = window.setInterval(() => {
        if (activeTab !== 'mouse' || document.hidden) return;
        renderCps();
        renderMovementStats();
        drawTrail();
    }, 100);
    listen(window, 'pagehide', event => {
        pauseInput();
        if (event.persisted) return;
        clearInterval(timer);
        observer.disconnect();
        if (keyboardLocked) navigator.keyboard?.unlock();
        audioContext?.close();
        controller.abort();
    });
    buildKeyboard();
}

const root = typeof document === 'undefined' ? null : document.getElementById('device-tester');
if (root) mount(root);
