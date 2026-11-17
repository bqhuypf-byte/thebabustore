<div class="wl-page">
    @php $warrantyPageTitle = match($activeTab) { 'orders' => 'Tra Cứu Tiến Trình Đơn Hàng', 'activate' => 'Kích Hoạt Bảo Hành Điện Tử', default => 'Tra Cứu Tình Trạng Bảo Hành' }; @endphp
    <title>{{ $warrantyPageTitle }} | The BABU Store</title>
    @livewire('inc.header')

    <link rel="stylesheet" href="{{ asset('css/warranty-workflow.css') }}?v={{ filemtime(public_path('css/warranty-workflow.css')) }}">
    <main class="wl-container">
        {{-- BREADCRUMB --}}
        <nav class="wl-breadcrumb" aria-label="Breadcrumb">
            <a href="/">Trang Chủ</a>
            <span class="wl-sep">/</span>
            <span class="wl-cur">
                @if ($activeTab === 'orders')
                    Tra Cứu Đơn Hàng
                @elseif ($activeTab === 'activate')
                    Kích Hoạt Bảo Hành
                @else
                    Tra Cứu Tình Trạng Bảo Hành Sản Phẩm
                @endif
            </span>
        </nav>

        {{-- TOP 3 TABS (CHUYỂN TAB TRỰC TIẾP TẠI CHỖ, KHÔNG CHUYỂN TRANG) --}}
        <div class="wl-top-tabs-wrap">
            <div class="wl-top-tabs" role="tablist">
                {{-- TAB 1: TRA CỨU BẢO HÀNH --}}
                <a
                    href="/tra-cuu-bao-hanh"
                    wire:click.prevent="setTab('warranty')"
                    class="wl-top-tab {{ $activeTab === 'warranty' ? 'is-active' : '' }}"
                    role="tab"
                    aria-selected="{{ $activeTab === 'warranty' ? 'true' : 'false' }}"
                    style="text-decoration: none !important;"
                >
                    <div class="tab-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                            <circle cx="12" cy="15" r="2"/>
                        </svg>
                    </div>
                    <span class="tab-label" style="text-decoration: none !important;">Tra cứu thời gian bảo hành</span>
                </a>

                {{-- TAB 2: TRA CỨU ĐƠN HÀNG --}}
                <a
                    href="/orders"
                    wire:click.prevent="setTab('orders')"
                    class="wl-top-tab {{ $activeTab === 'orders' ? 'is-active' : '' }}"
                    role="tab"
                    aria-selected="{{ $activeTab === 'orders' ? 'true' : 'false' }}"
                    style="text-decoration: none !important;"
                >
                    <div class="tab-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <circle cx="10" cy="9" r="1"/>
                        </svg>
                    </div>
                    <span class="tab-label" style="text-decoration: none !important;">Tra cứu đơn hàng</span>
                </a>

                {{-- TAB 3: KÍCH HOẠT BẢO HÀNH (ICON SHIELD CHECKMARK MỚI) --}}
                <a
                    href="/kich-hoat"
                    wire:click.prevent="setTab('activate')"
                    class="wl-top-tab {{ $activeTab === 'activate' ? 'is-active' : '' }}"
                    role="tab"
                    aria-selected="{{ $activeTab === 'activate' ? 'true' : 'false' }}"
                    style="text-decoration: none !important;"
                >
                    <div class="tab-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10"/>
                        </svg>
                    </div>
                    <span class="tab-label" style="text-decoration: none !important;">Kích hoạt bảo hành</span>
                </a>
            </div>
        </div>

        {{-- ================= TAB 1: TRA CỨU THỜI GIAN BẢO HÀNH ================= --}}
        @if ($activeTab === 'warranty')
            <section class="wl-center-card">
                <h1 class="wl-main-title">TRA CỨU THỜI GIAN BẢO HÀNH</h1>
                <p class="wl-main-sub">
                    Vui lòng nhập số điện thoại hoặc mã đơn hàng của sản phẩm, sau đó ấn "Kiểm tra" để kiểm tra thông tin bảo hành.<br>
                    Nếu bạn gặp khó khăn trong quá trình tra cứu bảo hành vui lòng liên hệ chúng tôi qua hotline:
                    <a href="tel:{{ preg_replace('/\D+/', '', $settings?->hotline ?: '0355198659') }}" class="wl-hl">{{ $settings?->hotline ?: '0355198659' }}</a>
                    hoặc email
                    <a href="mailto:{{ $settings?->email ?: 'support@thebabustore.vn' }}" class="wl-hl">{{ $settings?->email ?: 'support@thebabustore.vn' }}</a>
                </p>

                {{-- RADIO OPTIONS --}}
                <div class="wl-radio-group">
                    <label class="wl-radio-pill {{ $searchType === 'phone' ? 'is-selected' : '' }}">
                        <input type="radio" name="searchType" value="phone" wire:click="setType('phone')" {{ $searchType === 'phone' ? 'checked' : '' }}>
                        <span class="radio-dot"></span>
                        <span>Theo số điện thoại</span>
                    </label>

                    <label class="wl-radio-pill {{ $searchType === 'code' ? 'is-selected' : '' }}">
                        <input type="radio" name="searchType" value="code" wire:click="setType('code')" {{ $searchType === 'code' ? 'checked' : '' }}>
                        <span class="radio-dot"></span>
                        <span>Theo mã đơn hàng / SKU</span>
                    </label>
                </div>

                {{-- SEARCH ROW --}}
                <form wire:submit.prevent="checkWarranty" class="wl-search-row">
                    <div class="wl-input-wrap">
                        <input
                            type="text"
                            wire:model="searchInput"
                            placeholder="{{ $searchType === 'phone' ? 'Nhập số điện thoại (Ví dụ: 0355198659)' : 'Nhập mã đơn hàng hoặc SKU (Ví dụ: WEB033958537358 hoặc A5D9939)' }}"
                            autocomplete="off"
                            autofocus
                        >
                        @if (trim($searchInput) !== '')
                            <button type="button" class="wl-input-clear" wire:click="clearQuery" aria-label="Xóa">×</button>
                        @endif
                    </div>

                    <button type="submit" class="wl-search-submit-btn" wire:loading.attr="disabled">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <span wire:loading.remove>Kiểm tra</span>
                        <span wire:loading>Đang tìm...</span>
                    </button>
                </form>

                <div class="wl-example-note">
                    Ví dụ: <code>0355198659</code>, <code>BH26110914105528</code>, hoặc <code>A5D9939</code>
                </div>

                {{-- RESULTS --}}
                @if ($hasWarrantyQuery)
                    <div class="wl-results-section">
                        @if ($warrantyItems->isEmpty())
                            <div class="wl-no-results">
                                <p>Không tìm thấy thông tin bảo hành nào cho <b>"{{ $query }}"</b>.</p>
                                <span>Vui lòng kiểm tra lại số điện thoại hoặc mã đơn hàng đã nhập.</span>
                            </div>
                        @else
                            <div class="wl-results-head">
                                <span>Kết quả tìm kiếm cho: <b>{{ $query }}</b> ({{ $warrantyItems->count() }} sản phẩm)</span>
                            </div>

                            <div class="wl-cards-list">
                                @foreach ($warrantyItems as $item)
                                    @php
                                        $v = $item->product_variant;
                                        $p = $v?->product;
                                        $order = $item->order;
                                        $thumb = $v?->image ?: (($p?->images ?: [])[0] ?? null);
                                        $statusInfo = \App\Livewire\WarrantyLookup::getWarrantyStatus($item);
                                        $phone = trim((string) ($order?->address?->phone ?? ''));
                                        $isPhoneValid = strlen($phone) >= 9 && !preg_match('/^0+$/', $phone);
                                        $maskedPhone = $isPhoneValid
                                            ? (strlen($phone) > 6 ? substr($phone, 0, 3) . '***' . substr($phone, -3) : $phone)
                                            : 'Chưa cập nhật SĐT';
                                        $customerName = trim((string) ($order?->address?->full_name ?? ''));
                                        if (str_contains(mb_strtolower($customerName), 'chờ kích hoạt')) {
                                            $customerName = 'Khách hàng';
                                        }
                                    @endphp
                                    <article class="wl-item-card">
                                        <div class="wl-thumb-box">
                                            @if ($thumb)
                                                <img src="{{ asset('storage/' . ltrim($thumb, '/')) }}" alt="{{ $p?->name }}">
                                            @else
                                                <span class="wl-thumb-fallback">BABU</span>
                                            @endif
                                        </div>

                                        <div class="wl-item-body">
                                            <h3 class="wl-item-title">{{ $p?->name ?? 'Sản phẩm The BABU Store' }}</h3>

                                            <div class="wl-spec-row">
                                                @if ($v?->sku)
                                                    <span class="wl-spec-badge sku">SKU: {{ $v->sku }}</span>
                                                @endif
                                                @if ($v?->name)
                                                    <span class="wl-spec-badge variant">Loại: {{ $v->name }}</span>
                                                @endif
                                                <span class="wl-spec-badge qty">SL: ×{{ $item->quantity }}</span>
                                            </div>

                                            <div class="wl-item-meta">
                                                <span>Mã đơn: <b class="wl-code">#{{ $order?->order_code }}</b></span>
                                                <span class="wl-dot">•</span>
                                                <span>Ngày mua: {{ optional($order?->created_at)->format('d/m/Y') }}</span>
                                                @if ($customerName && $customerName !== 'Khách hàng')
                                                    <span class="wl-dot">•</span>
                                                    <span>Khách hàng: <b>{{ $customerName }}</b> ({{ $maskedPhone }})</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="wl-item-status-col">
                                            @if ($statusInfo['state'] === 'pending')
                                                <div class="wl-status-badge pending">
                                                    <span class="badge-dot"></span>
                                                    <span>Chưa kích hoạt</span>
                                                </div>
                                                <div class="wl-status-sub">Cần kích hoạt thông tin sở hữu</div>
                                                <button
                                                    type="button"
                                                    wire:click="setTab('activate'); $set('activateCode', '{{ $order?->order_code }}'); searchActivationCode()"
                                                    class="wl-activate-btn"
                                                >
                                                    <span>Kích hoạt ngay</span>
                                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="9 18 15 12 9 6"/>
                                                    </svg>
                                                </button>
                                            @elseif ($statusInfo['state'] === 'active')
                                                <div class="wl-status-badge active">
                                                    <span class="badge-dot"></span>
                                                    <span>Đang bảo hành</span>
                                                </div>
                                                <div class="wl-status-date">
                                                    Hạn đến: <b>{{ $statusInfo['end_formatted'] }}</b>
                                                </div>
                                                <div class="wl-status-sub is-positive">
                                                    Còn {{ $statusInfo['days_left'] }} ngày hiệu lực
                                                </div>
                                            @else
                                                <div class="wl-status-badge expired">
                                                    <span class="badge-dot"></span>
                                                    <span>Hết hạn bảo hành</span>
                                                </div>
                                                <div class="wl-status-date">
                                                    Hết hạn: {{ $statusInfo['end_formatted'] }}
                                                </div>
                                                <div class="wl-status-sub">Đã hết thời hạn bảo hành</div>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div style="margin-top: 24px; padding: 24px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; text-align: center;">
                        <p style="margin: 0 0 6px; font-size: 14.5px !important; font-weight: 700 !important; color: #0f172a;">Nhập Số điện thoại hoặc Mã đơn hàng để kiểm tra thông tin bảo hành</p>
                        <span style="font-size: 13px !important; color: #64748b;">Hoặc <a href="/account/login" style="color: var(--wl-brand); font-weight: 700; text-decoration: underline;">Đăng nhập tài khoản</a> để quản lý tự động toàn bộ sản phẩm bạn sở hữu.</span>
                    </div>
                @endif
            </section>

        {{-- ================= TAB 2: TRA CỨU ĐƠN HÀNG ================= --}}
        @elseif ($activeTab === 'orders')
            <section class="wl-center-card">
                <h1 class="wl-main-title">TRA CỨU TIẾN TRÌNH ĐƠN HÀNG</h1>
                <p class="wl-main-sub">
                    Nhập mã đơn hàng (#WEB...) hoặc số điện thoại đặt hàng để theo dõi tiến độ vận chuyển và lộ trình giao nhận hàng.
                </p>

                <form wire:submit.prevent="checkOrders" class="wl-search-row">
                    <div class="wl-input-wrap">
                        <input
                            type="text"
                            wire:model="orderSearchInput"
                            placeholder="Nhập mã đơn hàng (#WEB...) hoặc số điện thoại..."
                            autocomplete="off"
                        >
                        @if (trim($orderSearchInput) !== '')
                            <button type="button" class="wl-input-clear" wire:click="clearOrderSearch" aria-label="Xóa">×</button>
                        @endif
                    </div>

                    <button type="submit" class="wl-search-submit-btn" wire:loading.attr="disabled">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <span wire:loading.remove>Tra cứu</span>
                        <span wire:loading>Đang tìm...</span>
                    </button>
                </form>

                <div class="wl-example-note">
                    Ví dụ: <code>WEB26110914462221</code> hoặc <code>0355198659</code>
                </div>

                {{-- BỘ LỌC TRẠNG THÁI --}}
                @if ($hasOrderQuery || Auth::check())
                    <div class="wl-order-filter-bar">
                        @php
                            $tabs = [
                                'all' => 'Tất cả',
                                'pending' => 'Chờ xác nhận',
                                'shipping' => 'Đang vận chuyển',
                                'completed' => 'Đã giao thành công',
                                'cancelled' => 'Đã hủy',
                            ];
                        @endphp
                        @foreach ($tabs as $key => $label)
                            <button
                                type="button"
                                class="wl-filter-pill {{ $orderStatusFilter === $key ? 'is-active' : '' }}"
                                wire:click="setOrderStatusFilter('{{ $key }}')"
                            >
                                <span>{{ $label }}</span>
                                <b class="pill-count">{{ $orderCounts[$key] ?? 0 }}</b>
                            </button>
                        @endforeach
                    </div>

                    <div class="wl-results-section" style="margin-top: 18px; border-top: 0; padding-top: 0;">
                        @if ($orders->isEmpty())
                            <div class="wl-no-results">
                                <p>Không tìm thấy đơn hàng nào phù hợp.</p>
                                <span>Vui lòng kiểm tra lại mã đơn hàng hoặc số điện thoại.</span>
                            </div>
                        @else
                            <div class="wl-cards-list">
                                @foreach ($orders as $order)
                                    @php
                                        $group = \App\Livewire\WarrantyLookup::normalizeStatusGroup($order->status);
                                        $st = strtolower((string) $order->status);
                                        $stMeta = match ($group) {
                                            'shipping' => ['label' => 'Đang vận chuyển', 'class' => 'is-shipping', 'step' => 3],
                                            'completed' => ['label' => 'Đã giao thành công', 'class' => 'is-completed', 'step' => 4],
                                            'cancelled' => ['label' => 'Đã hủy', 'class' => 'is-cancelled', 'step' => 0],
                                            default => $st === 'processing'
                                                ? ['label' => 'Xác nhận & Đóng gói', 'class' => 'is-processing', 'step' => 2]
                                                : ['label' => 'Chờ xác nhận', 'class' => 'is-pending', 'step' => 1],
                                        };
                                        $stepNum = $stMeta['step'];
                                    @endphp
                                    <article class="wl-order-card">
                                        <div class="wl-order-head">
                                            <div>
                                                <span>Mã đơn: <b class="wl-code">#{{ $order->order_code }}</b></span>
                                                <span class="wl-dot">•</span>
                                                <span>Ngày đặt: {{ optional($order->created_at)->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <span class="wl-status-pill {{ $stMeta['class'] }}">{{ $stMeta['label'] }}</span>
                                        </div>

                                        {{-- 4-STEP TIMELINE STEPPER --}}
                                        @if ($stepNum > 0)
                                            <div class="wl-stepper">
                                                @foreach ([1 => 'Đặt hàng', 2 => 'Xác nhận & Đóng gói', 3 => 'Đang vận chuyển', 4 => 'Giao thành công'] as $sI => $sLabel)
                                                    @php
                                                        $isDone = $stepNum >= $sI;
                                                        $isCurr = $stepNum === $sI;
                                                        $isDelivered = ($stepNum === 4);
                                                        $isChecked = $stepNum > $sI || ($isDelivered && $sI === 4);
                                                    @endphp
                                                    <div class="wl-step {{ $isDone ? 'is-done' : '' }} {{ ($isCurr && !$isDelivered) ? 'is-curr' : '' }}">
                                                        <div class="wl-step-circle">{{ $isChecked ? '✓' : $sI }}</div>
                                                        <span class="wl-step-label">{{ $sLabel }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- ITEMS --}}
                                        <div class="wl-order-items">
                                            @foreach ($order->items as $item)
                                                @php
                                                    $iv = $item->product_variant;
                                                    $ip = $iv?->product;
                                                    $iThumb = $iv?->image ?: (($ip?->images ?: [])[0] ?? null);
                                                @endphp
                                                <div class="wl-order-item-row">
                                                    <div class="wl-thumb-box small">
                                                        @if ($iThumb)
                                                            <img src="{{ asset('storage/' . ltrim($iThumb, '/')) }}" alt="">
                                                        @else
                                                            <span class="wl-thumb-fallback">BABU</span>
                                                        @endif
                                                    </div>
                                                    <div class="wl-order-item-info">
                                                        <h4>{{ $ip?->name ?? 'Sản phẩm' }}</h4>
                                                        <div class="wl-spec-row">
                                                            @if ($iv?->sku)<span class="wl-spec-badge sku">{{ $iv->sku }}</span>@endif
                                                            @if ($iv?->name)<span class="wl-spec-badge variant">{{ $iv->name }}</span>@endif
                                                            <span class="wl-spec-badge qty">SL: ×{{ $item->quantity }}</span>
                                                        </div>
                                                        @php
                                                            $itemWEnd = $item->warranty ? \Illuminate\Support\Carbon::parse($item->warranty) : null;
                                                            $isItemActive = $itemWEnd && $itemWEnd->isFuture();
                                                        @endphp
                                                        @if ($isItemActive)
                                                            <div style="font-size: 12px; color: #16a34a; font-weight: 600; margin-top: 3px;">
                                                                Hạn bảo hành: {{ $itemWEnd->format('d/m/Y') }}
                                                            </div>
                                                        @elseif ($order->status === 'delivered' || $order->status === 'completed' || str_starts_with($order->order_code, 'BH'))
                                                            <div style="margin-top: 4px;">
                                                                <button type="button" wire:click="openOrderActivation('{{ $order->order_code }}')" style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 4px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-size: 11.5px; font-weight: 700; cursor: pointer;">
                                                                    ⚡ Kích hoạt bảo hành ngay →
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="wl-order-item-price">
                                                        {{ number_format((float) $item->total_amount, 0, ',', '.') }}₫
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- FOOTER --}}
                                        <div class="wl-order-footer">
                                            <div class="wl-order-address">
                                                @if ($order->address)
                                                    <span>Người nhận: <b>{{ $order->address->full_name }}</b> ({{ $order->address->phone }})</span>
                                                    <small>{{ collect([$order->address->detailed_address, $order->address->ward, $order->address->district, $order->address->province])->filter()->implode(', ') }}</small>
                                                @endif
                                            </div>
                                            <div class="wl-order-total">
                                                <span>Tổng thanh toán:</span>
                                                <strong>{{ number_format((float) $order->grand_total, 0, ',', '.') }}₫</strong>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div style="margin-top: 24px; padding: 24px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; text-align: center;">
                        <p style="margin: 0 0 6px; font-size: 14.5px !important; font-weight: 700 !important; color: #0f172a;">Nhập Mã đơn hàng hoặc Số điện thoại để theo dõi đơn hàng</p>
                        <span style="font-size: 13px !important; color: #64748b;">Hoặc <a href="/account/login" style="color: var(--wl-brand); font-weight: 700; text-decoration: underline;">Đăng nhập tài khoản</a> để xem toàn bộ lịch sử đơn hàng của bạn.</span>
                    </div>
                @endif
            </section>

        {{-- ================= TAB 3: KÍCH HOẠT BẢO HÀNH ================= --}}
        @elseif ($activeTab === 'activate')
            @include('livewire.partials.warranty-activation-form')
        @endif
    </main>

    @livewire('inc.footer')

    <style>
        .wl-page, .wl-page *, .wl-page input, .wl-page button, .wl-page select, .wl-page textarea {
            box-sizing: border-box;
            font-family: Roboto, Arial, sans-serif !important;
        }
        .wl-page {
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .wl-container {
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
            padding: 16px 16px 64px;
            flex: 1;
        }
        .wl-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px !important;
            color: #64748b;
            margin-bottom: 20px;
        }
        .wl-breadcrumb a {
            color: #64748b;
            text-decoration: none;
        }
        .wl-breadcrumb a:hover {
            color: #0f172a;
            text-decoration: underline;
        }
        .wl-sep {
            color: #cbd5e1;
        }
        .wl-cur {
            color: #0f172a;
            font-weight: 600 !important;
        }

        /* Top 3 tabs */
        .wl-top-tabs-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }
        .wl-top-tabs {
            display: inline-flex;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            flex-wrap: wrap;
            justify-content: center;
        }
        .wl-top-tab,
        .wl-top-tab:hover,
        .wl-top-tab:focus,
        .wl-top-tab:active,
        .wl-top-tab *,
        .wl-top-tab .tab-label {
            text-decoration: none !important;
        }
        .wl-top-tab {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14.5px !important;
            font-weight: 700 !important;
            border: none;
            background: transparent;
            color: #334155;
            cursor: pointer;
            text-decoration: none !important;
            transition: all 0.15s ease;
        }
        .wl-top-tab .tab-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wl-top-tab:hover {
            background: #f1f5f9;
            color: #0f172a;
            text-decoration: none !important;
        }
        .wl-top-tab.is-active {
            background: var(--wl-brand) !important;
            color: #ffffff !important;
            text-decoration: none !important;
            box-shadow: 0 2px 6px rgba(24, 42, 187, 0.25);
        }
        .wl-top-tab.is-active .tab-label {
            color: #ffffff !important;
            text-decoration: none !important;
        }

        /* Center card */
        .wl-center-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 36px 32px 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            text-align: center;
        }
        .wl-main-title {
            margin: 0 0 10px;
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            letter-spacing: normal !important;
            font-family: Roboto, Arial, sans-serif !important;
        }
        .wl-main-sub {
            margin: 0 auto 24px;
            font-size: 13.5px !important;
            color: #475569;
            line-height: 1.6;
            max-width: 720px;
        }
        .wl-hl {
            color: var(--wl-brand);
            font-weight: 600 !important;
            text-decoration: none;
        }
        .wl-hl:hover {
            text-decoration: underline;
        }

        /* Radio Options */
        .wl-radio-group {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .wl-radio-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px !important;
            color: #334155;
            font-weight: 500 !important;
            user-select: none;
        }
        .wl-radio-pill input {
            display: none;
        }
        .radio-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .radio-dot::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--wl-brand);
            display: none;
        }
        .wl-radio-pill.is-selected .radio-dot {
            border-color: var(--wl-brand);
        }
        .wl-radio-pill.is-selected .radio-dot::after {
            display: block;
        }
        .wl-radio-pill.is-selected span:last-child {
            color: #0f172a;
            font-weight: 700 !important;
        }

        /* Search input row */
        .wl-search-row {
            display: flex;
            justify-content: center;
            gap: 10px;
            max-width: 640px;
            margin: 0 auto;
        }
        .wl-input-wrap {
            position: relative;
            flex: 1;
        }
        .wl-input-wrap input {
            width: 100%;
            height: 46px;
            padding: 0 36px 0 16px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            font-size: 14.5px !important;
            color: #0f172a;
            outline: none;
            transition: all 0.15s ease;
        }
        .wl-input-wrap input:focus {
            background: #ffffff;
            border-color: var(--wl-brand);
            box-shadow: 0 0 0 3px rgba(24, 42, 187, 0.12);
        }
        .wl-input-clear {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            font-size: 18px !important;
            color: #94a3b8;
            cursor: pointer;
            padding: 0 4px;
            line-height: 1;
        }
        .wl-input-clear:hover {
            color: #0f172a;
        }
        .wl-search-submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 46px;
            padding: 0 22px;
            background: var(--wl-brand);
            border: 1px solid var(--wl-brand);
            border-radius: 8px;
            color: #ffffff;
            font-size: 14.5px !important;
            font-weight: 700 !important;
            cursor: pointer;
            transition: background 0.15s ease;
            white-space: nowrap;
        }
        .wl-search-submit-btn:hover {
            background: var(--wl-brand-dark);
            border-color: var(--wl-brand-dark);
        }

        .wl-example-note {
            margin-top: 10px;
            font-size: 12.5px !important;
            color: #64748b;
            text-align: center;
        }
        .wl-example-note code {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 1px 6px;
            border-radius: 4px;
            color: #0f172a;
            font-size: 12px !important;
        }

        /* Order Filter Bar */
        .wl-order-filter-bar {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        .wl-filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 99px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-size: 13px !important;
            font-weight: 600 !important;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .wl-filter-pill.is-active {
            background: var(--wl-brand);
            border-color: var(--wl-brand);
            color: #ffffff !important;
        }
        .pill-count {
            padding: 1px 6px;
            border-radius: 99px;
            font-size: 11px !important;
            background: rgba(0,0,0,0.06);
        }
        .wl-filter-pill.is-active .pill-count {
            background: rgba(255,255,255,0.25);
            color: #ffffff;
        }

        /* Stepper for orders */
        .wl-stepper {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 16px 0;
            padding: 0 10px;
        }
        .wl-stepper::before {
            content: '';
            position: absolute;
            top: 13px;
            left: 30px;
            right: 30px;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }
        .wl-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            position: relative;
            z-index: 2;
        }
        .wl-step-circle {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #64748b;
        }
        .wl-step.is-done .wl-step-circle {
            background: var(--wl-brand);
            border-color: var(--wl-brand);
            color: #ffffff;
        }
        .wl-step.is-curr .wl-step-circle {
            border-color: var(--wl-brand);
            color: var(--wl-brand);
            box-shadow: 0 0 0 3px rgba(24, 42, 187, 0.15);
        }
        .wl-step-label {
            font-size: 11.5px !important;
            color: #64748b;
            font-weight: 500 !important;
            text-align: center;
        }
        .wl-step.is-done .wl-step-label,
        .wl-step.is-curr .wl-step-label {
            color: #0f172a;
            font-weight: 700 !important;
        }

        /* Order card */
        .wl-order-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            text-align: left;
        }
        .wl-order-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px !important;
        }
        .wl-order-items {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .wl-order-item-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .wl-order-item-info {
            flex: 1;
            min-width: 0;
        }
        .wl-order-item-info h4 {
            margin: 0 0 4px;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #0f172a;
        }
        .wl-order-item-price {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a;
        }
        .wl-order-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 12px;
            gap: 16px;
            flex-wrap: wrap;
        }
        .wl-order-address {
            font-size: 12.5px !important;
            color: #64748b;
            line-height: 1.4;
        }
        .wl-order-address b {
            color: #0f172a;
        }
        .wl-order-address small {
            display: block;
            color: #94a3b8;
        }
        .wl-order-total {
            text-align: right;
            font-size: 13px !important;
            color: #64748b;
        }
        .wl-order-total strong {
            display: block;
            font-size: 16px !important;
            color: var(--wl-brand);
        }

        /* Results section */
        .wl-results-section {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
            text-align: left;
        }
        .wl-no-results {
            text-align: center;
            padding: 24px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
        }
        .wl-no-results p {
            margin: 0 0 4px;
            font-size: 14.5px !important;
            font-weight: 600 !important;
            color: #0f172a;
        }
        .wl-no-results span {
            font-size: 13px !important;
            color: #64748b;
        }
        .wl-results-head {
            font-size: 13.5px !important;
            color: #475569;
            margin-bottom: 14px;
        }
        .wl-results-head b {
            color: #0f172a;
        }

        /* Card List */
        .wl-cards-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .wl-item-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            transition: border-color 0.15s ease;
        }
        .wl-item-card:hover {
            border-color: #cbd5e1;
        }
        .wl-thumb-box {
            width: 68px;
            height: 68px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        .wl-thumb-box.small {
            width: 44px;
            height: 44px;
        }
        .wl-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .wl-thumb-fallback {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #94a3b8;
        }
        .wl-item-body {
            flex: 1;
            min-width: 0;
        }
        .wl-item-title {
            margin: 0 0 6px;
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #0f172a;
            line-height: 1.35;
        }
        .wl-spec-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 6px;
        }
        .wl-spec-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            line-height: 1.4;
        }
        .wl-spec-badge.sku {
            background: #f1f5f9;
            color: #334155;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }
        .wl-spec-badge.variant {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
        }
        .wl-spec-badge.qty {
            background: #f8fafc;
            color: #64748b;
        }
        .wl-item-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            font-size: 12px !important;
            color: #64748b;
        }
        .wl-code {
            color: #2563eb;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }
        .wl-dot {
            color: #cbd5e1;
        }

        /* Status Column */
        .wl-item-status-col {
            flex-shrink: 0;
            min-width: 180px;
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }
        .wl-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 9px;
            border-radius: 4px;
            font-size: 12px !important;
            font-weight: 700 !important;
        }
        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }
        .wl-status-badge.active {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .wl-status-badge.active .badge-dot {
            background: #16a34a;
        }
        .wl-status-badge.pending {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .wl-status-badge.pending .badge-dot {
            background: #f59e0b;
        }
        .wl-status-badge.expired {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .wl-status-badge.expired .badge-dot {
            background: #94a3b8;
        }
        .wl-status-date {
            font-size: 13px !important;
            color: #0f172a;
        }
        .wl-status-date b {
            font-weight: 700 !important;
            color: #15803d;
        }
        .wl-status-sub {
            font-size: 11.5px !important;
            color: #64748b;
        }
        .wl-status-sub.is-positive {
            color: #16a34a;
            font-weight: 600 !important;
        }
        .wl-activate-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
            padding: 6px 12px;
            background: var(--wl-brand);
            color: #ffffff !important;
            border: none;
            border-radius: 5px;
            font-size: 12px !important;
            font-weight: 600 !important;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .wl-activate-btn:hover {
            background: var(--wl-brand-dark);
        }

        .wl-status-pill {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 4px;
            font-size: 12px !important;
            font-weight: 700 !important;
        }
        .wl-status-pill.is-pending {
            background: #fef3c7;
            color: #b45309;
        }
        .wl-status-pill.is-processing {
            background: #eff6ff;
            color: #1d4ed8;
        }
        .wl-status-pill.is-shipping {
            background: #f0fdf4;
            color: #15803d;
        }
        .wl-status-pill.is-completed {
            background: #ecfdf5;
            color: #047857;
        }
        .wl-status-pill.is-cancelled {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* 2-column layout for Activation */
        .wa-two-col-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
            gap: 20px;
            align-items: start;
            width: 100%;
        }
        .wa-form-col {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            min-width: 0;
            overflow: hidden;
            box-sizing: border-box;
        }
        .wa-step-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .wa-step-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--wl-brand);
            color: #ffffff;
            font-size: 13px !important;
            font-weight: 700 !important;
            flex-shrink: 0;
        }
        .wa-search-inline-form {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }
        .wa-inline-search-box {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        .wa-search-icon {
            position: absolute;
            left: 10px;
            color: #94a3b8;
            pointer-events: none;
        }
        .wa-inline-search-box input {
            width: 100%;
            height: 38px;
            padding: 0 32px 0 34px;
            border: none;
            background: transparent;
            font-size: 13.5px !important;
            color: #0f172a;
            outline: none;
        }
        .wa-inline-clear-btn {
            position: absolute;
            right: 8px;
            width: 22px;
            height: 22px;
            border: none;
            border-radius: 50%;
            background: #f1f5f9;
            color: #64748b;
            font-size: 15px !important;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wa-inline-clear-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .wa-inline-search-btn {
            height: 38px;
            padding: 0 14px;
            border-radius: 6px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-size: 13px !important;
            font-weight: 600 !important;
            cursor: pointer;
            flex-shrink: 0;
        }
        .wa-alert-warning {
            padding: 10px 12px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
            border-radius: 6px;
            font-size: 12.5px !important;
            margin-bottom: 14px;
        }
        .wa-selected-order-box,
        .wa-selected-product-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 14px;
        }
        .wa-selected-product-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .wa-selected-product-head .head-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #15803d;
        }
        .wa-change-product-btn {
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .wa-change-product-btn:hover {
            border-color: var(--wl-brand);
            color: var(--wl-brand);
        }
        .order-code-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px !important;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .wa-item-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            min-width: 0;
            flex-shrink: 0 !important;
            box-sizing: border-box;
        }
        .wa-item-pill.is-active {
            border-color: #fca5a5;
            background: #fff5f5;
        }
        .wa-variant-picker-wrap {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
        }
        .wa-variant-picker-label {
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #334155;
            margin-bottom: 8px;
        }
        .wa-variant-chips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));
            gap: 8px;
        }
        .wa-variant-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 8px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            cursor: pointer;
            text-align: left;
            transition: all 0.15s ease;
            position: relative;
            min-width: 0;
        }
        .wa-variant-chip:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }
        .wa-variant-chip.is-selected {
            border-color: var(--wl-brand);
            background: #fff5f5;
            box-shadow: 0 0 0 1px var(--wl-brand);
        }
        .wa-variant-chip .chip-thumb {
            width: 28px !important;
            height: 28px !important;
            min-width: 28px !important;
            max-width: 28px !important;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            flex-shrink: 0;
        }
        .wa-variant-chip .chip-thumb img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            display: block !important;
        }
        .wa-variant-chip .chip-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
        }
        .wa-variant-chip .chip-name {
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #0f172a;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .wa-variant-chip .chip-sku {
            font-size: 10.5px !important;
            color: #64748b;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }
        .wa-variant-chip .chip-check {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--wl-brand);
            color: #ffffff;
            font-size: 10px !important;
            font-weight: 700 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .wa-thumb-box {
            width: 48px !important;
            height: 48px !important;
            min-width: 48px !important;
            max-width: 48px !important;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden !important;
            flex-shrink: 0 !important;
            box-sizing: border-box;
        }
        .wa-thumb-box.small {
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            max-width: 44px !important;
        }
        .wa-thumb-box img {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: cover !important;
            display: block !important;
        }
        .wa-thumb-fallback {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #94a3b8;
        }
        .wa-item-details {
            flex: 1;
            min-width: 0;
        }
        .wa-tags-row {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }
        .wa-tag {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px !important;
            font-weight: 600 !important;
            line-height: 1.35;
        }
        .wa-tag.sku {
            background: #f1f5f9;
            color: #334155;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }
        .wa-tag.variant {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475569;
        }
        .wa-tag.count {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }
        .wa-tag.warranty {
            color: #16a34a;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .wa-variants-list-scroll {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 360px;
            overflow-y: auto;
            overflow-x: hidden;
            min-width: 0;
            width: 100%;
            padding-right: 2px;
        }
        .wa-variant-select-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            min-height: 64px;
            flex-shrink: 0 !important;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            transition: border-color 0.15s ease, background 0.15s ease;
        }
        .wa-variant-select-item:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .wa-variant-select-item.is-selected {
            border-color: #fca5a5;
            background: #fff5f5;
        }
        .wa-pick-indicator {
            flex-shrink: 0;
            margin-left: auto;
        }
        .wa-pick-indicator .unpicked {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            background: #f1f5f9;
            color: #334155;
            font-size: 11.5px !important;
            font-weight: 600 !important;
        }
        .wa-pick-indicator .picked {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            background: var(--wl-brand);
            color: #ffffff;
            font-size: 11.5px !important;
            font-weight: 600 !important;
        }

        /* Form fields */
        .wa-fields-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .wa-field-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .wa-field-group label {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #334155;
        }
        .wa-field-group label .req {
            color: #dc2626;
        }
        .wa-field-group input,
        .wa-field-group textarea {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13.5px !important;
            color: #0f172a;
            outline: none;
        }
        .wa-field-group textarea {
            height: auto;
            padding: 8px 12px;
            resize: vertical;
        }
        .wa-field-group input:focus,
        .wa-field-group textarea:focus {
            border-color: var(--wl-brand);
            box-shadow: 0 0 0 2px rgba(24, 42, 187, 0.12);
        }
        .wa-field-hint {
            font-size: 11.5px !important;
            color: #64748b;
        }
        .wa-field-error {
            font-size: 12px !important;
            color: #dc2626;
            font-weight: 500 !important;
        }
        .wa-submit-action-btn {
            height: 42px;
            margin-top: 6px;
            background: var(--wl-brand);
            color: #ffffff;
            border: 1px solid var(--wl-brand);
            border-radius: 6px;
            font-size: 14px !important;
            font-weight: 600 !important;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .wa-submit-action-btn:hover {
            background: var(--wl-brand-dark);
        }

        /* Already activated state card */
        .wa-already-active-col {
            background: #ffffff;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 24px 20px;
        }
        .wa-active-badge-card {
            text-align: center;
            padding-bottom: 18px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 16px;
        }
        .wa-active-icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }
        .wa-active-badge-desc {
            margin: 0 auto;
            font-size: 13px !important;
            color: #64748b;
            line-height: 1.45;
            max-width: 420px;
        }
        .wa-active-details-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }
        .wa-active-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px !important;
            gap: 10px;
        }
        .wa-active-detail-row .label {
            color: #64748b;
        }
        .wa-active-detail-row .val {
            color: #0f172a;
            font-weight: 600 !important;
            text-align: right;
        }
        .wa-active-detail-row .val.code {
            color: #2563eb;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }
        .val-badge.active {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 8px;
            border-radius: 4px;
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            font-size: 12px !important;
            font-weight: 700 !important;
        }
        .val-badge.active .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #16a34a;
        }
        .wa-already-actions {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        /* Success Panel */
        .wa-success-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px;
            max-width: 760px;
            margin: 0 auto;
        }
        .wa-success-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .wa-success-badge-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            flex-shrink: 0;
        }
        .wa-info-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .wa-info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .info-label {
            font-size: 10.5px !important;
            font-weight: 700 !important;
            color: #64748b;
        }
        .info-val {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #0f172a;
        }
        .info-val.code {
            color: #2563eb;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }

        @media (max-width: 680px) {
            .wl-center-card {
                padding: 24px 16px;
            }
            .wl-search-row {
                flex-direction: column;
            }
            .wl-search-submit-btn {
                width: 100%;
            }
            .wl-item-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .wl-item-status-col {
                width: 100%;
                text-align: left;
                align-items: flex-start;
                padding-top: 10px;
                border-top: 1px solid #f1f5f9;
            }
            .wa-two-col-layout {
                grid-template-columns: 1fr;
            }
            .wa-info-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</div>
