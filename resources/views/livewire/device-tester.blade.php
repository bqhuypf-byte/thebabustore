<div class="device-test-page">
    @livewire('inc.header')
    <link rel="stylesheet" href="{{ asset('css/device-tester.css') }}?v={{ filemtime(public_path('css/device-tester.css')) }}">
    <main class="dt-container" id="device-tester" data-key-sound="{{ asset('audio/device-test/key.wav') }}?v={{ filemtime(public_path('audio/device-test/key.wav')) }}" data-bark-sound="{{ asset('audio/device-test/bark.wav') }}" wire:ignore>
        <div class="dt-intro">
            <div><p class="dt-eyebrow">BABU TOOLS</p><h1>Kiểm tra chuột & bàn phím</h1><p>Thử phím bấm, nút chuột và chuyển động ngay trên trình duyệt.</p></div>
            <button class="dt-button dt-secondary" type="button" data-action="reset">Đặt lại kết quả</button>
        </div>
        <p class="dt-mobile-note">Dùng máy tính và thiết bị vật lý để kiểm tra đầy đủ các phím và nút chuột.</p>
        <div class="dt-tabs" role="tablist" aria-label="Thiết bị kiểm tra">
            <button id="dt-tab-keyboard" type="button" role="tab" aria-selected="true" aria-controls="dt-keyboard" data-tab="keyboard">Bàn phím</button>
            <button id="dt-tab-mouse" type="button" role="tab" aria-selected="false" aria-controls="dt-mouse" tabindex="-1" data-tab="mouse">Chuột</button>
        </div>
        <section id="dt-keyboard" role="tabpanel" aria-labelledby="dt-tab-keyboard">
            <div class="dt-card">
                <div class="dt-card-heading">
                    <div><h2>Test bàn phím</h2><p>Nhấn phím để kiểm tra ngay. Bấm ngoài khung để tạm dừng, bấm vào khung để tiếp tục.</p></div>
                    <div class="dt-toolbar">
                        <label class="dt-select-label">Layout <select id="dt-layout"><option value="full">100% · Full-size</option><option value="tkl">80% · TKL</option></select></label>
                        <label class="dt-select-label">Hệ phím <select id="dt-platform"><option value="win">Windows</option><option value="mac">Mac</option></select></label>
                        <button class="dt-button" type="button" data-action="keyboard-toggle">Tạm dừng</button>
                    </div>
                </div>
                <div class="dt-keyboard-options">
                    <label><input id="dt-auto-layout" type="checkbox" checked> Tự nhận diện bố trí chữ</label>
                    <label><input id="dt-key-sound" type="checkbox" checked> Bật âm thanh</label>
                    <label class="dt-select-label">Âm nhấn <select id="dt-sound-type"><option value="bark">Gâu · một tiếng</option><option value="click">Click ngắn</option></select></label>
                    <label><input id="dt-reset-shortcut" type="checkbox" checked> Ctrl + Space đặt lại</label>
                    <label class="dt-select-label">Bố trí chữ <select id="dt-letter-layout"><option value="us">US · QWERTY</option><option value="auto">Theo hệ điều hành</option></select></label>
                </div>
                <p id="dt-layout-status" class="dt-option-note">Đang nhận diện bố trí chữ.</p>
                <p id="dt-sound-status" class="dt-option-note" hidden></p>
                <div class="dt-keyboard-scroll" id="dt-keyboard-stage" tabindex="0" role="region" aria-label="Vùng nhận phím bàn phím">
                    <div class="dt-media-heading"><span>Phím media & trình duyệt</span><span id="dt-media-tested">0 / 18</span></div>
                    <div class="dt-media-row" id="dt-media-visual"></div>
                    <div class="dt-lock-indicators" aria-label="Đèn trạng thái bàn phím"><span data-lock="NumLock">Num <b>—</b></span><span data-lock="CapsLock">Caps <b>—</b></span><span data-lock="ScrollLock">Scroll <b>—</b></span></div>
                    <div class="dt-keyboard" id="dt-keyboard-visual"></div>
                </div>
                <div class="dt-keyboard-footer">
                    <div class="dt-legend"><span><i class="dt-dot dt-dot-pressed"></i>Đang giữ</span><span><i class="dt-dot dt-dot-tested"></i>Đã nhận</span><span><i class="dt-dot"></i>Chưa thử</span></div>
                    <p id="dt-keyboard-status" role="status">Đang nhận phím · Bấm ngoài khung để tạm dừng</p>
                </div>
            </div>
            <div class="dt-stat-grid">
                <div class="dt-stat"><span>Đã nhận trên layout</span><strong id="dt-key-tested">0 / 104</strong></div>
                <div class="dt-stat"><span>Đang giữ</span><strong id="dt-key-held">0</strong></div>
                <div class="dt-stat"><span>Giữ đồng thời nhiều nhất</span><strong id="dt-key-max">0</strong></div>
                <div class="dt-stat"><span>Lần nhấn mới</span><strong id="dt-key-count">0</strong></div>
            </div>
            <div class="dt-card dt-event-card">
                <div><h2>Sự kiện gần nhất</h2><p id="dt-last-key" class="dt-last-key">Chưa nhận phím</p>
                    <dl class="dt-key-details"><div><dt>Code</dt><dd id="dt-event-code">—</dd></div><div><dt>KeyCode web</dt><dd id="dt-event-number">—</dd></div><div><dt>Vị trí</dt><dd id="dt-event-location">—</dd></div><div><dt>Trạng thái</dt><dd id="dt-event-flags">—</dd></div></dl>
                    <p class="dt-muted">Mã trên do trình duyệt cung cấp; scan code Windows không có trong bản web. Một số phím hệ thống/media có thể được hệ điều hành giữ lại.</p></div>
                <div class="dt-event-log" id="dt-key-log" aria-label="Lịch sử sự kiện bàn phím"><p class="dt-empty">Nhấn phím trong vùng test để xem lịch sử.</p></div>
            </div>
        </section>
        <section id="dt-mouse" role="tabpanel" aria-labelledby="dt-tab-mouse" hidden>
            <div class="dt-mouse-layout">
                <div class="dt-card">
                    <div class="dt-card-heading"><div><h2>Test nút & chuyển động</h2><p>Di chuột, bấm nút và cuộn trong vùng bên dưới.</p></div><span id="dt-input-source" class="dt-source"></span></div>
                    <div class="dt-mouse-pad" id="dt-mouse-pad" tabindex="0" role="region" aria-label="Vùng kiểm tra chuột">
                        <canvas id="dt-mouse-canvas" aria-hidden="true"></canvas>
                        <div class="dt-pad-caption">Di chuột để vẽ quỹ đạo<br><span>Bấm hoặc cuộn để kiểm tra</span></div>
                        <span class="dt-pad-position" id="dt-mouse-position">X — · Y —</span>
                    </div>
                    <div class="dt-mouse-buttons" aria-label="Trạng thái nút chuột">
                        <span data-mouse-button="0">Trái <b>0</b></span><span data-mouse-button="2">Phải <b>0</b></span><span data-mouse-button="1">Giữa <b>0</b></span><span data-mouse-button="3" title="Back">Hông 1 <b>0</b></span><span data-mouse-button="4" title="Forward">Hông 2 <b>0</b></span>
                    </div>
                    <div class="dt-mouse-summary"><p class="dt-wheel-status" id="dt-wheel-status" role="status">Con lăn chưa có sự kiện</p><span id="dt-mouse-total">Tổng 0 lượt bấm</span></div>
                </div>
                <aside class="dt-card dt-cps-card">
                    <h2>Tốc độ click</h2><p>Đo nút được chọn, bao gồm cả hai nút hông. Mặc định tính tất cả 5 nút.</p>
                    <label class="dt-select-label" for="dt-cps-button">Nút tính CPS <select id="dt-cps-button"><option value="all">Tất cả 5 nút</option><option value="0">Nút trái</option><option value="2">Nút phải</option><option value="1">Nút giữa</option><option value="3">Hông 1 · Back</option><option value="4">Hông 2 · Forward</option></select></label>
                    <label class="dt-select-label" for="dt-cps-duration">Thời gian <select id="dt-cps-duration"><option value="5">5 giây</option><option value="10">10 giây</option></select></label>
                    <div class="dt-cps-score"><strong id="dt-cps-score">0.00</strong><span>click / giây</span></div>
                    <div class="dt-cps-detail"><span id="dt-cps-count">0 click</span><span id="dt-cps-time">5.0 giây</span></div>
                    <progress id="dt-cps-progress" max="100" value="0" aria-label="Tiến độ đo tốc độ click"></progress>
                    <button class="dt-button" type="button" data-action="cps-start">Bắt đầu đo</button>
                    <p id="dt-cps-status" role="status">Chọn bắt đầu, sau đó bấm nút đã chọn trong vùng test.</p>
                </aside>
            </div>
            <div class="dt-stat-grid dt-mouse-stats">
                <div class="dt-stat"><span>Tần suất sự kiện ước tính</span><strong id="dt-mouse-hz">—</strong><small>Hz · trong 2 giây gần nhất</small></div>
                <div class="dt-stat"><span>Khoảng cách trung bình</span><strong id="dt-mouse-interval">—</strong><small>ms / sự kiện</small></div>
                <div class="dt-stat"><span>Khoảng cách nhỏ nhất</span><strong id="dt-mouse-min">—</strong><small>ms / sự kiện</small></div>
                <div class="dt-stat"><span>Khoảng cách lớn nhất</span><strong id="dt-mouse-max">—</strong><small>ms / sự kiện</small></div>
            </div>
            <p class="dt-measure-note">Tần suất trên là dữ liệu trình duyệt ghi nhận khi di chuột, dùng để tham khảo. Đây không phải phép đo trực tiếp polling rate USB hoặc độ trễ phần cứng như công cụ Windows.</p>
        </section>
        <noscript><p class="dt-measure-note">Bật JavaScript để sử dụng công cụ kiểm tra.</p></noscript>
    </main>
    @livewire('inc.footer')
    <script type="module" src="{{ asset('js/device-tester.js') }}?v={{ filemtime(public_path('js/device-tester.js')) }}"></script>
</div>
