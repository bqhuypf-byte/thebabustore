<div class="ss-page bf-page">
    @livewire('inc.header')
    <main class="bf-container">
        <nav class="ss-breadcrumb" aria-label="Đường dẫn"><a href="/">Trang chủ</a><span aria-hidden="true">/</span><span aria-current="page">{{ $campaign->name }}</span></nav>
        @if($campaign->banner_image)
        <a class="bf-hero" href="#black-friday-products" aria-label="Xem sản phẩm {{ $campaign->name }}"><picture>@if($campaign->mobile_banner_image)<source media="(max-width:600px)" srcset="{{ Storage::url($campaign->mobile_banner_image) }}">@endif<img src="{{ Storage::url($campaign->banner_image) }}" alt="{{ $campaign->headline }}" fetchpriority="high" width="2160" height="720"></picture></a>
        @endif
        <div class="bf-event-content">
        <div class="bf-highlights">@if($lowestPrice !== null)<div><span>GIÁ CHỈ TỪ</span><strong>{{ number_format($lowestPrice,0,',','.') }}<small>₫</small></strong></div>@endif<div><span>BLACK FRIDAY</span><strong>DEAL GAMING GEAR</strong></div></div>
        <header class="bf-heading"><div><p class="bf-eyebrow">THE BABU STORE / ƯU ĐÃI</p><h1>{{ $campaign->headline }}</h1>@if($campaign->intro)<p class="bf-intro">{{ $campaign->intro }}</p>@endif</div>@if($campaign->ends_at)<p class="bf-end">Kết thúc {{ $campaign->ends_at->copy()->timezone('Asia/Bangkok')->format('d/m/Y H:i') }}</p>@endif</header>
        <section id="black-friday-products" class="bf-products" aria-labelledby="bf-products-title">
            <div class="bf-section-heading"><h2 id="bf-products-title">Sản phẩm ưu đãi</h2><span>{{ $items->total() }} sản phẩm</span></div>
            <div class="bf-filters" role="group" aria-label="Lọc danh mục ưu đãi"><button type="button" wire:click="$set('category', '')" class="{{ $category === '' ? 'active' : '' }}" aria-pressed="{{ $category === '' ? 'true' : 'false' }}">Tất cả</button>@foreach($categories as $group)<button type="button" wire:click="$set('category', '{{ $group->slug }}')" class="{{ $category === $group->slug ? 'active' : '' }}" aria-pressed="{{ $category === $group->slug ? 'true' : 'false' }}">{{ $group->name }}</button>@endforeach</div>
            @error('cardPurchase')<p class="purchase-error" role="alert">{{ $message }}</p>@enderror
            @if($items->isNotEmpty())
                @php $dealGroups=$items->getCollection()->groupBy(function($item) { $minimum=$item->product->variants->filter(fn($variant)=>\App\Support\PurchaseValidation::available($variant))->min('price'); return $minimum < 500000 ? 'accessible' : 'upgrade'; })->sortKeys(); @endphp
                @foreach($dealGroups as $dealKey => $dealItems)
                <section class="bf-deal-section" aria-labelledby="bf-deal-{{ $dealKey }}">
                    <div class="bf-deal-heading"><p>{{ $dealKey === 'accessible' ? 'GIÁ TỐT CHO GÓC MÁY' : 'NÂNG CẤP TRẢI NGHIỆM' }}</p><h3 id="bf-deal-{{ $dealKey }}">{{ $dealKey === 'accessible' ? 'Deal dưới 500K' : 'Gear nâng cấp' }}</h3><span>{{ $dealKey === 'accessible' ? 'Khám phá sản phẩm có phiên bản còn hàng dưới 500.000₫.' : 'Bàn phím, chuột và thiết bị cho lựa chọn nâng cấp của bạn.' }}</span></div>
                    <div class="bf-grid">@include('livewire.partials.campaign-deal-cards')</div>
                </section>
                @endforeach
                <div class="ss-pagination">{{ $items->links('livewire.store-pagination') }}</div>
            @else<div class="bf-empty"><h3>Sản phẩm ưu đãi đang được cập nhật</h3><p>{{ $category ? 'Thử danh mục khác để xem thêm sản phẩm trong chiến dịch.' : 'Khám phá thêm các thiết bị đang có tại cửa hàng.' }}</p><a href="/shop">Khám phá cửa hàng</a></div>@endif
        </section>
        @if($campaign->terms)<section class="bf-terms" aria-labelledby="bf-terms-title"><h2 id="bf-terms-title">Điều kiện chương trình</h2><p>{{ $campaign->terms }}</p></section>@endif
        </div>
    </main>
    @livewire('inc.footer')
    @livewire('product-quick-view')
    <link rel="stylesheet" href="{{ asset('css/promotion-campaign.css') }}?v={{ filemtime(public_path('css/promotion-campaign.css')) }}">
</div>
