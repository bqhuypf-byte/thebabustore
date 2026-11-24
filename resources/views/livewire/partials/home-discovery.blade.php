@php
    $discoverySettings = \App\Models\Setting::query()->first();
    $discoverySections = collect($discoverySettings?->homepage_sections ?? []);
    $setupSection = $discoverySections->firstWhere('layout', 'setup');
    $setupItems = collect($setupSection['items'] ?? [])->filter(fn ($item) => !empty($item['image']))->take(3);
    $homeBrands = \App\Models\Brand::query()->whereHas('products', fn ($q) => $q->where('is_active', true)->whereHas('variants', fn ($v) => $v->where('stock', 'in_stock')))->orderBy('name')->take(6)->get();
@endphp
@if($homeBrands->isNotEmpty())
<section class="home-discovery home-brands" aria-labelledby="home-brands-title">
    <div class="home-proposal-heading"><div><h2 id="home-brands-title">Thương hiệu</h2><p>Khám phá các thương hiệu tại BABU Store.</p></div><a href="/shop">Xem tất cả</a></div>
    <link rel="stylesheet" href="{{ asset('css/home-brand-logos.css') }}?v={{ filemtime(public_path('css/home-brand-logos.css')) }}">
    <div class="home-brand-grid">
        @foreach($homeBrands as $brand)
            @php
                $brandAsset = config('brand-logos.'.\Illuminate\Support\Str::slug($brand->name));
                $brandLogo = filled($brand->logo) ? Storage::url($brand->logo) : ($brandAsset ? asset('images/brands/'.$brandAsset['file']) : null);
                $brandBackground = !filled($brand->logo) && $brandAsset ? $brandAsset['background'] : '#fff';
            @endphp
            <a href="/shop?search={{ urlencode($brand->name) }}" aria-label="{{ $brand->name }}" style="--brand-tile-background:{{ $brandBackground }}">
                @if($brandLogo)<img src="{{ $brandLogo }}" alt="{{ $brand->name }}" loading="lazy" width="160" height="48">@else<span class="home-brand-missing-logo">{{ $brand->name }}</span>@endif
            </a>
        @endforeach
    </div>
</section>
@endif
@if(($setupSection['enabled'] ?? true) && $setupItems->isNotEmpty())
<section class="home-discovery home-setups" aria-labelledby="home-setups-title">
    <div class="home-proposal-heading"><div><h2 id="home-setups-title">{{ $setupSection['title'] ?? 'Góc setup của khách' }}</h2><p>{{ $setupSection['body'] ?? 'Chia sẻ setup của bạn với #BabuSetup' }}</p></div><a href="/contact">Chia sẻ setup</a></div>
    <div class="home-setup-layout"><div class="home-setup-gallery">
        @foreach($setupItems->values() as $setupIndex => $setupItem)

            <div class="home-setup-tile">@if($setupItem)<img src="{{ Storage::url($setupItem['image']) }}" alt="{{ $setupItem['title'] ?? 'Góc setup' }}" loading="lazy">@else<span>Ảnh setup đang cập nhật</span>@endif</div>
        @endforeach
    </div><div class="home-setup-note"><h3>Góc máy của bạn, cảm hứng của cộng đồng</h3><p>Gửi hình ảnh và chia sẻ trải nghiệm setup của bạn cùng BABU.</p><a href="/contact">Gửi chia sẻ của bạn</a></div></div>
</section>
@endif