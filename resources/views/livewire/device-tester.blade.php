<div class="device-test-page">
    @livewire('inc.header')
    <link rel="stylesheet" href="{{ asset('css/device-tester.css') }}?v={{ filemtime(public_path('css/device-tester.css')) }}">
    <main class="dt-container" id="device-tester" data-key-sound="{{ asset('audio/device-test/key.wav') }}?v={{ filemtime(public_path('audio/device-test/key.wav')) }}" wire:ignore>
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
                    <label><input id="dt-key-sound" type="checkbox" checked> Bật âm thanh</label>
                    <label><input id="dt-reset-shortcut" type="checkbox" checked> Ctrl + Space đặt lại</label>
                </div>
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
                        <svg class="dt-mouse-svg" viewBox="0 0 240 300" aria-hidden="true">
                            <path id="dt-mouse-body" d="M120 20 C55 20 28 66 28 135 V196 C28 251 61 280 120 280 S212 251 212 196 V135 C212 66 185 20 120 20Z"/>
                            <path id="dt-mouse-left" data-mouse-zone="0" d="M114 28 C62 30 36 68 36 128 V138 H104 V90 Q104 76 114 72Z"/>
                            <path id="dt-mouse-right" data-mouse-zone="2" d="M126 28 C178 30 204 68 204 128 V138 H136 V90 Q136 76 126 72Z"/>
                            <rect id="dt-mouse-wheel" data-mouse-zone="1" x="107" y="78" width="26" height="56" rx="12"/>
                            <path class="dt-wheel-lines" d="M113 91H127 M113 101H127 M113 111H127 M113 121H127"/>
                            <path class="dt-wheel-arrow dt-wheel-up" d="M114 61L120 55L126 61"/>
                            <path class="dt-wheel-arrow dt-wheel-down" d="M114 151L120 157L126 151"/>
                            <rect id="dt-mouse-back" data-mouse-zone="3" x="20" y="145" width="13" height="35" rx="6"/>
                            <rect id="dt-mouse-forward" data-mouse-zone="4" x="20" y="187" width="13" height="35" rx="6"/>
                            <text x="120" y="225" text-anchor="middle">BABU</text>
                        </svg>
                        <div class="dt-mouse-hint">Bấm 5 nút · Cuộn để kiểm tra · Di chuột để vẽ quỹ đạo</div>
                        <span class="dt-pad-position" id="dt-mouse-position">X — · Y —</span>
                    </div>
                    <div class="dt-mouse-buttons" aria-label="Trạng thái nút chuột">
                        <span data-mouse-button="0">Trái <b>0</b></span><span data-mouse-button="2">Phải <b>0</b></span><span data-mouse-button="1">Nút cuộn <b>0</b></span><span data-mouse-button="3" title="Back">Hông 1 <b>0</b></span><span data-mouse-button="4" title="Forward">Hông 2 <b>0</b></span>
                    </div>
                    <div class="dt-mouse-summary"><p class="dt-wheel-status" id="dt-wheel-status" role="status">Con lăn chưa có sự kiện</p><span id="dt-mouse-total">Tổng 0 lượt bấm</span></div>
                    <div class="dt-scroll-readouts" aria-label="Kết quả test con lăn">
                        <div><span>Cuộn lên</span><strong id="dt-scroll-up">0</strong></div>
                        <div><span>Cuộn xuống</span><strong id="dt-scroll-down">0</strong></div>
                        <div><span>deltaY gần nhất</span><strong id="dt-scroll-delta">—</strong></div>
                        <div><span>Tổng quãng cuộn</span><strong id="dt-scroll-distance">0</strong></div>
                    </div>
                    <p class="dt-scroll-note">Tổng cộng độ lớn mỗi lượt cuộn, tách theo đơn vị trình duyệt: pixel, dòng hoặc trang. Số lượt là số sự kiện wheel, không phải số nấc vật lý.</p>
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
            <div class="dt-diagnostic-grid">
                <section class="dt-card dt-diagnostic-card" aria-labelledby="dt-click-check-title">
                    <div class="dt-check-heading"><div><p class="dt-check-eyebrow">BẤM ĐƠN</p><h2 id="dt-click-check-title">Kiểm tra nghi click đúp</h2></div><button type="button" class="dt-button" data-action="click-check">Bắt đầu lượt mới</button></div>
                    <p>Bấm từng lần chậm, cách nhau khoảng 1 giây trong khung chuột. Hai lần nhấn cùng nút quá sát nhau sẽ được đánh dấu để kiểm tra lại.</p>
                    <div class="dt-check-controls">
                        <label class="dt-select-label">Nút thử <select id="dt-check-button"><option value="0">Trái</option><option value="2">Phải</option><option value="1">Nút cuộn</option><option value="3">Hông 1</option><option value="4">Hông 2</option></select></label>
                        <label class="dt-select-label">Ngưỡng cảnh báo <select id="dt-check-threshold"><option value="40">40 ms</option><option value="80" selected>80 ms</option><option value="120">120 ms</option></select></label>
                    </div>
                    <div class="dt-check-metrics"><div><span>Lần nhấn</span><strong id="dt-click-samples">0</strong></div><div><span>Nghi click đúp</span><strong id="dt-click-suspects">0</strong></div><div><span>Khoảng cách gần nhất</span><strong id="dt-click-interval">—</strong></div></div>
                    <p id="dt-click-check-status" role="status">Chọn bắt đầu, rồi bấm từng lần bằng chuột thật.</p>
                    <div id="dt-click-check-log" class="dt-check-log" aria-label="Lịch sử kiểm tra click đúp"></div>
                    <p class="dt-check-note">Ngưỡng là bộ lọc tham khảo, không phải chuẩn kết luận hỏng switch. Bấm nhanh chủ động vẫn có thể bị đánh dấu. Chạm màn hình không được tính trong bài thử này.</p>
                </section>
                <section class="dt-card dt-diagnostic-card" aria-labelledby="dt-wheel-check-title">
                    <div class="dt-check-heading"><div><p class="dt-check-eyebrow">CUỘN MỘT CHIỀU</p><h2 id="dt-wheel-check-title">Kiểm tra tín hiệu cuộn ngược</h2></div><button type="button" class="dt-button" data-action="wheel-check">Bắt đầu lượt mới</button></div>
                    <p>Cuộn đều theo chiều đã chọn trong khung chuột. Tín hiệu ngược chiều được tô màu để bạn đối chiếu với thao tác thực tế.</p>
                    <div class="dt-check-controls"><label class="dt-select-label">Chiều thử <select id="dt-check-direction"><option value="down">Chỉ cuộn xuống</option><option value="up">Chỉ cuộn lên</option></select></label></div>
                    <div class="dt-check-metrics dt-wheel-check-metrics"><div><span>Sự kiện đã nhận</span><strong id="dt-wheel-samples">0</strong></div><div><span>Ngược chiều đã chọn</span><strong id="dt-wheel-reversed">0</strong></div></div>
                    <p id="dt-wheel-check-status" role="status">Chọn chiều và bắt đầu, dùng con lăn chuột thật để thử.</p>
                    <div id="dt-wheel-check-log" class="dt-check-log" aria-label="Lịch sử kiểm tra cuộn ngược"></div>
                    <p class="dt-check-note">Đổi chiều chủ động và touchpad có thể tạo cảnh báo. Bỏ qua thao tác Ctrl + cuộn để thu phóng. Bản web không xác nhận hỏng encoder hay phát hiện chắc chắn nấc cuộn bị mất.</p>
                </section>
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
