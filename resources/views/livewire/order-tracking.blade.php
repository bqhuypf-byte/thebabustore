<div class="ot-page">
    <head>
        <title>Quản lý & Tra cứu đơn hàng | The BABU Store</title>
    </head>
    @livewire('inc.header')

    <main class="ot-container">
        <nav class="ot-breadcrumb" aria-label="Breadcrumb">
            <a href="/">Trang chủ</a>
            <span class="ot-sep">›</span>
            <span class="ot-cur">Quản lý đơn hàng</span>
        </nav>

        <div class="ot-head">
            <div class="ot-head-left">
                <div class="ot-title-row">
                    <svg class="ot-cube-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                        <path d="m3.3 7 8.7 5 8.7-5"/>
                        <path d="M12 22V12"/>
                    </svg>
                    <h1>Quản Lý &amp; Tra Cứu Đơn Hàng</h1>
                </div>
                <p>Theo dõi tiến độ giao nhận, lịch sử mua sắm bằng <b>Số điện thoại</b> hoặc <b>Mã đơn hàng (#WEB...)</b>.</p>
            </div>
        <div class="wl-top-tabs-wrap" style="display: flex; justify-content: center; margin-bottom: 24px;">
            <div class="wl-top-tabs" style="display: inline-flex; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 6px; gap: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <a href="/tra-cuu-bao-hanh" class="wl-top-tab" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 700; text-decoration: none; color: #334155;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                        <circle cx="12" cy="15" r="2"/>
                    </svg>
                    <span>Tra cứu thời gian bảo hành</span>
                </a>
                <a href="/orders" class="wl-top-tab is-active" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 700; text-decoration: none; background: #d70018; color: #ffffff; box-shadow: 0 2px 6px rgba(215, 0, 24, 0.25);">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#ffffff" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <circle cx="10" cy="9" r="1"/>
                    </svg>
                    <span>Tra cứu đơn hàng</span>
                </a>
                <a href="/kich-hoat" class="wl-top-tab" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 700; text-decoration: none; color: #334155;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline points="9 12 11 14 15 10"/>
                    </svg>
                    <span>Kích hoạt bảo hành</span>
                </a>
            </div>
        </div>

        <div class="ot-toolbar">
            <div class="ot-search-box">
                <svg class="ot-search-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="query"
                    placeholder="Tìm theo SĐT, mã đơn / bảo hành (WEB..., BH...) hoặc mã SKU..."
                    aria-label="Tìm theo mã đơn hàng, số điện thoại hoặc mã SKU"
                    autocomplete="off"
                >
                @if($hasQuery)
                    <button type="button" class="ot-clear-btn" wire:click="clearSearch" aria-label="Xóa từ khóa">×</button>
                @endif
            </div>

            @php
                $tabs = [
                    'all' => 'Tất cả',
                    'pending' => 'Chờ xác nhận',
                    'shipping' => 'Đang vận chuyển',
                    'completed' => 'Đã giao thành công',
                    'cancelled' => 'Đã hủy',
                ];
            @endphp
            <div class="ot-tabs" role="tablist" aria-label="Lọc trạng thái đơn hàng">
                @foreach($tabs as $key => $label)
                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $statusFilter === $key ? 'true' : 'false' }}"
                        class="ot-tab {{ $statusFilter === $key ? 'active' : '' }}"
                        wire:click="setStatusFilter('{{ $key }}')"
                    >
                        <span>{{ $label }}</span>
                        <b class="ot-tab-count">{{ $counts[$key] ?? 0 }}</b>
                    </button>
                @endforeach
            </div>
        </div>

        @if($orders->isEmpty())
            <section class="ot-empty" aria-live="polite">
                <div class="ot-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                        <path d="m3.3 7 8.7 5 8.7-5"/>
                        <path d="M12 22V12"/>
                    </svg>
                </div>
                <h2>Không tìm thấy đơn hàng nào</h2>
                @if(!auth()->check() && !$hasQuery)
                    <p>Nhập <b>mã đơn hàng</b> hoặc <b>số điện thoại đặt hàng</b> vào ô tìm kiếm phía trên để tra cứu nhanh lộ trình đơn hàng của bạn.</p>
                @else
                    <p>Không có đơn hàng nào khớp với bộ lọc hiện tại. Bạn có thể kiểm tra lại mã đơn / số điện thoại hoặc khám phá sản phẩm mới tại cửa hàng.</p>
                @endif
                <a href="/shop" class="ot-cta-dark">Khám phá cửa hàng ngay</a>
            </section>
        @else
            <div class="ot-list" aria-live="polite">
                @foreach($orders as $order)
                    @php
                        $group = \App\Livewire\OrderTracking::normalizeStatusGroup($order->status);
                        $rawStatus = strtolower((string) $order->status);
                        $statusMeta = match ($group) {
                            'shipping' => ['label' => 'Đang vận chuyển', 'class' => 'is-shipping', 'step' => 3],
                            'completed' => ['label' => 'Đã giao thành công', 'class' => 'is-completed', 'step' => 4],
                            'cancelled' => ['label' => 'Đã hủy', 'class' => 'is-cancelled', 'step' => 0],
                            default => $rawStatus === 'processing'
                                ? ['label' => 'Xác nhận & Đóng gói', 'class' => 'is-processing', 'step' => 2]
                                : ['label' => 'Chờ xác nhận', 'class' => 'is-pending', 'step' => 1],
                        };
                        $paymentLabel = match ($order->payment_method) {
                            'bank', 'appotabank' => 'Chuyển khoản ngân hàng',
                            'cod' => 'Thanh toán khi nhận hàng (COD)',
                            default => strtoupper((string) $order->payment_method),
                        };
                        $isPaid = $order->payment_status === 'paid';
                    @endphp
                    <article class="ot-card" wire:key="order-{{ $order->id }}">
                        <div class="ot-card-head">
                            <div class="ot-card-meta">
                                <span class="ot-order-code">#{{ $order->order_code }}</span>
                                <span class="ot-dot">•</span>
                                <time datetime="{{ optional($order->created_at)->toIso8601String() }}">
                                    {{ optional($order->created_at)->format('d/m/Y H:i') }}
                                </time>
                                <span class="ot-dot">•</span>
                                <span class="ot-pay-method">{{ $paymentLabel }}</span>
                            </div>
                            <div class="ot-badges">
                                <span class="ot-pay-badge {{ $isPaid ? 'paid' : 'unpaid' }}">
                                    {{ $isPaid ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                                </span>
                                <span class="ot-status-badge {{ $statusMeta['class'] }}">
                                    {{ $statusMeta['label'] }}
                                </span>
                            </div>
                        </div>

                        @if($group !== 'cancelled')
                            <div class="ot-stepper" aria-label="Tiến độ đơn hàng">
                                @foreach([1 => 'Đặt hàng', 2 => 'Xác nhận & Đóng gói', 3 => 'Đang vận chuyển', 4 => 'Giao thành công'] as $stepNum => $stepName)
                                    @php
                                        $isStepDone = $statusMeta['step'] >= $stepNum;
                                        $isStepCurr = $statusMeta['step'] === $stepNum;
                                        $isDelivered = ($statusMeta['step'] === 4);
                                        $isStepChecked = $statusMeta['step'] > $stepNum || ($isDelivered && $stepNum === 4);
                                    @endphp
                                    <div class="ot-step {{ $isStepDone ? 'done' : '' }} {{ ($isStepCurr && !$isDelivered) ? 'current' : '' }}">
                                        <span class="ot-step-dot">{{ $isStepChecked ? '✓' : $stepNum }}</span>
                                        <span class="ot-step-label">{{ $stepName }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="ot-items">
                            @foreach($order->items as $item)
                                @php
                                    $variant = $item->product_variant;
                                    $product = $variant?->product;
                                    $thumb = $variant?->image ?: (($product?->images ?: [])[0] ?? null);
                                    $warrantyDate = $item->warranty ? \Illuminate\Support\Carbon::parse($item->warranty) : null;
                                    $orderCreated = $order->created_at ? \Illuminate\Support\Carbon::parse($order->created_at) : now();
                                    $isPendingActivation = ($order->address?->phone === '0000000000')
                                        || ($warrantyDate && $warrantyDate->lessThanOrEqualTo($orderCreated->copy()->addMinutes(5)) && (int) ($variant?->warranty_days ?? 0) > 0);
                                    $daysRemaining = ($warrantyDate && $warrantyDate->isFuture())
                                        ? (int) now()->diffInDays($warrantyDate, false)
                                        : 0;
                                @endphp
                                <div class="ot-item">
                                    <a class="ot-item-thumb" href="{{ $product ? '/product/'.$product->slug : '#' }}">
                                        @if($thumb)
                                            <img src="{{ asset('storage/'.$thumb) }}" alt="{{ $product?->name ?? 'Sản phẩm' }}" loading="lazy">
                                        @else
                                            <span>BABU</span>
                                        @endif
                                    </a>
                                    <div class="ot-item-info">
                                        @if($product)
                                            <a class="ot-item-name" href="/product/{{ $product->slug }}">{{ $product->name }}</a>
                                        @else
                                            <strong class="ot-item-name">Sản phẩm đơn hàng</strong>
                                        @endif
                                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 2px;">
                                            @if($variant?->sku)
                                                <span style="padding: 2px 7px; border-radius: 5px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-family: monospace !important; font-size: 11.5px !important; font-weight: 800 !important;">
                                                    SKU: {{ $variant->sku }}
                                                </span>
                                            @endif
                                            @if($variant?->name)
                                                <span class="ot-item-variant">Phân loại: {{ $variant->name }}</span>
                                            @endif
                                            <span class="ot-item-qty">• SL: ×{{ $item->quantity }}</span>
                                        </div>

                                        <div style="margin-top: 5px;">
                                            @if($isPendingActivation)
                                                <a href="/kich-hoat/{{ $order->order_code }}" style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 99px; background: #eff6ff; border: 1px solid #93c5fd; color: #1d4ed8; font-size: 12px !important; font-weight: 700 !important; text-decoration: none;">
                                                    ⚡ Kích hoạt bảo hành ngay →
                                                </a>
                                            @elseif($warrantyDate && $warrantyDate->isFuture())
                                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 99px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 12px !important; font-weight: 700 !important;">
                                                    🛡️ Bảo hành đến: {{ $warrantyDate->format('d/m/Y') }} (Còn {{ $daysRemaining }} ngày)
                                                </span>
                                            @elseif($warrantyDate)
                                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 99px; background: #f3f4f6; color: #6b7280; font-size: 12px !important; font-weight: 600 !important;">
                                                    Hạn bảo hành: {{ $warrantyDate->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ot-item-price">
                                        <strong>{{ number_format($item->total_amount, 0, ',', '.') }}₫</strong>
                                        @if($item->quantity > 1)
                                            <small>{{ number_format($item->unit_amount, 0, ',', '.') }}₫ / sp</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="ot-card-foot">
                            <div class="ot-address">
                                @if($order->address)
                                    <strong>Người nhận: {{ $order->address->full_name }} ({{ $order->address->phone }})</strong>
                                    <span>{{ collect([$order->address->detailed_address, $order->address->ward, $order->address->district, $order->address->province])->filter()->implode(', ') }}</span>
                                @endif
                            </div>
                            <div class="ot-total-block">
                                @if(($order->shipping_amount ?? 0) > 0)
                                    <span class="ot-ship-fee">Phí vận chuyển: {{ number_format($order->shipping_amount, 0, ',', '.') }}₫</span>
                                @endif
                                <div class="ot-grand-total">
                                    <span>Tổng thanh toán:</span>
                                    <strong>{{ number_format($order->grand_total, 0, ',', '.') }}₫</strong>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </main>

    @livewire('inc.footer')

    <style>
        .ot-page, .ot-page * {
            box-sizing: border-box;
            font-family: StoreGeist, "Roboto", system-ui, -apple-system, sans-serif !important;
        }
        .ot-page {
            background: #ffffff;
            color: #14161c;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .ot-container {
            width: 100%;
            max-width: 1120px;
            margin: 0 auto;
            padding: 24px 20px 72px;
            flex: 1;
        }
        .ot-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px !important;
            color: #68707d;
            margin-bottom: 18px;
        }
        .ot-breadcrumb a {
            color: #68707d;
            text-decoration: none;
        }
        .ot-breadcrumb a:hover {
            color: #1240f7;
        }
        .ot-cur {
            color: #14161c;
            font-weight: 600;
        }
        .ot-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 22px;
            border-bottom: 1px solid #eef0f4;
        }
        .ot-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ot-cube-ic {
            width: 28px;
            height: 28px;
            color: #14161c;
            flex: none;
        }
        .ot-head h1 {
            margin: 0;
            font-size: 28px !important;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #14161c;
            line-height: 1.2;
        }
        .ot-head p {
            margin: 6px 0 0;
            color: #68707d;
            font-size: 14px !important;
            line-height: 1.5;
        }
        .ot-head p b {
            color: #14161c;
            font-weight: 600;
        }
        .ot-head-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: none;
        }
        .ot-btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 38px;
            padding: 0 15px;
            border: 1px solid #e4e7ec;
            border-radius: 99px;
            background: #ffffff;
            color: #14161c;
            font-size: 13.5px !important;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }
        .ot-btn-ghost:hover {
            border-color: #c9d1f8;
            background: #f8f9fc;
            color: #1240f7;
        }
        .ot-btn-ghost svg {
            width: 15px;
            height: 15px;
            flex: none;
        }
        @keyframes ot-rotate {
            to { transform: rotate(360deg); }
        }
        .ot-spin {
            animation: ot-rotate .7s linear infinite;
        }
        .ot-switch-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 18px 0 0;
            flex-wrap: wrap;
        }
        .ot-switch-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 99px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            text-decoration: none;
            transition: all .15s;
        }
        .ot-switch-pill:hover {
            border-color: #d70018;
            color: #d70018;
        }
        .ot-switch-pill.is-active {
            background: #d70018;
            border-color: #d70018;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(215, 0, 24, 0.25);
        }
        .ot-toolbar {
            margin: 22px 0 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .ot-search-box {
            position: relative;
            width: 100%;
            max-width: 520px;
        }
        .ot-search-ic {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 17px;
            height: 17px;
            color: #8b93a1;
            pointer-events: none;
        }
        .ot-search-box input {
            width: 100%;
            height: 44px;
            padding: 0 40px 0 42px;
            border: 1px solid #e4e7ec;
            border-radius: 99px;
            background: #ffffff;
            color: #14161c;
            font-size: 14px !important;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .ot-search-box input::-webkit-search-cancel-button {
            display: none;
        }
        .ot-search-box input:focus {
            border-color: #a8b7ff;
            box-shadow: 0 0 0 3px #eef1ff;
        }
        .ot-clear-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 0;
            background: #f1f3f7;
            color: #68707d;
            font-size: 16px !important;
            line-height: 1;
            display: grid;
            place-items: center;
            cursor: pointer;
        }
        .ot-clear-btn:hover {
            background: #e4e7ec;
            color: #14161c;
        }
        .ot-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .ot-tab {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 36px;
            padding: 0 14px;
            border-radius: 99px;
            border: 1px solid #e4e7ec;
            background: #ffffff;
            color: #4b5563;
            font-size: 13px !important;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
        }
        .ot-tab:hover {
            border-color: #cfd5e2;
            color: #14161c;
        }
        .ot-tab.active {
            background: #14161c;
            border-color: #14161c;
            color: #ffffff;
        }
        .ot-tab-count {
            display: inline-grid;
            place-items: center;
            min-width: 20px;
            height: 19px;
            padding: 0 6px;
            border-radius: 99px;
            background: #f3f4f6;
            color: #4b5563;
            font-size: 11px !important;
            font-weight: 700;
        }
        .ot-tab.active .ot-tab-count {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }
        .ot-empty {
            border: 1px solid #eceef2;
            border-radius: 20px;
            background: #ffffff;
            padding: 64px 24px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(20, 22, 28, 0.02);
        }
        .ot-empty-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 16px;
            border-radius: 50%;
            background: #f3f4f6;
            color: #4b5563;
            display: grid;
            place-items: center;
        }
        .ot-empty-icon svg {
            width: 26px;
            height: 26px;
        }
        .ot-empty h2 {
            margin: 0 0 8px;
            font-size: 17px !important;
            font-weight: 700;
            color: #14161c;
        }
        .ot-empty p {
            margin: 0 auto 22px;
            max-width: 480px;
            color: #68707d;
            font-size: 13.5px !important;
            line-height: 1.55;
        }
        .ot-empty p b {
            color: #14161c;
            font-weight: 600;
        }
        .ot-cta-dark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            padding: 0 22px;
            border-radius: 99px;
            background: #14161c;
            color: #ffffff !important;
            font-size: 13.5px !important;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s, transform .15s;
        }
        .ot-cta-dark:hover {
            background: #1240f7;
            transform: translateY(-1px);
        }
        .ot-list {
            display: grid;
            gap: 18px;
        }
        .ot-card {
            border: 1px solid #e4e7ec;
            border-radius: 18px;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(20, 22, 28, 0.04);
        }
        .ot-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 15px 20px;
            background: #f8f9fc;
            border-bottom: 1px solid #eef0f4;
        }
        .ot-card-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 13.5px !important;
            color: #68707d;
        }
        .ot-order-code {
            font-weight: 800;
            color: #1240f7;
            font-size: 14.5px !important;
            letter-spacing: 0.2px;
        }
        .ot-dot {
            color: #cbd5e1;
        }
        .ot-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .ot-pay-badge, .ot-status-badge {
            display: inline-flex;
            align-items: center;
            height: 26px;
            padding: 0 11px;
            border-radius: 99px;
            font-size: 12px !important;
            font-weight: 700;
        }
        .ot-pay-badge.paid {
            background: #e7f6ef;
            color: #0e7a4f;
        }
        .ot-pay-badge.unpaid {
            background: #f3f4f6;
            color: #4b5563;
        }
        .ot-status-badge.is-pending, .ot-status-badge.is-processing {
            background: #fffaeb;
            color: #b54708;
            border: 1px solid #fde68a;
        }
        .ot-status-badge.is-shipping {
            background: #eef2ff;
            color: #1240f7;
            border: 1px solid #c7d2fe;
        }
        .ot-status-badge.is-completed {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .ot-status-badge.is-cancelled {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .ot-stepper {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            padding: 14px 20px;
            border-bottom: 1px solid #f1f3f7;
            background: #ffffff;
        }
        .ot-step {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #98a0ad;
            font-size: 12.5px !important;
            font-weight: 600;
        }
        .ot-step-dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #f1f3f7;
            color: #68707d;
            display: grid;
            place-items: center;
            font-size: 11px !important;
            font-weight: 800;
            flex: none;
        }
        .ot-step.done {
            color: #14161c;
        }
        .ot-step.done .ot-step-dot {
            background: #eef2ff;
            color: #1240f7;
        }
        .ot-step.current .ot-step-dot {
            background: #1240f7;
            color: #ffffff;
        }
        .ot-items {
            padding: 6px 20px;
        }
        .ot-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .ot-item:last-child {
            border-bottom: 0;
        }
        .ot-item-thumb {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            border: 1px solid #eef0f4;
            background: #f8f9fc;
            overflow: hidden;
            flex: none;
            display: grid;
            place-items: center;
            text-decoration: none;
            color: #98a0ad;
            font-size: 11px !important;
            font-weight: 700;
        }
        .ot-item-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .ot-item-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .ot-item-name {
            color: #14161c;
            font-size: 14.5px !important;
            font-weight: 700;
            text-decoration: none;
            line-height: 1.35;
        }
        .ot-item-name:hover {
            color: #1240f7;
        }
        .ot-item-variant, .ot-item-qty {
            color: #68707d;
            font-size: 12.5px !important;
        }
        .ot-item-price {
            text-align: right;
            flex: none;
        }
        .ot-item-price strong {
            display: block;
            color: #d92332;
            font-size: 14.5px !important;
            font-weight: 800;
        }
        .ot-item-price small {
            color: #68707d;
            font-size: 11.5px !important;
        }
        .ot-card-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            padding: 14px 20px;
            background: #fbfcfe;
            border-top: 1px solid #eef0f4;
        }
        .ot-address {
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-size: 13px !important;
            color: #68707d;
        }
        .ot-address strong {
            color: #14161c;
            font-weight: 600;
        }
        .ot-total-block {
            margin-left: auto;
            text-align: right;
        }
        .ot-ship-fee {
            display: block;
            font-size: 12px !important;
            color: #68707d;
            margin-bottom: 2px;
        }
        .ot-grand-total {
            display: flex;
            align-items: baseline;
            gap: 8px;
            font-size: 14px !important;
        }
        .ot-grand-total strong {
            font-size: 18px !important;
            font-weight: 800;
            color: #d92332;
        }
        @media (max-width: 720px) {
            .ot-head {
                flex-direction: column;
            }
            .ot-stepper {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .ot-card-foot {
                flex-direction: column;
                align-items: flex-start;
            }
            .ot-total-block {
                margin-left: 0;
                text-align: left;
            }
        }
    </style>
</div>
