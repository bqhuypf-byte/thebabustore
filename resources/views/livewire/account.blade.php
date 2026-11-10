<div class="cp-page">
    <head>
        <title>Trung tâm thành viên &amp; Bảo hành | The BABU Store</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    </head>
    @livewire('inc.header')

    <main class="cp-container">
        {{-- TOP MEMBER CARD (STYLE CELLPHONES) --}}
        <section class="cp-user-banner">
            <div class="cp-user-main">
                <div class="cp-avatar">
                    <span>{{ strtoupper(substr($user->name ?: 'U', 0, 1)) }}</span>
                </div>
                <div class="cp-user-meta">
                    <div class="cp-user-name-row">
                        <h2>{{ $user->name ?: 'Khách hàng' }}</h2>
                        <span class="cp-tier-badge">Thành viên BABU</span>
                    </div>
                    <div class="cp-user-sub">
                        <span>
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            {{ $user->phone ?: 'Chưa cập nhật SĐT' }}
                        </span>
                        <span>
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            {{ $user->email }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="cp-user-pills">
                <button type="button" class="cp-pill {{ $activeTab === 'overview' ? 'active' : '' }}" wire:click="setTab('overview')">
                    <b>Tổng quan</b>
                </button>
                <button type="button" class="cp-pill {{ $activeTab === 'orders' ? 'active' : '' }}" wire:click="setTab('orders')">
                    <b>Lịch sử mua hàng ({{ $allOrdersCount }})</b>
                </button>
                <button type="button" class="cp-pill {{ $activeTab === 'warranty' ? 'active' : '' }}" wire:click="setTab('warranty')">
                    <b>Tra cứu bảo hành ({{ $activeWarrantyCount }})</b>
                </button>
                <button type="button" class="cp-pill {{ $activeTab === 'profile' ? 'active' : '' }}" wire:click="setTab('profile')">
                    <b>Sổ địa chỉ &amp; Tài khoản</b>
                </button>
                <a href="/kich-hoat" class="cp-pill">
                    <b>Kích hoạt bảo hành</b>
                </a>
            </div>
        </section>

        {{-- MAIN 2-COLUMN LAYOUT --}}
        <div class="cp-layout">
            {{-- LEFT SIDEBAR NAVIGATION --}}
            <aside class="cp-sidebar">
                <div class="cp-nav-list">
                    <button type="button" class="cp-nav-item {{ $activeTab === 'overview' ? 'is-active' : '' }}" wire:click="setTab('overview')">
                        <span class="cp-nav-ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </span>
                        <span class="cp-nav-text">Tổng quan</span>
                    </button>

                    <button type="button" class="cp-nav-item {{ $activeTab === 'orders' ? 'is-active' : '' }}" wire:click="setTab('orders')">
                        <span class="cp-nav-ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                        </span>
                        <span class="cp-nav-text">Lịch sử mua hàng</span>
                        @if ($allOrdersCount > 0)
                            <b class="cp-nav-count">{{ $allOrdersCount }}</b>
                        @endif
                    </button>

                    {{-- TRA CỨU BẢO HÀNH NẰM RIÊNG BIỆT --}}
                    <button type="button" class="cp-nav-item {{ $activeTab === 'warranty' ? 'is-active' : '' }}" wire:click="setTab('warranty')">
                        <span class="cp-nav-ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                        </span>
                        <span class="cp-nav-text">Tra cứu bảo hành</span>
                        @if ($activeWarrantyCount > 0)
                            <b class="cp-nav-count is-green">{{ $activeWarrantyCount }}</b>
                        @endif
                    </button>

                    <a href="/kich-hoat" class="cp-nav-item">
                        <span class="cp-nav-ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                            </svg>
                        </span>
                        <span class="cp-nav-text">Kích hoạt bảo hành</span>
                    </a>

                    <div class="cp-nav-divider"></div>

                    <button type="button" class="cp-nav-item {{ $activeTab === 'profile' ? 'is-active' : '' }}" wire:click="setTab('profile')">
                        <span class="cp-nav-ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <span class="cp-nav-text">Thông tin tài khoản</span>
                    </button>

                    <button type="button" class="cp-nav-item {{ $activeTab === 'password' ? 'is-active' : '' }}" wire:click="setTab('password')">
                        <span class="cp-nav-ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <span class="cp-nav-text">Đổi mật khẩu</span>
                    </button>

                    <a href="/chinh-sach-bao-hanh" class="cp-nav-item">
                        <span class="cp-nav-ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="16" x2="12" y2="12"/>
                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                            </svg>
                        </span>
                        <span class="cp-nav-text">Chính sách bảo hành</span>
                    </a>

                    <a href="/contact" class="cp-nav-item">
                        <span class="cp-nav-ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </span>
                        <span class="cp-nav-text">Góp ý - Phản hồi - Hỗ trợ</span>
                    </a>

                    <a href="/account/logout" class="cp-nav-item is-logout">
                        <span class="cp-nav-ic">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                        </span>
                        <span class="cp-nav-text">Đăng xuất</span>
                    </a>
                </div>
            </aside>

            {{-- RIGHT MAIN CONTENT --}}
            <section class="cp-main">
                {{-- ================= TAB 1: TỔNG QUAN ================= --}}
                @if ($activeTab === 'overview')
                    <div class="cp-notif-banners">
                        <div class="cp-notif-box" x-data="{ open: true }" x-show="open">
                            <div class="cp-notif-content">
                                <svg class="cp-notif-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                <span>Kích hoạt bảo hành điện tử chính hãng để theo dõi thời hạn &amp; quyền lợi sản phẩm.</span>
                            </div>
                            <div class="cp-notif-actions">
                                <a href="/kich-hoat" class="cp-notif-link">Kích hoạt ngay</a>
                                <button type="button" class="cp-notif-close" @click="open = false" aria-label="Đóng">×</button>
                            </div>
                        </div>

                        <div class="cp-notif-box" x-data="{ open: true }" x-show="open">
                            <div class="cp-notif-content">
                                <svg class="cp-notif-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                <span>Thêm địa chỉ giao hàng chi tiết để hoàn tất đơn hàng nhanh hơn.</span>
                            </div>
                            <div class="cp-notif-actions">
                                <button type="button" class="cp-notif-link" wire:click="setTab('profile')">Thêm địa chỉ</button>
                                <button type="button" class="cp-notif-close" @click="open = false" aria-label="Đóng">×</button>
                            </div>
                        </div>

                        <div class="cp-notif-box" x-data="{ open: true }" x-show="open">
                            <div class="cp-notif-content">
                                <svg class="cp-notif-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                <span>Thành viên BABU được hỗ trợ vệ sinh bàn phím và tư vấn mod switch miễn phí.</span>
                            </div>
                            <div class="cp-notif-actions">
                                <a href="/shop" class="cp-notif-link">Xem ưu đãi</a>
                                <button type="button" class="cp-notif-close" @click="open = false" aria-label="Đóng">×</button>
                            </div>
                        </div>
                    </div>

                    <div class="cp-overview-grid">
                        <div class="cp-overview-left">
                            {{-- ĐƠN HÀNG GẦN ĐÂY --}}
                            <div class="cp-card">
                                <div class="cp-card-head">
                                    <h3>Đơn hàng gần đây</h3>
                                    <button type="button" class="cp-link-btn" wire:click="setTab('orders')">Xem tất cả ›</button>
                                </div>

                                <div class="cp-orders-list">
                                    @forelse ($recentOrders as $order)
                                        @php
                                            $st = strtolower((string) $order->status);
                                            $stLabel = match ($st) {
                                                'shipped', 'shipping' => 'Đang vận chuyển',
                                                'delivered', 'completed' => 'Giao thành công',
                                                'cancelled', 'canceled' => 'Đã hủy',
                                                'processing' => 'Xác nhận & Đóng gói',
                                                default => 'Chờ xác nhận',
                                            };
                                            $stClass = match ($st) {
                                                'delivered', 'completed' => 'is-delivered',
                                                'cancelled', 'canceled' => 'is-cancelled',
                                                'shipped', 'shipping' => 'is-shipping',
                                                default => 'is-pending',
                                            };
                                            $firstItem = $order->items->first();
                                            $firstVariant = $firstItem?->product_variant;
                                            $firstProduct = $firstVariant?->product;
                                            $thumb = $firstVariant?->image ?: (($firstProduct?->images ?: [])[0] ?? null);
                                        @endphp
                                        <div class="cp-order-row">
                                            <div class="cp-order-head-bar">
                                                <span>Đơn hàng: <b>#{{ $order->order_code }}</b> • Ngày đặt: {{ optional($order->created_at)->format('d/m/Y') }}</span>
                                                <span class="cp-status-pill {{ $stClass }}">{{ $stLabel }}</span>
                                            </div>

                                            <div class="cp-order-body-bar">
                                                <div class="cp-order-thumb">
                                                    @if ($thumb)
                                                        <img src="{{ asset('storage/' . ltrim($thumb, '/')) }}" alt="">
                                                    @else
                                                        <span>BABU</span>
                                                    @endif
                                                </div>

                                                <div class="cp-order-info-text">
                                                    <h4>{{ $firstProduct?->name ?? 'Sản phẩm The BABU Store' }}</h4>
                                                    @if ($firstVariant?->name)
                                                        <p>Phân loại: {{ $firstVariant->name }}</p>
                                                    @endif
                                                    @if ($order->items->count() > 1)
                                                        <small>+{{ $order->items->count() - 1 }} sản phẩm khác</small>
                                                    @endif
                                                </div>

                                                <div class="cp-order-price-bar">
                                                    <span>Tổng thanh toán: <strong>{{ number_format((float) $order->grand_total, 0, ',', '.') }}₫</strong></span>
                                                    <button type="button" class="cp-view-order-btn" wire:click="setTab('orders')">Xem chi tiết ›</button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="cp-empty-state">
                                            <p>Bạn chưa có đơn hàng nào tại cửa hàng.</p>
                                            <a href="/shop" class="cp-cta-btn">Khám phá sản phẩm ngay</a>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT WIDGET: ƯU ĐÃI & HỖ TRỢ --}}
                        <aside class="cp-overview-right">
                            <div class="cp-card cp-widget-card">
                                <h3>Ưu đãi của bạn</h3>
                                <div class="cp-gift-box">
                                    <div class="cp-gift-icon">
                                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 12 20 22 4 22 4 12"/>
                                            <rect x="2" y="7" width="20" height="5"/>
                                            <line x1="12" y1="22" x2="12" y2="7"/>
                                            <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                                            <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                                        </svg>
                                    </div>
                                    <p>Thành viên BABU nhận ưu đãi vệ sinh phím miễn phí &amp; hỗ trợ kỹ thuật trọn đời.</p>
                                    <a href="/shop?sort=discount" class="cp-gift-btn">Xem khuyến mãi mới</a>
                                </div>
                            </div>

                            <div class="cp-card cp-widget-card" style="margin-top: 16px;">
                                <h3>Hỗ trợ kỹ thuật &amp; Bảo hành</h3>
                                <p style="font-size: 13px; color: #64748b; margin: 4px 0 12px;">Cần bảo hành sản phẩm hoặc hỗ trợ mod switch / keycap?</p>
                                <a href="tel:{{ preg_replace('/\D+/', '', $settings?->hotline ?? '') }}" class="cp-support-line">
                                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                        </svg>
                                        Hotline:
                                    </span>
                                    <strong>{{ $settings?->hotline ?: '0355198659' }}</strong>
                                </a>
                                @if ($settings?->zalo)
                                    <a href="{{ str_starts_with($settings->zalo, 'http') ? $settings->zalo : 'https://zalo.me/' . preg_replace('/\D+/', '', $settings->zalo) }}" target="_blank" class="cp-support-line">
                                        <span style="display: inline-flex; align-items: center; gap: 4px;">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                            </svg>
                                            Zalo:
                                        </span>
                                        <strong>Chat ngay ↗</strong>
                                    </a>
                                @endif
                            </div>
                        </aside>
                    </div>

                {{-- ================= TAB 2: LỊCH SỬ MUA HÀNG (CELLPHONES STYLE) ================= --}}
                @elseif ($activeTab === 'orders')
                    <div class="cp-cell-panel">
                        {{-- STATUS FILTER TABS (CELLPHONES STYLE WITH RED UNDERLINE) --}}
                        <div class="cp-cell-tabs-bar">
                            @php
                                $orderFilterTabs = [
                                    'all' => 'Tất cả',
                                    'new' => 'Chờ xác nhận',
                                    'processing' => 'Đang xử lý',
                                    'shipping' => 'Đang vận chuyển',
                                    'delivered' => 'Đã nhận hàng',
                                    'cancelled' => 'Đã hủy',
                                ];
                            @endphp
                            @foreach ($orderFilterTabs as $k => $label)
                                <button
                                    type="button"
                                    class="cp-cell-tab-item {{ $orderStatusFilter === $k ? 'is-active' : '' }}"
                                    wire:click="setOrderStatusFilter('{{ $k }}')"
                                >
                                    <span>{{ $label }}</span>
                                    @if (($purchaseOrderCounts[$k] ?? 0) > 0)
                                        <small class="cp-cell-tab-badge">({{ $purchaseOrderCounts[$k] }})</small>
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        {{-- SUBHEAD ROW: TITLE, DATE PICKER & SEARCH --}}
                        <div class="cp-cell-subhead-row">
                            <h2 class="cp-cell-section-title">Lịch sử mua hàng</h2>

                            <div class="cp-cell-filter-controls">
                                <div class="cp-cell-datepicker">
                                    <input type="date" wire:model.live="dateFrom" class="cp-cell-date-field" title="Từ ngày">
                                    <span class="cp-cell-date-sep">→</span>
                                    <input type="date" wire:model.live="dateTo" class="cp-cell-date-field" title="Đến ngày">
                                    <svg class="cp-cell-date-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </div>

                                <div class="cp-cell-search-box">
                                    <input
                                        type="search"
                                        wire:model.live.debounce.300ms="orderSearch"
                                        placeholder="Tìm theo mã đơn hoặc sản phẩm..."
                                        class="cp-cell-search-input"
                                    >
                                    <svg class="cp-cell-search-icon" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- ORDERS LIST --}}
                        <div class="cp-cell-orders-container cp-orders-full-list">
                            @forelse ($orders as $order)
                                @php
                                    $st = strtolower((string) $order->status);
                                    $stMeta = match ($st) {
                                        'delivered', 'completed' => ['label' => 'Đã nhận hàng', 'class' => 'is-delivered'],
                                        'shipped', 'shipping' => ['label' => 'Đang vận chuyển', 'class' => 'is-shipping'],
                                        'cancelled', 'canceled' => ['label' => 'Đã hủy', 'class' => 'is-cancelled'],
                                        'processing' => ['label' => 'Đang xử lý', 'class' => 'is-processing'],
                                        default => ['label' => 'Chờ xác nhận', 'class' => 'is-pending'],
                                    };
                                @endphp
                                <article class="cp-cell-order-card">
                                    <div class="cp-cell-order-header">
                                        <div class="cp-cell-order-meta">
                                            <span>Đơn hàng: <b>#{{ $order->order_code }}</b></span>
                                            <span class="cp-cell-dot">•</span>
                                            <span>Ngày đặt hàng: {{ optional($order->created_at)->format('d/m/Y') }}</span>
                                        </div>
                                        <span class="cp-cell-status-pill {{ $stMeta['class'] }}">
                                            {{ $stMeta['label'] }}
                                        </span>
                                    </div>

                                    <div class="cp-cell-order-body">
                                        <div class="cp-cell-order-items">
                                            @foreach ($order->items as $item)
                                                @php
                                                    $iv = $item->product_variant;
                                                    $ip = $iv?->product;
                                                    $iThumb = $iv?->image ?: (($ip?->images ?: [])[0] ?? null);
                                                    $itemPrice = (float) ($item->price > 0 ? $item->price : ($item->total_amount / max(1, $item->quantity)));
                                                @endphp
                                                <div class="cp-cell-item-row">
                                                    <div class="cp-cell-item-thumb">
                                                        @if ($iThumb)
                                                            <img src="{{ asset('storage/' . ltrim($iThumb, '/')) }}" alt="{{ $ip?->name }}">
                                                        @else
                                                            <span>BABU</span>
                                                        @endif
                                                    </div>
                                                    <div class="cp-cell-item-info">
                                                        <h4 class="cp-cell-item-name">{{ $ip?->name ?? 'Sản phẩm The Babu Store' }}</h4>
                                                        @if ($iv?->name)
                                                            <div class="cp-cell-item-var">{{ $iv->name }}</div>
                                                        @endif
                                                        <div class="cp-cell-item-price">
                                                            {{ number_format($itemPrice, 0, ',', '.') }}₫
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="cp-cell-order-actions">
                                            <div class="cp-cell-total-row">
                                                <span class="cp-cell-total-lbl">Tổng thanh toán:</span>
                                                <strong class="cp-cell-total-amt">{{ number_format((float) $order->grand_total, 0, ',', '.') }}₫</strong>
                                            </div>
                                            <a href="/account/orders/{{ $order->order_code }}" class="cp-cell-detail-link">Xem chi tiết &rsaquo;</a>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="cp-cell-empty-list" style="padding: 40px 16px; text-align: center; color: #64748b;">
                                    <p style="margin: 0; font-size: 14px;">Không có đơn hàng nào phù hợp với bộ lọc.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                {{-- ================= TAB 3: TRA CỨU BẢO HÀNH (RMA / TIẾN TRÌNH GỬI BẢO HÀNH) ================= --}}
                @elseif ($activeTab === 'warranty')
                    <div class="cp-cell-panel">
                        {{-- STATUS FILTER TABS (CELLPHONES RMA PIPELINE) --}}
                        <div class="cp-cell-tabs-bar">
                            @php
                                $claimFilterTabs = [
                                    'all' => 'Tất cả',
                                    'received' => 'Đã tiếp nhận',
                                    'dispatching' => 'Đang điều phối',
                                    'repairing' => 'Đang sửa',
                                    'fixed' => 'Đã sửa xong',
                                    'returned' => 'Đã trả khách',
                                ];
                            @endphp
                            @foreach ($claimFilterTabs as $ck => $clabel)
                                <button
                                    type="button"
                                    class="cp-cell-tab-item {{ $warrantyClaimFilter === $ck ? 'is-active' : '' }}"
                                    wire:click="setWarrantyClaimFilter('{{ $ck }}')"
                                >
                                    <span>{{ $clabel }}</span>
                                    @if (($claimCounts[$ck] ?? 0) > 0)
                                        <small class="cp-cell-tab-badge">({{ $claimCounts[$ck] }})</small>
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        {{-- SEPARATE BANNER LINK TO WARRANTY VALIDITY LOOKUP --}}
                        <div class="cp-warranty-notice-banner">
                            <div class="cp-warranty-notice-content">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                <span>Bạn muốn kiểm tra thời hạn và quyền lợi bảo hành điện tử của thiết bị đã mua?</span>
                            </div>
                            <a href="/tra-cuu-bao-hanh" class="cp-warranty-notice-btn">
                                <span>Tra cứu thời gian bảo hành</span>
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                            </a>
                        </div>

                        @if ($filteredClaims->isEmpty())
                            {{-- CELLPHONES EMPTY STATE --}}
                            <div class="cp-cell-empty-rma">
                                <div class="cp-cell-empty-icon-wrap">
                                    <svg viewBox="0 0 160 140" width="130" height="114" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="80" cy="125" rx="55" ry="10" fill="#f1f5f9"/>
                                        <rect x="42" y="52" width="76" height="58" rx="8" fill="#fee2e2" stroke="#dc2626" stroke-width="2.5"/>
                                        <path d="M42 68h76" stroke="#ef4444" stroke-width="2"/>
                                        <path d="M80 52v58" stroke="#ef4444" stroke-width="2"/>
                                        <rect x="58" y="28" width="44" height="30" rx="6" fill="#ffffff" stroke="#cbd5e1" stroke-width="2"/>
                                        <path d="M68 38h24M68 46h16" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
                                        <circle cx="116" cy="44" r="14" fill="#d70018"/>
                                        <path d="M111 44l3.5 3.5 6.5-6.5" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h3 class="cp-cell-empty-title">Bạn chưa có đơn bảo hành</h3>
                                <div class="cp-cell-empty-actions">
                                    <a href="/" class="cp-cell-empty-home-link">Trang chủ</a>
                                    <span class="cp-cell-dot">•</span>
                                    <a href="/tra-cuu-bao-hanh" class="cp-cell-empty-sec-link">Tra cứu thời hạn bảo hành</a>
                                    <span class="cp-cell-dot">•</span>
                                    <a href="tel:{{ preg_replace('/\D+/', '', $settings?->hotline ?: '0355198659') }}" class="cp-cell-empty-sec-link">Hotline hỗ trợ</a>
                                </div>
                            </div>
                        @else
                            {{-- CLAIMS LIST --}}
                            <div class="cp-cell-orders-container">
                                @foreach ($filteredClaims as $claim)
                                    @php
                                        $claimStatusKey = \App\Livewire\Account::mapClaimStatus($claim);
                                        $claimMeta = \App\Livewire\Account::getClaimStatusMeta($claimStatusKey);
                                    @endphp
                                    <article class="cp-cell-order-card cp-cell-claim-card">
                                        <div class="cp-cell-order-header">
                                            <div class="cp-cell-order-meta">
                                                <span>Mã đơn tiếp nhận: <b>#{{ $claim->order_code }}</b></span>
                                                <span class="cp-cell-dot">•</span>
                                                <span>Ngày tiếp nhận: {{ optional($claim->created_at)->format('d/m/Y') }}</span>
                                            </div>
                                            <span class="cp-cell-status-label is-claim {{ $claimMeta['class'] }}">
                                                {{ $claimMeta['label'] }}
                                            </span>
                                        </div>

                                        <div class="cp-cell-order-items">
                                            @foreach ($claim->items as $cItem)
                                                @php
                                                    $civ = $cItem->product_variant;
                                                    $cip = $civ?->product;
                                                    $cThumb = $civ?->image ?: (($cip?->images ?: [])[0] ?? null);
                                                @endphp
                                                <div class="cp-cell-item-row">
                                                    <div class="cp-cell-item-thumb">
                                                        @if ($cThumb)
                                                            <img src="{{ asset('storage/' . ltrim($cThumb, '/')) }}" alt="{{ $cip?->name }}">
                                                        @else
                                                            <span>BABU</span>
                                                        @endif
                                                    </div>
                                                    <div class="cp-cell-item-info">
                                                        <h4 class="cp-cell-item-name">{{ $cip?->name ?? 'Thiết bị gửi bảo hành' }}</h4>
                                                        <div class="cp-cell-item-badges">
                                                            @if ($civ?->sku)<span class="cp-cell-badge sku">SKU: {{ $civ->sku }}</span>@endif
                                                            @if ($civ?->name)<span class="cp-cell-badge var">{{ $civ->name }}</span>@endif
                                                            <span class="cp-cell-badge qty">SL: ×{{ $cItem->quantity }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="cp-cell-claim-progress-box">
                                            <div class="cp-cell-progress-bar">
                                                @php
                                                    $claimSteps = [
                                                        'received' => 'Đã tiếp nhận',
                                                        'dispatching' => 'Đang điều phối',
                                                        'repairing' => 'Đang sửa',
                                                        'fixed' => 'Đã sửa xong',
                                                        'returned' => 'Đã trả khách',
                                                    ];
                                                    $stepKeys = array_keys($claimSteps);
                                                    $currIdx = array_search($claimStatusKey, $stepKeys, true);
                                                    if ($currIdx === false) $currIdx = 0;
                                                @endphp
                                                @foreach ($claimSteps as $sk => $slabel)
                                                    @php
                                                        $sIdx = array_search($sk, $stepKeys, true);
                                                        $isStepDone = $sIdx <= $currIdx;
                                                        $isStepCurrent = $sIdx === $currIdx;
                                                    @endphp
                                                    <div class="cp-cell-progress-step {{ $isStepDone ? 'is-done' : '' }} {{ $isStepCurrent ? 'is-active' : '' }}">
                                                        <div class="cp-cell-step-dot">{{ $isStepDone ? '✓' : ($sIdx + 1) }}</div>
                                                        <span class="cp-cell-step-title">{{ $slabel }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="cp-cell-order-footer">
                                            <div class="cp-cell-footer-recipient">
                                                <span>Trung tâm tiếp nhận: <b>The BABU Store Service Center</b></span>
                                                <small>Hotline hỗ trợ: {{ $settings?->hotline ?: '0355198659' }}</small>
                                            </div>
                                            <div class="cp-cell-footer-action">
                                                <a href="/account/orders/{{ $claim->order_code }}" class="cp-cell-detail-link">Xem chi tiết đơn bảo hành &rsaquo;</a>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>

                {{-- ================= TAB 4: THÔNG TIN TÀI KHOẢN ================= --}}
                @elseif ($activeTab === 'profile')
                    <div class="cp-card">
                        <div class="cp-card-head">
                            <div>
                                <h2>Thông tin tài khoản &amp; Sổ địa chỉ</h2>
                                <p>Cập nhật họ tên, số điện thoại và địa chỉ nhận hàng của bạn.</p>
                            </div>
                        </div>

                        @if ($profileUpdated)
                            <div class="cp-alert-success">✓ Đã cập nhật thông tin thành công!</div>
                        @endif

                        <form wire:submit.prevent="updateProfile" class="cp-form">
                            <div class="cp-form-group">
                                <label for="pf-name">Họ và tên <b>*</b></label>
                                <input id="pf-name" type="text" wire:model="name" required>
                                @error('name') <small class="cp-err">{{ $message }}</small> @enderror
                            </div>

                            <div class="cp-form-group">
                                <label for="pf-phone">Số điện thoại <b>*</b></label>
                                <input id="pf-phone" type="tel" wire:model="phone" required>
                                @error('phone') <small class="cp-err">{{ $message }}</small> @enderror
                            </div>

                            <div class="cp-form-group">
                                <label for="pf-email">Email</label>
                                <input id="pf-email" type="email" value="{{ $user->email }}" disabled style="background: #f1f5f9;">
                            </div>

                            <div class="cp-form-group">
                                <label for="pf-address">Địa chỉ nhận hàng mặc định</label>
                                <input id="pf-address" type="text" wire:model="detailed_address" placeholder="Ví dụ: 183 HT44, Phường Hiệp Thành, Quận 12, TP.HCM">
                                @error('detailed_address') <small class="cp-err">{{ $message }}</small> @enderror
                            </div>

                            <button type="submit" class="cp-cta-btn" wire:loading.attr="disabled">
                                <span wire:loading.remove>Lưu thông tin</span>
                                <span wire:loading>Đang lưu...</span>
                            </button>
                        </form>
                    </div>

                {{-- ================= TAB 5: ĐỔI MẬT KHẨU ================= --}}
                @elseif ($activeTab === 'password')
                    <div class="cp-card">
                        <div class="cp-card-head">
                            <div>
                                <h2>Đổi mật khẩu tài khoản</h2>
                                <p>Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác.</p>
                            </div>
                        </div>

                        @if ($passwordUpdated)
                            <div class="cp-alert-success">✓ Đã đổi mật khẩu thành công!</div>
                        @endif

                        <form wire:submit.prevent="updatePassword" class="cp-form">
                            <div class="cp-form-group">
                                <label for="pw-current">Mật khẩu hiện tại <b>*</b></label>
                                <input id="pw-current" type="password" wire:model="current_password" required>
                                @error('current_password') <small class="cp-err">{{ $message }}</small> @enderror
                            </div>

                            <div class="cp-form-group">
                                <label for="pw-new">Mật khẩu mới <b>*</b></label>
                                <input id="pw-new" type="password" wire:model="new_password" required>
                                @error('new_password') <small class="cp-err">{{ $message }}</small> @enderror
                            </div>

                            <div class="cp-form-group">
                                <label for="pw-confirm">Xác nhận mật khẩu mới <b>*</b></label>
                                <input id="pw-confirm" type="password" wire:model="new_password_confirmation" required>
                            </div>

                            <button type="submit" class="cp-cta-btn" wire:loading.attr="disabled">
                                <span wire:loading.remove>Cập nhật mật khẩu</span>
                                <span wire:loading>Đang cập nhật...</span>
                            </button>
                        </form>
                    </div>
                @endif
            </section>
        </div>
    </main>

    @livewire('inc.footer')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800&display=swap');

        .cp-page, .cp-page *, .cp-page input, .cp-page button, .cp-page select, .cp-page textarea {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
        .cp-page {
            background: #f4f6f8;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .cp-container {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 20px 20px 72px;
            flex: 1;
        }
        /* TOP USER BANNER (CELLPHONES STYLE) */
        .cp-user-banner {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 18px 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .cp-user-main {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .cp-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1240f7, #0c2fbf);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-size: 24px !important;
            font-weight: 800 !important;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(18, 64, 247, 0.25);
        }
        .cp-user-meta h2 {
            margin: 0;
            font-size: 20px !important;
            font-weight: 800 !important;
            color: #0f172a;
        }
        .cp-user-name-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .cp-tier-badge {
            padding: 2px 9px;
            border-radius: 99px;
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #b45309;
            font-size: 11.5px !important;
            font-weight: 700 !important;
        }
        .cp-user-sub {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 4px;
            font-size: 13px !important;
            color: #64748b;
        }
        .cp-user-pills {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            border-top: 1px solid #f1f5f9;
            padding-top: 14px;
        }
        .cp-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 99px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #334155;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-decoration: none;
            cursor: pointer;
            transition: all .15s;
        }
        .cp-pill:hover, .cp-pill.active {
            border-color: #1240f7;
            background: #eff6ff;
            color: #1240f7;
        }
        /* 2-COLUMN LAYOUT */
        .cp-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 20px;
            align-items: start;
        }
        @media (max-width: 900px) {
            .cp-layout {
                grid-template-columns: 1fr;
            }
        }
        /* SIDEBAR (CELLPHONES STYLE) */
        .cp-sidebar {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 12px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }
        .cp-nav-list {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .cp-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 12px;
            border: 0;
            background: transparent;
            color: #334155;
            font-size: 14px !important;
            font-weight: 600 !important;
            text-align: left;
            text-decoration: none;
            cursor: pointer;
            width: 100%;
            transition: all .15s;
            position: relative;
        }
        .cp-nav-item:hover {
            background: #f8fafc;
            color: #1240f7;
        }
        .cp-nav-item.is-active {
            background: #fee2e2;
            color: #dc2626 !important;
            font-weight: 700 !important;
            border-left: 3px solid #dc2626;
        }
        .cp-nav-ic {
            font-size: 16px;
            flex-shrink: 0;
        }
        .cp-nav-text {
            flex: 1;
        }
        .cp-nav-count {
            padding: 1px 7px;
            border-radius: 99px;
            background: #e2e8f0;
            color: #475569;
            font-size: 11px !important;
            font-weight: 700 !important;
        }
        .cp-nav-count.is-green {
            background: #dcfce7;
            color: #15803d;
        }
        .cp-nav-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 8px 6px;
        }
        .cp-nav-item.is-logout {
            color: #dc2626;
        }
        .cp-nav-item.is-logout:hover {
            background: #fef2f2;
        }
        /* NOTIFICATION BANNERS (STYLE CELLPHONES) */
        .cp-notif-banners {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 18px;
        }
        .cp-notif-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 14px;
            background: #f0f7ff;
            border: 1px solid #d0e6ff;
            border-radius: 6px;
            color: #1e40af;
            font-size: 13px !important;
        }
        .cp-notif-content {
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
        }
        .cp-notif-icon {
            color: #2563eb;
            flex-shrink: 0;
        }
        .cp-notif-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .cp-notif-link {
            color: #2563eb;
            font-weight: 600 !important;
            font-size: 12.5px !important;
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            white-space: nowrap;
        }
        .cp-notif-link:hover {
            text-decoration: underline;
        }
        .cp-notif-close {
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 18px !important;
            line-height: 1;
            cursor: pointer;
            padding: 0 4px;
        }
        .cp-notif-close:hover {
            color: #0f172a;
        }
        /* OVERVIEW GRID */
        .cp-overview-grid {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 20px;
            align-items: start;
        }
        @media (max-width: 1080px) {
            .cp-overview-grid {
                grid-template-columns: 1fr;
            }
        }
        .cp-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 20px 24px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }
        .cp-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 14px;
            margin-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .cp-card-head h3, .cp-card-head h2 {
            margin: 0;
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #0f172a;
        }
        .cp-card-head p {
            margin: 3px 0 0;
            font-size: 13px !important;
            color: #64748b;
        }
        .cp-link-btn {
            background: none;
            border: 0;
            color: #1240f7;
            font-size: 13px !important;
            font-weight: 700 !important;
            cursor: pointer;
            padding: 0;
        }
        /* ORDER ROW (CELLPHONES STYLE) */
        .cp-orders-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .cp-order-row {
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: border-color .15s;
        }
        .cp-order-row:hover {
            border-color: #cbd5e1;
        }
        .cp-order-head-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px !important;
            color: #64748b;
            padding-bottom: 8px;
            border-bottom: 1px solid #f8fafc;
        }
        .cp-order-head-bar b {
            color: #1240f7;
        }
        .cp-status-pill {
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 11.5px !important;
            font-weight: 700 !important;
        }
        .cp-status-pill.is-delivered {
            background: #ecfdf5;
            color: #047857;
        }
        .cp-status-pill.is-shipping {
            background: #eef2ff;
            color: #1240f7;
        }
        .cp-status-pill.is-pending, .cp-status-pill.is-processing {
            background: #fffbeb;
            color: #b45309;
        }
        .cp-status-pill.is-cancelled {
            background: #fef2f2;
            color: #dc2626;
        }
        .cp-order-body-bar {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-top: 10px;
        }
        .cp-order-thumb {
            width: 58px;
            height: 58px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            overflow: hidden;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            font-size: 10px !important;
            color: #94a3b8;
        }
        .cp-order-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cp-order-info-text {
            flex: 1;
            min-width: 0;
        }
        .cp-order-info-text h4 {
            margin: 0 0 2px;
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .cp-order-info-text p {
            margin: 0;
            font-size: 12px !important;
            color: #64748b;
        }
        .cp-order-info-text small {
            font-size: 11.5px !important;
            color: #94a3b8;
        }
        .cp-order-price-bar {
            text-align: right;
            flex-shrink: 0;
        }
        .cp-order-price-bar span {
            display: block;
            font-size: 12.5px !important;
            color: #475569;
        }
        .cp-order-price-bar strong {
            color: #d92332 !important;
            font-weight: 800 !important;
            font-size: 14px !important;
        }
        .cp-view-order-btn {
            background: none;
            border: 0;
            color: #1240f7;
            font-size: 12px !important;
            font-weight: 700 !important;
            cursor: pointer;
            padding: 2px 0 0;
        }
        /* WARRANTY ROW */
        .cp-warranty-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .cp-warranty-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }
        .cp-warranty-thumb {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            overflow: hidden;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            font-size: 10px !important;
            color: #94a3b8;
        }
        .cp-warranty-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cp-warranty-meta {
            flex: 1;
            min-width: 0;
        }
        .cp-warranty-meta h4 {
            margin: 0 0 3px;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #0f172a;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .cp-warranty-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .cp-sku-tag {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11.5px !important;
            font-weight: 700 !important;
        }
        .cp-var-tag {
            padding: 1px 6px;
            border-radius: 5px;
            background: #f1f5f9;
            color: #475569;
            font-size: 11px !important;
            font-weight: 600 !important;
        }
        .cp-warranty-badge-box {
            text-align: right;
            padding: 6px 12px;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .cp-warranty-badge-box.is-active {
            background: #ecfdf5;
            color: #047857;
        }
        .cp-warranty-badge-box.is-expired {
            background: #f1f5f9;
            color: #64748b;
        }
        .cp-warranty-badge-box span {
            display: block;
            font-size: 11.5px !important;
        }
        .cp-warranty-badge-box small {
            font-size: 11px !important;
            font-weight: 800 !important;
        }
        /* WIDGETS */
        .cp-widget-card h3 {
            margin: 0 0 10px;
            font-size: 15px !important;
            font-weight: 800 !important;
        }
        .cp-gift-box {
            text-align: center;
            padding: 16px 10px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
        }
        .cp-gift-icon {
            font-size: 32px;
            margin-bottom: 6px;
        }
        .cp-gift-box p {
            font-size: 12.5px !important;
            color: #64748b;
            margin: 0 0 12px;
            line-height: 1.5;
        }
        .cp-gift-btn {
            display: inline-block;
            padding: 7px 16px;
            border-radius: 99px;
            background: #1240f7;
            color: #fff !important;
            font-size: 12.5px !important;
            font-weight: 700 !important;
            text-decoration: none;
        }
        .cp-support-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 12px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            text-decoration: none;
            font-size: 12.5px !important;
            margin-bottom: 6px;
        }
        .cp-support-line strong {
            color: #1240f7;
        }
        /* TAB 2 & 3: FULL VIEWS */
        .cp-filter-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .cp-search-wrap {
            min-width: 280px;
            max-width: 380px;
            width: 100%;
        }
        .cp-search-wrap input {
            width: 100%;
            height: 40px;
            padding: 0 16px;
            border-radius: 99px;
            border: 1px solid #cbd5e1;
            font-size: 13.5px !important;
            outline: none;
            background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .cp-search-wrap input:focus, .cp-warranty-search-bar input:focus {
            border-color: #d70018;
            box-shadow: 0 0 0 3px rgba(215, 0, 24, 0.12);
        }
        .cp-warranty-search-bar {
            margin-bottom: 18px;
            width: 100%;
        }
        .cp-warranty-search-bar input {
            width: 100%;
            height: 42px;
            padding: 0 16px;
            border-radius: 99px;
            border: 1px solid #cbd5e1;
            font-size: 13.5px !important;
            outline: none;
            background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        /* CELLPHONES MODERN ORDERS & WARRANTY STYLES */
        .cp-cell-panel {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            overflow: hidden;
        }
        .cp-cell-tabs-bar {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 0 16px;
            gap: 4px;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .cp-cell-tabs-bar::-webkit-scrollbar {
            display: none;
        }
        .cp-cell-tab-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 14px 16px;
            background: transparent;
            border: 0;
            border-bottom: 2px solid transparent;
            color: #475569;
            font-size: 14px !important;
            font-weight: 500 !important;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s ease;
            text-decoration: none !important;
        }
        .cp-cell-tab-item:hover {
            color: #d70018;
        }
        .cp-cell-tab-item.is-active {
            color: #d70018 !important;
            font-weight: 700 !important;
            border-bottom: 2px solid #d70018 !important;
        }
        .cp-cell-tab-badge {
            font-size: 12px !important;
            color: #94a3b8;
            font-weight: 600 !important;
        }
        .cp-cell-tab-item.is-active .cp-cell-tab-badge {
            color: #d70018;
        }
        .cp-cell-subhead-row {
            padding: 16px 20px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            border-bottom: 1px solid #f8fafc;
        }
        .cp-cell-section-title {
            margin: 0;
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #0f172a;
        }
        .cp-cell-filter-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .cp-cell-datepicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 5px 10px;
            background: #ffffff;
        }
        .cp-cell-date-field {
            border: 0;
            outline: none;
            font-size: 12.5px !important;
            color: #334155;
            background: transparent;
            cursor: pointer;
        }
        .cp-cell-date-sep {
            color: #94a3b8;
            font-size: 12px !important;
        }
        .cp-cell-date-icon {
            color: #64748b;
            flex-shrink: 0;
        }
        .cp-cell-search-box {
            position: relative;
            display: inline-flex;
            align-items: center;
        }
        .cp-cell-search-input {
            width: 220px;
            height: 34px;
            padding: 0 12px 0 32px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 12.5px !important;
            outline: none;
            background: #ffffff;
            transition: all 0.15s ease;
        }
        .cp-cell-search-input:focus {
            border-color: #d70018;
            box-shadow: 0 0 0 2px rgba(215, 0, 24, 0.12);
        }
        .cp-cell-search-icon {
            position: absolute;
            left: 10px;
            color: #94a3b8;
            pointer-events: none;
        }
        .cp-cell-orders-container {
            padding: 16px 20px 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .cp-cell-order-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #ffffff;
            overflow: hidden;
            padding: 14px 16px;
            margin-bottom: 12px;
            transition: border-color 0.15s ease;
        }
        .cp-cell-order-card:hover {
            border-color: #cbd5e1;
        }
        .cp-cell-order-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            background: transparent;
            border-bottom: 1px solid #f3f4f6;
            margin-bottom: 12px;
            font-size: 13.5px !important;
            color: #4b5563;
        }
        .cp-cell-order-meta b {
            color: #111827;
            font-weight: 700 !important;
        }
        .cp-cell-dot {
            margin: 0 6px;
            color: #9ca3af;
        }
        .cp-cell-status-pill {
            font-size: 12px !important;
            font-weight: 500 !important;
            padding: 2px 8px;
            border-radius: 4px;
            white-space: nowrap;
        }
        .cp-cell-status-pill.is-delivered {
            background: #ecfdf5;
            color: #16a34a;
        }
        .cp-cell-status-pill.is-shipping {
            background: #eff6ff;
            color: #2563eb;
        }
        .cp-cell-status-pill.is-processing {
            background: #fffbeb;
            color: #d97706;
        }
        .cp-cell-status-pill.is-cancelled {
            background: #fef2f2;
            color: #dc2626;
        }
        .cp-cell-status-pill.is-pending {
            background: #f3f4f6;
            color: #4b5563;
        }
        .cp-cell-order-body {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }
        .cp-cell-order-items {
            flex: 1;
            min-width: 240px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 0;
        }
        .cp-cell-item-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 0;
            border: none;
        }
        .cp-cell-item-thumb {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            border: 1px solid #f3f4f6;
            background: #ffffff;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .cp-cell-item-thumb img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .cp-cell-item-info {
            flex: 1;
            min-width: 0;
        }
        .cp-cell-item-name {
            margin: 0 0 4px;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #111827;
            line-height: 1.35;
        }
        .cp-cell-item-var {
            font-size: 12px !important;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .cp-cell-item-price {
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #111827;
        }
        .cp-cell-order-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
            flex-shrink: 0;
            text-align: right;
        }
        .cp-cell-total-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            font-size: 13.5px !important;
        }
        .cp-cell-total-lbl {
            color: #4b5563;
        }
        .cp-cell-total-amt {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #d70018 !important;
        }
        .cp-cell-detail-link {
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #4b5563 !important;
            text-decoration: none !important;
        }
        .cp-cell-detail-link:hover {
            color: #d70018 !important;
            text-decoration: underline !important;
        }
        /* TAB 3 SPECIFIC: NOTICE BANNER, EMPTY STATE, CLAIM PROGRESS */
        .cp-warranty-notice-banner {
            margin: 16px 20px;
            padding: 12px 16px;
            background: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .cp-warranty-notice-content {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px !important;
            color: #991b1b;
        }
        .cp-warranty-notice-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 14px;
            border-radius: 6px;
            background: #d70018;
            color: #ffffff !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            transition: background 0.15s ease;
        }
        .cp-warranty-notice-btn:hover {
            background: #b91c1c;
        }
        .cp-cell-empty-rma {
            padding: 56px 24px 64px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .cp-cell-empty-icon-wrap {
            margin-bottom: 4px;
        }
        .cp-cell-empty-title {
            margin: 0;
            font-size: 15px !important;
            font-weight: 600 !important;
            color: #64748b;
        }
        .cp-cell-empty-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px !important;
        }
        .cp-cell-empty-home-link {
            color: #d70018;
            font-weight: 600 !important;
            text-decoration: none !important;
        }
        .cp-cell-empty-home-link:hover {
            text-decoration: underline !important;
        }
        .cp-cell-empty-sec-link {
            color: #475569;
            text-decoration: none !important;
        }
        .cp-cell-empty-sec-link:hover {
            color: #0f172a;
            text-decoration: underline !important;
        }
        .cp-cell-claim-progress-box {
            padding: 14px 20px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }
        .cp-cell-progress-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .cp-cell-progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            flex: 1;
            text-align: center;
        }
        .cp-cell-step-dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            font-size: 11px !important;
            font-weight: 700 !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cp-cell-progress-step.is-done .cp-cell-step-dot {
            background: #d70018;
            color: #ffffff;
        }
        .cp-cell-step-title {
            font-size: 11px !important;
            color: #64748b;
            font-weight: 500 !important;
        }
        .cp-cell-progress-step.is-done .cp-cell-step-title {
            color: #0f172a;
            font-weight: 700 !important;
        }
        /* FORMS */
        .cp-form {
            max-width: 520px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 8px;
        }
        .cp-form-group label {
            display: block;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .cp-form-group label b {
            color: #dc2626;
        }
        .cp-form-group input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            font-size: 14px !important;
            outline: none;
            background: #fff;
        }
        .cp-form-group input:focus {
            border-color: #1240f7;
            box-shadow: 0 0 0 3px #eef1ff;
        }
        .cp-err {
            color: #dc2626;
            font-size: 12px !important;
            margin-top: 3px;
            display: block;
        }
        .cp-alert-success {
            padding: 11px 16px;
            border-radius: 10px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            margin-bottom: 14px;
        }
        /* BUTTONS */
        .cp-cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            padding: 0 20px;
            border-radius: 99px;
            background: #1240f7;
            color: #fff !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(18, 64, 247, 0.25);
            transition: all .15s;
        }
        .cp-cta-btn:hover {
            background: #0c2fbf;
        }
        .cp-secondary-btn {
            display: inline-flex;
            align-items: center;
            height: 42px;
            padding: 0 16px;
            border-radius: 99px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155 !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            text-decoration: none;
        }
        .cp-actions-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .cp-empty-state {
            padding: 36px 18px;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            text-align: center;
            color: #64748b;
            font-size: 13.5px !important;
        }
    </style>
</div>
