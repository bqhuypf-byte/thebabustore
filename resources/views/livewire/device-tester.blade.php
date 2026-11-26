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
                <div id="dt-ime-warning" class="dt-ime-warning" role="alert" hidden><strong>Vui lòng tắt UniKey hoặc chuyển bộ gõ sang tiếng Anh.</strong><p>Có dấu hiệu bộ gõ đang ghép chữ tiếng Việt (ví dụ â, đ). Bộ gõ có thể tự gửi Backspace để thay chữ; đây chưa phải dấu hiệu lỗi bàn phím. Tắt bộ gõ rồi đặt lại kết quả để thử lại.</p></div>
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
            <div class="dt-key-dock dt-key-dock-classic">
                <div class="dt-dock-controls"><label><input id="dt-log-visible" type="checkbox" checked> Hiện thông tin phím</label><span>Ctrl + Space: đặt lại</span><div class="dt-key-dock-totals"><span>Đã thử <b id="dt-key-tested">0 / 104</b></span><span>Giữ tối đa <b id="dt-key-max">0</b></span><span>Lần nhấn <b id="dt-key-count">0</b></span></div></div>
                <div class="dt-dock-table" id="dt-dock-table"><table><thead><tr><th>KeyCode web</th><th title="Trình duyệt không cung cấp scan code phần cứng">scanCode</th><th>Flags web</th><th>Phím</th><th title="Khoảng cách giữa hai sự kiện, không phải độ trễ phần cứng">dt(ms)</th></tr></thead><tbody id="dt-key-log"></tbody></table></div>
                <div class="dt-held-counter"><strong id="dt-key-held">000</strong><span>PHÍM ĐÃ NHẬN</span></div>
                <button class="dt-dock-clear" type="button" data-action="keyboard-clear">Xóa<span>Ctrl + Space</span></button>
            </div>
            <div hidden><span id="dt-last-key"></span><span id="dt-event-code"></span><span id="dt-event-number"></span><span id="dt-event-location"></span><span id="dt-event-flags"></span></div>
        </section>
        <section id="dt-mouse" role="tabpanel" aria-labelledby="dt-tab-mouse" hidden>
            <div class="dt-mouse-layout"><section class="dt-auto-results"><div class="dt-auto-heading"><h2>Tự động theo dõi</h2><span>Không cần bấm bắt đầu</span></div><div class="dt-auto-grid"><div class="dt-auto-result" id="dt-auto-click"><h3>Nghi double click</h3><strong id="dt-auto-click-count">0</strong><p id="dt-auto-click-detail"></p></div><div class="dt-auto-result" id="dt-auto-wheel"><h3>Nghi loạn con lăn</h3><strong id="dt-auto-wheel-count">0</strong><p id="dt-auto-wheel-detail"></p></div><div class="dt-auto-result" id="dt-auto-hold"><h3>Giữ & thả nút</h3><strong id="dt-auto-hold-time">—</strong><p id="dt-auto-hold-detail"></p></div></div></section>
                <div class="dt-card">
                    <div class="dt-card-heading"><div><h2>Vùng kiểm tra chuột</h2><p>Di chuột, bấm nút và cuộn trong vùng bên dưới.</p></div></div>
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
                    <div class="dt-scroll-simple"><div><span>Cuộn lên</span><strong id="dt-scroll-up">0</strong></div>
                        <div><span>Cuộn xuống</span><strong id="dt-scroll-down">0</strong></div></div><details class="dt-scroll-details"><summary>Dữ liệu cuộn chi tiết</summary><div class="dt-scroll-readouts" aria-label="Kết quả test con lăn">
                        
                        <div><span>deltaY gần nhất</span><strong id="dt-scroll-delta">—</strong></div>
                        <div><span>Tổng quãng cuộn</span><strong id="dt-scroll-distance">0</strong></div>
                    </div>
                    <p class="dt-scroll-note">Tổng cộng độ lớn mỗi lượt cuộn, tách theo đơn vị trình duyệt: pixel, dòng hoặc trang. Số lượt là số sự kiện wheel, không phải số nấc vật lý.</p></details>
                </div>
                <aside class="dt-card dt-results-card"><h2>Kết quả kiểm tra</h2><p>Dấu tích xác nhận đã nhận tín hiệu.</p><div class="dt-results-heading"><span>CƠ BẢN</span><strong id="dt-results-basic-count">0 / 5</strong></div><div id="dt-results-basic"></div><div class="dt-results-heading"><span>MỞ RỘNG</span><strong id="dt-results-extra-count">0 / 4</strong></div><div id="dt-results-extra"></div></aside>
            </div>
        </section>
        <noscript><p class="dt-measure-note">Bật JavaScript để sử dụng công cụ kiểm tra.</p></noscript>
    </main>
    @livewire('inc.footer')
    <script type="module" src="{{ asset('js/device-tester.js') }}?v={{ filemtime(public_path('js/device-tester.js')) }}"></script>
</div>
