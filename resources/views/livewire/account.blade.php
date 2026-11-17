<div class="cp-page">
    <link rel="stylesheet" href="{{ asset('css/account-center.css') }}?v={{ filemtime(public_path('css/account-center.css')) }}">
    <title>Trung tâm thành viên &amp; Bảo hành | The BABU Store</title>
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
            <section class="cp-main" wire:key="account-tab-{{ $activeTab }}" data-account-tab="{{ $activeTab }}">
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
                                        <div class="cp-order-row" wire:key="overview-order-{{ $order->id }}">
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
                                <article class="cp-cell-order-card" wire:key="purchase-order-{{ $order->id }}">
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
                                                <div class="cp-cell-item-row" wire:key="purchase-item-{{ $item->id }}">
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
                                                <div class="cp-cell-item-row" wire:key="claim-item-{{ $cItem->id }}">
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


</div>
