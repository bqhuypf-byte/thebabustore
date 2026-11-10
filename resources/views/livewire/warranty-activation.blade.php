<div class="wa-page">
    <head>
        <title>Kích hoạt bảo hành điện tử | The BABU Store</title>
    </head>
    @livewire('inc.header')

    <main class="wa-container">
        <nav class="wa-breadcrumb" aria-label="Breadcrumb">
            <a href="/">Trang chủ</a>
            <span class="wa-sep">/</span>
            <a href="/tra-cuu-bao-hanh">Bảo hành điện tử</a>
            <span class="wa-sep">/</span>
            <span class="wa-cur">Kích hoạt sản phẩm</span>
        </nav>

        <div class="wa-header">
            <div class="wa-header-left">
                <div class="wa-title-row">
                    <svg class="wa-title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                    <h1>Kích Hoạt Bảo Hành Điện Tử</h1>
                </div>
                <p>Điền thông tin liên hệ và chọn sản phẩm để kích hoạt bảo hành chính hãng. Sau khi kích hoạt, bạn có thể tra cứu mọi lúc bằng <b>Số điện thoại</b> hoặc <b>Mã đơn hàng</b>.</p>
            </div>
        </div>

        {{-- SWITCH BAR DUY NHẤT (GOM GỌN 1 HÀNG TABS) --}}
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
                <a href="/orders" class="wl-top-tab" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 700; text-decoration: none; color: #334155;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <circle cx="10" cy="9" r="1"/>
                    </svg>
                    <span>Tra cứu đơn hàng</span>
                </a>
                <a href="/kich-hoat" class="wl-top-tab is-active" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 700; text-decoration: none; background: #d70018; color: #ffffff; box-shadow: 0 2px 6px rgba(215, 0, 24, 0.25);">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#ffffff" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline points="9 12 11 14 15 10"/>
                    </svg>
                    <span>Kích hoạt bảo hành</span>
                </a>
            </div>
        </div>

        @if ($activatedSuccess && $order)
            {{-- MÀN HÌNH KÍCH HOẠT THÀNH CÔNG --}}
            <section class="wa-success-panel">
                <div class="wa-success-header">
                    <div class="wa-success-badge-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Kích Hoạt Bảo Hành Thành Công</h2>
                        <p>Thông tin sản phẩm và thời hạn bảo hành đã được lưu vào hệ thống theo số điện thoại <b>{{ $order->address?->phone }}</b>.</p>
                    </div>
                </div>

                <div class="wa-info-cards-grid">
                    <div class="wa-info-card">
                        <span class="info-label">MÃ ĐƠN HÀNG / BẢO HÀNH</span>
                        <strong class="info-val code">#{{ $order->order_code }}</strong>
                    </div>
                    <div class="wa-info-card">
                        <span class="info-label">KHÁCH HÀNG</span>
                        <strong class="info-val">{{ $order->address?->full_name }}</strong>
                    </div>
                    <div class="wa-info-card">
                        <span class="info-label">SỐ ĐIỆN THOẠI TRA CỨU</span>
                        <strong class="info-val code">{{ $order->address?->phone }}</strong>
                    </div>
                    <div class="wa-info-card">
                        <span class="info-label">TRẠNG THÁI BẢO HÀNH</span>
                        <strong class="info-val" style="color: #16a34a;">Đang có hiệu lực</strong>
                    </div>
                </div>

                <div class="wa-items-summary" style="margin-top: 18px;">
                    <div style="font-size: 13.5px !important; font-weight: 700 !important; color: #0f172a; margin-bottom: 10px;">
                        Danh sách sản phẩm được kích hoạt:
                    </div>
                    @foreach ($order->items as $item)
                        @php
                            $v = $item->product_variant;
                            $p = $v?->product;
                            $thumb = $v?->image ?: (($p?->images ?: [])[0] ?? null);
                            $warrantyEnd = $item->warranty ? \Illuminate\Support\Carbon::parse($item->warranty) : null;
                            $daysLeft = $warrantyEnd ? max(0, (int) now()->diffInDays($warrantyEnd, false)) : 0;
                        @endphp
                        <div class="wa-summary-item-card">
                            <div class="wa-thumb-box">
                                @if ($thumb)
                                    <img src="{{ asset('storage/' . ltrim($thumb, '/')) }}" alt="{{ $p?->name }}">
                                @else
                                    <span class="wa-thumb-fallback">BABU</span>
                                @endif
                            </div>
                            <div class="wa-summary-item-info">
                                <h4>{{ $p?->name ?? 'Sản phẩm chính hãng' }}</h4>
                                <div class="wa-tags-row">
                                    <span class="wa-tag sku">SKU: {{ $v?->sku ?: 'N/A' }}</span>
                                    @if ($v?->name)
                                        <span class="wa-tag variant">Loại: {{ $v->name }}</span>
                                    @endif
                                    <span class="wa-tag qty">SL: ×{{ $item->quantity }}</span>
                                </div>
                            </div>
                            <div class="wa-summary-item-expiry">
                                <span class="expiry-label">Hạn bảo hành đến</span>
                                <strong class="expiry-date">{{ $warrantyEnd ? $warrantyEnd->format('d/m/Y') : '—' }}</strong>
                                <span class="expiry-sub">Còn {{ $daysLeft }} ngày hiệu lực</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="wa-success-actions-row">
                    <a href="/tra-cuu-bao-hanh?q={{ urlencode((string) $order->address?->phone) }}" class="wa-action-btn primary">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                        <span>Tra cứu bảo hành của tôi</span>
                    </a>
                    <a href="/orders?q={{ urlencode((string) $order->address?->phone) }}" class="wa-action-btn secondary">
                        <span>Xem đơn hàng</span>
                    </a>
                    <a href="/shop" class="wa-action-btn secondary">
                        <span>Tiếp tục mua sắm</span>
                    </a>
                </div>
            </section>
        @else
            {{-- GIAO DIỆN FORM 2 CỘT CHUẨN E-COMMERCE --}}
            <div class="wa-two-col-layout">
                {{-- CỘT 1: CHỌN SẢN PHẨM / MÃ SKU --}}
                <div class="wa-form-col">
                    <div class="wa-step-header">
                        <span class="wa-step-badge">1</span>
                        <div>
                            <h2>Sản Phẩm &amp; Mã Kích Hoạt</h2>
                            <p>Nhập tên sản phẩm, Mã SKU (VD: A5D9939, S75...) hoặc Mã đơn (#WEB..., #BH...).</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="searchCodeOrSku" class="wa-search-inline-form">
                        <div class="wa-inline-search-box">
                            <svg class="wa-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.3-4.3"/>
                            </svg>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="code"
                                placeholder="Gõ tên sản phẩm, mã SKU hoặc mã đơn..."
                                autocomplete="off"
                            >
                        </div>
                        <button type="submit" class="wa-inline-search-btn">Tìm mã</button>
                    </form>

                    @error('lookup')
                        <div class="wa-alert-warning">{{ $message }}</div>
                    @enderror

                    @if ($order)
                        {{-- THÔNG TIN ĐƠN ĐÃ CÓ --}}
                        <div class="wa-selected-order-box">
                            <div class="order-code-row">
                                <div>
                                    <span class="label">Đơn hàng kích hoạt:</span>
                                    <strong class="code">#{{ $order->order_code }}</strong>
                                </div>
                                <div class="date">{{ optional($order->created_at)->format('d/m/Y H:i') }}</div>
                            </div>

                            <div class="wa-order-items-compact">
                                @foreach ($order->items as $item)
                                    @php
                                        $v = $item->product_variant;
                                        $p = $v?->product;
                                        $thumb = $v?->image ?: (($p?->images ?: [])[0] ?? null);
                                        $wDays = (int) ($v?->warranty_days ?: 365);
                                        if ($wDays <= 0) $wDays = 365;
                                    @endphp
                                    <div class="wa-item-pill is-active">
                                        <div class="wa-thumb-box small">
                                            @if ($thumb)
                                                <img src="{{ asset('storage/' . ltrim($thumb, '/')) }}" alt="{{ $p?->name }}">
                                            @else
                                                <span class="wa-thumb-fallback">BABU</span>
                                            @endif
                                        </div>
                                        <div class="wa-item-details">
                                            <h4>{{ $p?->name ?? 'Sản phẩm chính hãng' }}</h4>
                                            <div class="wa-tags-row">
                                                <span class="wa-tag sku">SKU: {{ $v?->sku ?: 'N/A' }}</span>
                                                @if ($v?->name)
                                                    <span class="wa-tag variant">{{ $v->name }}</span>
                                                @endif
                                                <span class="wa-tag qty">SL: ×{{ $item->quantity }}</span>
                                            </div>
                                            <div class="wa-warranty-period">
                                                Thời hạn: {{ $wDays === 365 ? '12 tháng (365 ngày)' : $wDays . ' ngày' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- NẾU ĐÃ CHỌN VARIANT --}}
                        @if ($variant)
                            @php
                                $p = $variant->product;
                                $thumb = $variant->image ?: (($p?->images ?: [])[0] ?? null);
                                $wDays = (int) ($variant->warranty_days ?: 365);
                                if ($wDays <= 0) $wDays = 365;
                            @endphp
                            <div class="wa-chosen-variant-card">
                                <div class="chosen-badge-row">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    <span>Đã chọn sản phẩm kích hoạt:</span>
                                </div>
                                <div class="wa-item-pill is-active">
                                    <div class="wa-thumb-box small">
                                        @if ($thumb)
                                            <img src="{{ asset('storage/' . ltrim($thumb, '/')) }}" alt="{{ $p?->name }}">
                                        @else
                                            <span class="wa-thumb-fallback">BABU</span>
                                        @endif
                                    </div>
                                    <div class="wa-item-details">
                                        <h4>{{ $p?->name ?? 'Sản phẩm chính hãng' }}</h4>
                                        <div class="wa-tags-row">
                                            <span class="wa-tag sku">SKU: {{ $variant->sku }}</span>
                                            @if ($variant->name)
                                                <span class="wa-tag variant">{{ $variant->name }}</span>
                                            @endif
                                            <span class="wa-tag warranty">BH {{ $wDays === 365 ? '12 tháng' : $wDays . ' ngày' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- DANH SÁCH GỢI Ý / TÌM KIẾM --}}
                        <div class="wa-select-label">
                            {{ trim($code) !== '' ? 'Kết quả tìm kiếm sản phẩm (bấm chọn):' : 'Sản phẩm phổ biến (bấm chọn):' }}
                        </div>

                        <div class="wa-variants-list-scroll">
                            @forelse ($matchingVariants as $mv)
                                @php
                                    $mp = $mv->product;
                                    $mThumb = $mv->image ?: (($mp?->images ?: [])[0] ?? null);
                                    $mDays = (int) ($mv->warranty_days ?: 365);
                                    if ($mDays <= 0) $mDays = 365;
                                    $isSelected = $variant && $variant->id === $mv->id;
                                @endphp
                                <button
                                    type="button"
                                    wire:click="selectVariant({{ $mv->id }})"
                                    class="wa-variant-select-item {{ $isSelected ? 'is-selected' : '' }}"
                                >
                                    <div class="wa-thumb-box small">
                                        @if ($mThumb)
                                            <img src="{{ asset('storage/' . ltrim($mThumb, '/')) }}" alt="{{ $mp?->name }}">
                                        @else
                                            <span class="wa-thumb-fallback">BABU</span>
                                        @endif
                                    </div>
                                    <div class="wa-item-details" style="text-align: left;">
                                        <h4>{{ $mp?->name ?? 'Sản phẩm' }}</h4>
                                        <div class="wa-tags-row">
                                            <span class="wa-tag sku">{{ $mv->sku }}</span>
                                            @if ($mv->name)
                                                <span class="wa-tag variant">{{ $mv->name }}</span>
                                            @endif
                                            <span class="wa-tag warranty">BH {{ $mDays === 365 ? '12 tháng' : $mDays . ' ngày' }}</span>
                                        </div>
                                    </div>
                                    <div class="wa-pick-indicator">
                                        @if ($isSelected)
                                            <span class="picked">Đang chọn</span>
                                        @else
                                            <span class="unpicked">Chọn</span>
                                        @endif
                                    </div>
                                </button>
                            @empty
                                <div class="wa-variants-empty">
                                    Không tìm thấy sản phẩm khớp với từ khóa "{{ $code }}".
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>

                {{-- CỘT 2: NẾU ĐÃ KÍCH HOẠT THÌ KHÔNG HIỆN Ô KÍCH HOẠT NỮA --}}
                @if ($isAlreadyActivated && $order)
                    <div class="wa-form-col wa-already-active-col">
                        <div class="wa-active-badge-card">
                            <div class="wa-active-icon-circle">
                                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                            </div>
                            <h3>Sản Phẩm Đã Được Kích Hoạt</h3>
                            <p class="wa-active-badge-desc">
                                Đơn hàng này đã hoàn tất kích hoạt bảo hành điện tử chính hãng trên hệ thống. Bạn không cần phải kích hoạt lại.
                            </p>
                        </div>

                        <div class="wa-active-details-list">
                            <div class="wa-active-detail-row">
                                <span class="label">Trạng thái:</span>
                                <span class="val-badge active">
                                    <span class="badge-dot"></span>
                                    <span>Đang có hiệu lực</span>
                                </span>
                            </div>
                            <div class="wa-active-detail-row">
                                <span class="label">Khách hàng:</span>
                                <strong class="val">{{ $order->address?->full_name }}</strong>
                            </div>
                            <div class="wa-active-detail-row">
                                <span class="label">SĐT tra cứu:</span>
                                <strong class="val code">{{ strlen($order->address?->phone ?? '') > 6 ? substr($order->address->phone, 0, 3) . '***' . substr($order->address->phone, -3) : $order->address?->phone }}</strong>
                            </div>
                            <div class="wa-active-detail-row">
                                <span class="label">Mã đơn / Mã BH:</span>
                                <code class="val code">#{{ $order->order_code }}</code>
                            </div>
                            @php
                                $firstItem = $order->items->first();
                                $wEnd = $firstItem?->warranty ? \Illuminate\Support\Carbon::parse($firstItem->warranty) : null;
                                $daysLeft = $wEnd ? max(0, (int) now()->diffInDays($wEnd, false)) : 0;
                            @endphp
                            @if ($wEnd)
                                <div class="wa-active-detail-row">
                                    <span class="label">Thời hạn bảo hành:</span>
                                    <strong class="val" style="color: #16a34a;">Hạn đến {{ $wEnd->format('d/m/Y') }} (Còn {{ $daysLeft }} ngày)</strong>
                                </div>
                            @endif
                        </div>

                        <div class="wa-already-actions">
                            <a href="/tra-cuu-bao-hanh?q={{ urlencode((string) $order->order_code) }}" class="wa-action-btn primary" style="justify-content: center; width: 100%;">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                </svg>
                                <span>Tra cứu chi tiết bảo hành</span>
                            </a>
                            <button type="button" class="wa-action-btn secondary" wire:click="$set('orderId', null); $set('code', ''); $set('productSearch', '')" style="justify-content: center; width: 100%; margin-top: 10px;">
                                <span>Kích hoạt sản phẩm khác</span>
                            </button>
                        </div>
                    </div>
                @else
                    {{-- CỘT 2: FORM THÔNG TIN KHÁCH HÀNG (CHỈ HIỆN KHI CHƯA KÍCH HOẠT) --}}
                    <div class="wa-form-col">
                        <div class="wa-step-header">
                            <span class="wa-step-badge">2</span>
                            <div>
                                <h2>Thông Tin Khách Hàng</h2>
                                <p>Nhập họ tên và số điện thoại chính xác để liên kết bảo hành và tra cứu sau này.</p>
                            </div>
                        </div>

                        <form wire:submit.prevent="activateWarranty" class="wa-fields-form">
                            <div class="wa-field-group">
                                <label for="wa-full-name">Họ và tên khách hàng <span class="req">*</span></label>
                                <input
                                    id="wa-full-name"
                                    type="text"
                                    wire:model="full_name"
                                    placeholder="Ví dụ: Nguyễn Văn A"
                                    required
                                >
                                @error('full_name') <span class="wa-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="wa-field-group">
                                <label for="wa-phone">Số điện thoại kích hoạt &amp; tra cứu <span class="req">*</span></label>
                                <input
                                    id="wa-phone"
                                    type="tel"
                                    wire:model="phone"
                                    placeholder="Ví dụ: 0912345678"
                                    required
                                >
                                <div class="wa-field-hint">Số điện thoại dùng để nhận tin nhắn và tra cứu thông tin bảo hành.</div>
                                @error('phone') <span class="wa-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="wa-field-group">
                                <label for="wa-address">Địa chỉ / Khu vực (Không bắt buộc)</label>
                                <input
                                    id="wa-address"
                                    type="text"
                                    wire:model="detailed_address"
                                    placeholder="Ví dụ: Quận 1, TP. Hồ Chí Minh"
                                >
                                @error('detailed_address') <span class="wa-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="wa-field-group">
                                <label for="wa-notes">Ghi chú hoặc Số Serial trên hộp (Không bắt buộc)</label>
                                <textarea
                                    id="wa-notes"
                                    rows="2"
                                    wire:model="notes"
                                    placeholder="Nhập ghi chú thêm hoặc số Serial nếu có..."
                                ></textarea>
                            </div>

                            <button type="submit" class="wa-submit-action-btn" wire:loading.attr="disabled">
                                <span wire:loading.remove>Xác Nhận Kích Hoạt Bảo Hành</span>
                                <span wire:loading>Đang xử lý kích hoạt...</span>
                            </button>

                            <div class="wa-back-link-row">
                                <a href="/tra-cuu-bao-hanh">Đã kích hoạt trước đó? Tra cứu bằng Số điện thoại &rarr;</a>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    </main>

    @livewire('inc.footer')

    <style>
        .wa-page, .wa-page * {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Roboto", "Segoe UI", Helvetica, Arial, sans-serif !important;
        }
        .wa-page {
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .wa-container {
            width: 100%;
            max-width: 1040px;
            margin: 0 auto;
            padding: 20px 16px 64px;
            flex: 1;
        }
        .wa-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px !important;
            color: #64748b;
            margin-bottom: 16px;
        }
        .wa-breadcrumb a {
            color: #64748b;
            text-decoration: none;
        }
        .wa-breadcrumb a:hover {
            color: #1e293b;
            text-decoration: underline;
        }
        .wa-sep {
            color: #cbd5e1;
        }
        .wa-cur {
            color: #0f172a;
            font-weight: 600 !important;
        }
        .wa-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .wa-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
        }
        .wa-title-icon {
            width: 24px;
            height: 24px;
            color: #2563eb;
            flex-shrink: 0;
        }
        .wa-header h1 {
            margin: 0;
            font-size: 22px !important;
            font-weight: 700 !important;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .wa-header p {
            margin: 0;
            font-size: 13.5px !important;
            color: #475569;
        }
        .wa-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .wa-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 38px;
            padding: 0 14px;
            border-radius: 6px;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .wa-action-btn.primary {
            background: #2563eb;
            border: 1px solid #2563eb;
            color: #ffffff !important;
        }
        .wa-action-btn.primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }
        .wa-action-btn.secondary {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155 !important;
        }
        .wa-action-btn.secondary:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        /* Nav Tabs */
        .wa-nav-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 22px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            overflow-x: auto;
        }
        .wa-nav-tab {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border-radius: 6px;
            background: transparent;
            color: #475569;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            text-decoration: none;
            border: 1px solid transparent;
            white-space: nowrap;
            transition: all 0.15s ease;
        }
        .wa-nav-tab:hover {
            color: #0f172a;
            background: #f1f5f9;
        }
        .wa-nav-tab.is-active {
            background: #ffffff;
            color: #2563eb !important;
            border-color: #cbd5e1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        /* Two column layout */
        .wa-two-col-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }
        .wa-form-col {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
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
            background: #2563eb;
            color: #ffffff;
            font-size: 13px !important;
            font-weight: 700 !important;
            flex-shrink: 0;
        }
        .wa-step-header h2 {
            margin: 0 0 4px;
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #0f172a;
        }
        .wa-step-header p {
            margin: 0;
            font-size: 12.5px !important;
            color: #64748b;
            line-height: 1.4;
        }

        /* Inline search */
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
        .wa-inline-search-box:focus-within {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
        }
        .wa-search-icon {
            position: absolute;
            left: 10px;
            width: 16px;
            height: 16px;
            color: #94a3b8;
            pointer-events: none;
        }
        .wa-inline-search-box input {
            width: 100%;
            height: 38px;
            padding: 0 12px 0 34px;
            border: none;
            background: transparent;
            font-size: 13.5px !important;
            color: #0f172a;
            outline: none;
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
            transition: all 0.15s ease;
        }
        .wa-inline-search-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
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

        /* Selected Order Box */
        .wa-selected-order-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 14px;
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
        .order-code-row .label {
            color: #64748b;
        }
        .order-code-row .code {
            color: #2563eb;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
            margin-left: 4px;
        }
        .order-code-row .date {
            color: #64748b;
            font-size: 12px !important;
        }

        /* Item Pill */
        .wa-item-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .wa-item-pill.is-active {
            border-color: #93c5fd;
            background: #eff6ff;
        }
        .wa-thumb-box {
            width: 48px;
            height: 48px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        .wa-thumb-box.small {
            width: 44px;
            height: 44px;
        }
        .wa-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .wa-thumb-fallback {
            font-size: 10px !important;
            font-weight: 700 !important;
            color: #94a3b8;
        }
        .wa-item-details {
            flex: 1;
            min-width: 0;
        }
        .wa-item-details h4 {
            margin: 0 0 4px;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #0f172a;
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
        .wa-tag.qty {
            color: #64748b;
        }
        .wa-tag.warranty {
            color: #16a34a;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .wa-warranty-period {
            margin-top: 4px;
            font-size: 11.5px !important;
            color: #16a34a;
            font-weight: 600 !important;
        }

        /* Chosen variant card */
        .wa-chosen-variant-card {
            margin-bottom: 14px;
        }
        .chosen-badge-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #16a34a;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        /* Variant Select List */
        .wa-select-label {
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: #475569;
            margin-bottom: 8px;
        }
        .wa-variants-list-scroll {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 340px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .wa-variant-select-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: all 0.15s ease;
        }
        .wa-variant-select-item:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }
        .wa-variant-select-item.is-selected {
            border-color: #2563eb;
            background: #eff6ff;
        }
        .wa-pick-indicator {
            flex-shrink: 0;
        }
        .wa-pick-indicator .picked {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            background: #2563eb;
            color: #ffffff;
            font-size: 11px !important;
            font-weight: 700 !important;
        }
        .wa-pick-indicator .unpicked {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            background: #f1f5f9;
            color: #475569;
            font-size: 11px !important;
            font-weight: 600 !important;
        }
        .wa-variants-empty {
            padding: 24px 16px;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            text-align: center;
            color: #64748b;
            font-size: 13px !important;
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
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .wa-field-group textarea {
            height: auto;
            padding: 8px 12px;
            resize: vertical;
        }
        .wa-field-group input:focus,
        .wa-field-group textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
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
            background: #d70018;
            color: #ffffff;
            border: 1px solid #d70018;
            border-radius: 6px;
            font-size: 14px !important;
            font-weight: 600 !important;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .wa-submit-action-btn:hover {
            background: #b90014;
        }
        .wa-submit-action-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .wa-back-link-row {
            text-align: center;
            margin-top: 4px;
        }
        .wa-back-link-row a {
            color: #2563eb;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            text-decoration: none;
        }
        .wa-back-link-row a:hover {
            text-decoration: underline;
        }

        /* Already Active State Card */
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
        .wa-active-badge-card h3 {
            margin: 0 0 6px;
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #0f172a;
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
            max-width: 800px;
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
        .wa-success-header h2 {
            margin: 0 0 4px;
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #0f172a;
        }
        .wa-success-header p {
            margin: 0;
            font-size: 13.5px !important;
            color: #475569;
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
            letter-spacing: 0.02em;
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

        .wa-summary-item-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 8px;
        }
        .wa-summary-item-info {
            flex: 1;
            min-width: 0;
        }
        .wa-summary-item-info h4 {
            margin: 0 0 4px;
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a;
        }
        .wa-summary-item-expiry {
            flex-shrink: 0;
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .expiry-label {
            font-size: 11px !important;
            color: #64748b;
        }
        .expiry-date {
            font-size: 14px !important;
            color: #16a34a;
            font-weight: 700 !important;
        }
        .expiry-sub {
            font-size: 11px !important;
            color: #64748b;
        }
        .wa-success-actions-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }

        @media (max-width: 768px) {
            .wa-two-col-layout {
                grid-template-columns: 1fr;
            }
            .wa-info-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .wa-summary-item-card {
                flex-direction: column;
                align-items: flex-start;
            }
            .wa-summary-item-expiry {
                text-align: left;
            }
            .wa-success-actions-row {
                flex-direction: column;
                align-items: stretch;
            }
            .wa-success-actions-row .wa-action-btn {
                justify-content: center;
            }
        }
    </style>
</div>
