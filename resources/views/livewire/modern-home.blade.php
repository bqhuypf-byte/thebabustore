<div class="ms-root">
@php
    $modernCategories = \App\Models\Category::query()->where('is_active', 1)->withCount(['product_cat as active_products_count' => fn ($query) => $query->where('is_active', 1)])->orderBy('ordinal_number')->take(4)->get();
    $saleProducts = \App\Models\Product::query()->with('variants')->where('is_active', 1)->where('on_sale', 1)->whereHas('variants', fn ($query) => $query->where('stock', 'in_stock'))->latest('id')->take(4)->get();
@endphp

<div class="modern-storefront mc-home">
    @livewire('inc.header')

    <main>
        @include('livewire.partials.home-hero')
        <section class="ms-section" id="modern-categories" aria-label="Danh mục sản phẩm">
            <div class="ms-category-grid">
                @foreach($modernCategories as $index => $category)
                    @php $categoryImage = $category->banner ?: $category->image; @endphp
                    <a class="ms-category-card" href="/shop?category={{ urlencode($category->slug) }}">
                        <span class="ms-category-top"><span class="ms-category-icon" aria-hidden="true">@if($categoryImage)<img src="{{ asset('storage/'.$categoryImage) }}" alt="">@else{{ ['⌨', '◉', '♬', '◇', '▦', '▧'][$index % 6] }}@endif</span><span class="ms-category-count">{{ $category->active_products_count }} mẫu</span></span>
                        <span class="ms-category-name"><b>{{ $category->name }}</b><span aria-hidden="true">→</span></span><span class="ms-gear-cta">Khám phá <span aria-hidden="true">→</span></span>
                    </a>
                @endforeach
            </div>
        </section>

        @include('livewire.partials.home-product-carousel')

        <section class="ms-section ms-flash-section" aria-labelledby="flash-title">
            <div class="ms-flash">
                <div class="ms-flash-heading"><div><span class="ms-flash-kicker">⚡ ƯU ĐÃI NỔI BẬT</span><h2 id="flash-title">Flash Sale hôm nay</h2><p>Khám phá ưu đãi gaming gear từ The BABU Store</p></div><a href="/shop?sort=discount">Xem tất cả ưu đãi →</a></div>
                @if($saleProducts->isNotEmpty())
                    <div class="ms-flash-grid">
                        @foreach($saleProducts as $saleProduct)
                            @php $saleVariant = $saleProduct->variants->firstWhere('stock', 'in_stock') ?: $saleProduct->variants->first(); $saleImage = $saleVariant?->image ?: (($saleProduct->images ?: [])[0] ?? null); @endphp
                            <article class="ms-flash-card">
                                <a class="ms-flash-image" href="/product/{{ $saleProduct->slug }}">@if($saleImage)<img src="{{ asset('storage/'.$saleImage) }}" alt="{{ $saleProduct->name }}">@endif</a>
                                <div class="ms-flash-body"><a href="/product/{{ $saleProduct->slug }}">{{ $saleProduct->name }}</a><strong>{{ number_format($saleVariant?->price ?? $saleProduct->price, 0, ',', '.') }}₫</strong><button type="button" wire:click="openPreview({{ $saleProduct->id }})">Chọn phiên bản</button></div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="ms-flash-empty"><strong>Ưu đãi mới đang được cập nhật</strong><span>Trong lúc chờ đợi, khám phá các sản phẩm gaming gear của chúng tôi.</span><a href="/shop">Khám phá cửa hàng →</a></div>
                @endif
            </div>
        </section>

        @include('livewire.partials.home-editorial')
    </main>

    @livewire('inc.footer')
    <link rel="stylesheet" href="{{ asset('css/home-mchose.css') }}?v={{ filemtime(public_path('css/home-mchose.css')) }}">
</div>

@if($previewProduct)
    @php
        $previewImages = $previewProduct->images ?: [];
        $previewVariant = $previewProduct->variants->firstWhere('id', $previewVariantId);
        $previewGallery = collect($previewImages)->merge($previewProduct->variants->pluck('image'))->filter()->unique()->values();
        $previewImage = $previewGallery->get((int) $previewImageIndex) ?: $previewGallery->first();
    @endphp
    <div class="ms-preview-overlay" wire:click.self="closePreview" x-data @keydown.escape.window="$wire.closePreview()">
        <section class="ms-preview" role="dialog" aria-modal="true" aria-labelledby="preview-title">
            <button class="ms-preview-close" type="button" wire:click="closePreview" aria-label="Đóng">×</button>
            <div class="ms-preview-gallery">
                <div class="ms-preview-image">
                    @if($previewImage)<img src="{{ asset('storage/'.$previewImage) }}" alt="{{ $previewProduct->name }}">@endif
                </div>
                @if($previewGallery->count() > 1)
                    <div class="ms-preview-thumbnails">
                        @foreach($previewGallery as $index => $galleryImage)
                            <button type="button" wire:click="$set('previewImageIndex', {{ $index }})" class="{{ $previewImageIndex == $index ? 'selected' : '' }}" aria-label="Ảnh sản phẩm {{ $index + 1 }}" aria-pressed="{{ $previewImageIndex == $index ? 'true' : 'false' }}"><img src="{{ asset('storage/'.$galleryImage) }}" alt=""></button>
                        @endforeach
                    </div>
                @endif
                <p class="ms-preview-image-caption">Hình ảnh sản phẩm tại The BABU Store</p>
            </div>
            <div class="ms-preview-details">
                <div class="ms-preview-top"><span>{{ $previewProduct->category?->name }}</span><a href="/product/{{ $previewProduct->slug }}">Xem trang chi tiết →</a></div>
                <h2 id="preview-title">{{ $previewProduct->name }}</h2>
                <p>Thông tin sản phẩm</p>
                <div class="ms-preview-price">{{ number_format($previewVariant?->price ?? $previewProduct->price, 0, ',', '.') }}₫</div>
                <div class="ms-preview-variant-heading"><h3>Chọn phiên bản / Biến thể:</h3></div>
                <div class="ms-preview-variants">
                    @forelse($previewProduct->variants as $variant)
                        <button type="button" wire:click="selectPreviewVariant({{ $variant->id }})" @disabled($variant->stock !== 'in_stock') aria-pressed="{{ $previewVariantId == $variant->id ? 'true' : 'false' }}" class="{{ $previewVariantId == $variant->id ? 'selected' : '' }}">@if($variant->image)<img class="ms-variant-image" src="{{ asset('storage/'.$variant->image) }}" alt="">@elseif($previewGallery->first())<img class="ms-variant-image" src="{{ asset('storage/'.$previewGallery->first()) }}" alt="">@endif<span>{{ $variant->name }}<small>{{ number_format($variant->price, 0, ',', '.') }}₫</small></span></button>
                    @empty
                        <p>Sản phẩm chưa có phiên bản để đặt hàng.</p>
                    @endforelse
                </div>
                @error('previewVariantId') <p class="ms-preview-error">{{ $message }}</p> @enderror
                @if($previewProduct->short_description)
                    <h3>Thông tin nổi bật:</h3>
                    <p>{{ strip_tags($previewProduct->short_description) }}</p>
                @endif
                <div class="ms-preview-quantity"><span>Số lượng:</span><button type="button" wire:click="changePreviewQuantity(-1)" aria-label="Giảm số lượng">−</button><span>{{ $previewQuantity }}</span><button type="button" wire:click="changePreviewQuantity(1)" aria-label="Tăng số lượng">+</button></div>
                <div class="ms-preview-actions"><button type="button" wire:click="addPreviewToCart" @disabled(!$previewVariant)>Thêm vào giỏ</button><button type="button" wire:click="addPreviewToCart(true)" @disabled(!$previewVariant)>Mua ngay</button></div>
                <p class="ms-preview-benefits">Bảo hành · Giao hàng toàn quốc · Hỗ trợ tận tâm</p>
            </div>
        </section>
    </div>
@endif
@if(session()->has('preview_cart_message')) <div class="ms-preview-toast" role="status">{{ session('preview_cart_message') }}</div> @endif

<style>
    @font-face { font-family: Geist; src: url('/fonts/Geist-Variable.woff2') format('woff2'); font-weight: 100 900; font-display: swap; }
    :root {
        --ms-ink: #111316;
        --ms-muted: #6f7378;
        --ms-line: #e8e8e5;
        --ms-lime: #d8ff45;
        --ms-paper: #f6f6f1;
    }
    .modern-storefront {
        font-family: Geist, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: var(--ms-ink);
        background: #fff;
        min-height: 100vh;
    }
    .modern-storefront *, .ms-preview-overlay * { font-family: inherit; }
    .ms-promo {
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--ms-ink);
        color: #fff;
        font-size: 13px;
        letter-spacing: .02em;
    }
    .ms-promo strong {
        color: var(--ms-lime);
        margin: 0 4px;
    }
    .ms-header {
        height: 78px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 30px;
        padding: 0 max(28px, calc((100vw - 1320px) / 2));
        border-bottom: 1px solid var(--ms-line);
        position: sticky;
        top: 0;
        background: rgba(255, 255, 255, .96);
        backdrop-filter: blur(16px);
        z-index: 50;
    }
    .ms-brand {
        display: flex;
        align-items: center;
        gap: 11px;
        color: inherit;
        text-decoration: none;
        width: max-content;
    }
    .ms-brand-mark {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--ms-lime);
        display: grid;
        place-items: center;
        font-weight: 900;
        font-size: 22px;
    }
    .ms-brand b, .ms-brand small {
        display: block;
        line-height: 1.1;
    }
    .ms-brand b {
        font-size: 16px;
        letter-spacing: .05em;
    }
    .ms-brand small {
        font-size: 9px;
        letter-spacing: .15em;
        color: var(--ms-muted);
        margin-top: 3px;
    }
    .ms-nav {
        display: flex;
        gap: 28px;
        justify-content: center;
    }
    .ms-nav a, .ms-footer a {
        font-size: 14px;
        color: inherit;
        text-decoration: none;
        font-weight: 600;
    }
    .ms-nav a:hover, .ms-footer a:hover {
        opacity: .65;
    }
    .ms-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }
    .ms-actions a {
        width: 38px;
        height: 38px;
        border: 1px solid var(--ms-line);
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: inherit;
        text-decoration: none;
        font-size: 18px;
    }
    .ms-actions .ms-cart {
        width: auto;
        border-radius: 99px;
        padding: 0 16px;
        font-size: 13px;
        background: var(--ms-ink);
        color: #fff;
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .ms-cart-badge {
        background: var(--ms-lime);
        color: #000;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 99px;
    }
    .ms-hero {
        max-width: 1320px;
        margin: 24px auto 0;
        min-height: 320px;
        border-radius: 24px;
        background: #fbfbf9;
        display: grid;
        grid-template-columns: 1.2fr .8fr;
        overflow: hidden;
        border: 1px solid var(--ms-line);
    }
    .ms-hero-copy {
        padding: 60px 50px;
        display: flex;
        align-items: center;
    }
    .ms-stats {
        display: flex;
        gap: 48px;
    }
    .ms-stats span {
        font-size: 11px;
        text-transform: uppercase;
        color: var(--ms-muted);
        letter-spacing: .08em;
    }
    .ms-stats b {
        display: block;
        color: var(--ms-ink);
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 6px;
        letter-spacing: -.03em;
    }
    .ms-hero-art {
        position: relative;
        background: var(--ms-lime);
        display: grid;
        place-items: center;
        overflow: hidden;
    }
    .ms-orbit {
        position: absolute;
        border: 1px solid rgba(17, 19, 22, .12);
        border-radius: 50%;
    }
    .ms-orbit-a { width: 480px; height: 480px; }
    .ms-orbit-b { width: 320px; height: 320px; }
    .ms-floating {
        position: relative;
        z-index: 2;
        background: #fff;
        border-radius: 14px;
        padding: 14px 20px;
        font-size: 10px;
        letter-spacing: .12em;
        box-shadow: 0 15px 35px rgba(0,0,0,.1);
        text-align: center;
    }
    .ms-floating b {
        font-size: 18px;
        letter-spacing: 0;
    }
    .ms-section {
        max-width: 1320px;
        margin: 80px auto;
        padding: 0 16px;
    }
    .ms-section-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 28px;
    }
    .ms-eyebrow {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--ms-muted);
        display: block;
        margin-bottom: 8px;
    }
    .ms-section-head h2 {
        font-size: 36px;
        letter-spacing: -.04em;
        margin: 0;
        font-weight: 800;
    }
    .ms-section-head > a {
        color: inherit;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }
    .ms-category-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .ms-category-card {
        height: 280px;
        border-radius: 18px;
        position: relative;
        overflow: hidden;
        background: #222;
        color: #fff;
        text-decoration: none;
    }
    .ms-category-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: .4s ease;
    }
    .ms-category-card:hover img {
        transform: scale(1.05);
    }
    .ms-category-shade {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,.1), rgba(0,0,0,.7));
    }
    .ms-category-number {
        position: absolute;
        top: 18px;
        left: 20px;
        font-size: 12px;
        opacity: .8;
    }
    .ms-category-name {
        position: absolute;
        left: 22px;
        bottom: 22px;
    }
    .ms-category-name b {
        font-size: 22px;
        display: block;
    }
    .ms-category-name small {
        margin-top: 6px;
        font-size: 12px;
        opacity: .85;
        display: block;
    }
    .ms-product-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    .ms-product-card {
        background: #fbfbf9;
        border: 1px solid var(--ms-line);
        border-radius: 16px;
        padding: 12px;
        transition: .25s ease;
    }
    .ms-product-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,.06);
    }
    .ms-product-image {
        height: 260px;
        border-radius: 12px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        text-decoration: none;
    }
    .ms-product-image img {
        width: 85%;
        height: 85%;
        object-fit: contain;
        transition: .35s;
    }
    .ms-product-image > a { display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; }
    .ms-quick { border: 0; cursor: pointer; z-index: 2; }
    .ms-preview-overlay { position: fixed; inset: 0; z-index: 9998; background: rgba(0,0,0,.8); display: grid; place-items: center; padding: 20px; }
    .ms-preview { position: relative; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; width: min(896px, 100%); max-height: 90vh; overflow: auto; background: #fff; border-radius: 12px; padding: 32px; color: #171717; box-shadow: 0 20px 60px #0004; }
    .ms-preview, .ms-preview * { font-family: 'Geist', sans-serif; box-sizing: border-box; }
    .ms-preview-close { position: absolute; top: 12px; right: 16px; border: 0; background: transparent; font-size: 24px; cursor: pointer; }
    .ms-preview-image { border-radius: 16px; overflow: hidden; background: #f5f5f5; aspect-ratio: 1; }
    .ms-preview-image img { width: 100%; height: 100%; object-fit: contain; }
    .ms-preview-top { display: flex; justify-content: space-between; color: #777; font-size: 12px; gap: 8px; }
    .ms-preview-top a { color: inherit; }
    .ms-preview-details h2 { margin: 16px 0 8px; font-size: 24px; line-height: 1.2; font-weight: 700; }
    .ms-preview-details h3 { font-size: 13px; font-weight: 650; margin: 18px 0 10px; }
    .ms-preview-details p { color: #666; font-size: 12px; line-height: 1.8; }
    .ms-preview-price { border: 1px solid #e5e5e5; border-radius: 16px; padding: 16px; font-size: 28px; font-weight: 700; }
    .ms-preview-variants { display: flex; flex-wrap: wrap; gap: 8px; }
    .ms-preview-variants button { border: 1px solid #ddd; border-radius: 9px; background: white; padding: 10px; cursor: pointer; }
    .ms-preview-variants button.selected { border: 2px solid #111; background: #f5f5f5; }
    .ms-preview-variants button:disabled { opacity: .45; cursor: not-allowed; }
    .ms-preview-quantity { display: flex; gap: 12px; align-items: center; margin: 16px 0; font-size: 13px; }
    .ms-preview-quantity button { border: 1px solid #ddd; border-radius: 50%; background: #fafafa; width: 30px; height: 30px; cursor: pointer; }
    .ms-preview-actions { display: flex; gap: 8px; border-top: 1px solid #eee; padding-top: 12px; }
    .ms-preview-actions button { flex: 1; border: 1px solid #ddd; background: #fff; padding: 12px; border-radius: 30px; cursor: pointer; }
    .ms-preview-actions button:last-child { background: #111; color: #fff; }
    .ms-preview-actions button:disabled { opacity: .45; cursor: not-allowed; }
    .ms-preview-error { color: #b91c1c !important; }
    .ms-preview-benefits { text-align: center; }
    .ms-preview-toast { position: fixed; bottom: 24px; right: 24px; background: #111; color: #fff; padding: 14px 20px; border-radius: 10px; z-index: 9999; }
    @media(max-width: 700px) { .ms-preview { grid-template-columns: 1fr; padding: 24px; } .ms-preview-image { max-height: 35vh; } }
    .ms-product-card:hover img {
        transform: scale(1.05);
    }
    .ms-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: var(--ms-lime);
        color: #111;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
    }
    .ms-quick {
        position: absolute;
        left: 12px;
        right: 12px;
        bottom: 12px;
        background: #111;
        color: #fff;
        text-align: center;
        border-radius: 99px;
        padding: 10px;
        font-size: 12px;
        transform: translateY(50px);
        transition: .25s ease;
        opacity: 0;
    }
    .ms-product-card:hover .ms-quick {
        transform: none;
        opacity: 1;
    }
    .ms-product-card:focus-within .ms-quick { transform: none; opacity: 1; }
    .ms-product-info {
        padding: 12px 4px 4px;
    }
    .ms-product-info small {
        display: block;
        font-size: 10px;
        color: var(--ms-muted);
        letter-spacing: .1em;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .ms-product-info > a {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-decoration: none;
        color: inherit;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .ms-product-info b {
        font-size: 15px;
        color: var(--ms-ink);
    }
    .ms-product-info del {
        font-size: 12px;
        color: #aaa;
        margin-left: 6px;
    }
    /* Featured products and quick view follow the storefront reference. */
    .ms-products-section { max-width: 1152px; margin: 64px auto; padding: 0 16px; }
    .ms-products-section .ms-section-head { align-items: flex-end; gap: 20px; margin-bottom: 32px; }
    .ms-products-label { display: inline-flex; gap: 7px; align-items: center; padding: 3px 11px; margin-bottom: 8px; border: 1px solid #e5e5e5; border-radius: 99px; color: #171717; font-size: 12px; line-height: 16px; }
    .ms-products-label span { color: #ed8c18; font-size: 17px; }
    .ms-products-section .ms-section-head h2 { font-size: 30px; line-height: 1.2; letter-spacing: -.035em; font-weight: 700; }
    .ms-products-section .ms-section-head > a { flex-shrink: 0; display: inline-flex; align-items: center; gap: 10px; padding: 9px 16px; border: 1px solid #e5e5e5; border-radius: 99px; font-size: 12px; font-weight: 500; white-space: nowrap; }
    .ms-products-section .ms-section-head > a:hover { background: #f5f5f5; }
    .ms-product-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .ms-product-card { display: flex; flex-direction: column; min-width: 0; padding: 14px; background: #fff; border: 1px solid #e5e5e5; border-radius: 16px; box-shadow: 0 1px 2px #0000000d; transition: border-color .2s, box-shadow .2s; }
    .ms-product-card:hover, .ms-product-card:focus-within { border-color: #bdbdbd; box-shadow: 0 3px 8px #0000001a; }
    .ms-product-image { width: 100%; height: auto; aspect-ratio: 1; border-radius: 12px; background: #f6f6f6; }
    .ms-product-image img { width: 100%; height: 100%; object-fit: contain; transition: filter .2s ease, transform .25s ease; }
    .ms-product-card:hover .ms-product-image img, .ms-product-card:focus-within .ms-product-image img { filter: blur(4px); transform: scale(1.03); }
    .ms-badge { top: 8px; left: 8px; background: #ef4444; color: #fff; border-radius: 99px; padding: 2px 8px; font-size: 11px; line-height: 16px; }
    .ms-quick { left: 50%; right: auto; top: 50%; bottom: auto; display: inline-flex; align-items: center; justify-content: center; gap: 6px; width: max-content; padding: 8px 14px; background: #ffffffdc; color: #171717; font-size: 12px; font-weight: 500; box-shadow: 0 2px 8px #0002; transform: translate(-50%, -40%); }
    .ms-quick svg { width: 15px; height: 15px; }
    .ms-product-card:hover .ms-quick, .ms-product-card:focus-within .ms-quick { transform: translate(-50%, -50%); opacity: 1; }
    .ms-product-info { display: flex; flex: 1; flex-direction: column; padding: 12px 0 0; }
    .ms-product-info > a { min-height: 40px; margin: 0 0 10px; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 20px; font-size: 14px; font-weight: 600; }
    .ms-product-bottom { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: auto; padding-top: 10px; border-top: 1px solid #ededed; }
    .ms-product-price b { display: block; font-size: 15px; font-weight: 700; }
    .ms-product-price del { display: block; margin: 0; font-size: 11px; }
    .ms-product-cart { display: grid; place-items: center; flex-shrink: 0; width: 32px; height: 32px; padding: 8px; color: #fff; background: #171717; border: 0; border-radius: 50%; cursor: pointer; }
    .ms-product-cart:hover { background: #404040; }
    .ms-product-cart svg { width: 16px; height: 16px; }
    .ms-products-more { margin-top: 40px; text-align: center; }
    .ms-products-more a { display: inline-flex; align-items: center; gap: 12px; padding: 13px 28px; border-radius: 99px; color: #fff; background: #171717; font-size: 14px; font-weight: 600; text-decoration: none; }
    .ms-products-more a:hover { background: #383838; }
    .ms-preview-overlay { background: #000c; }
    .ms-preview { gap: 24px; padding: 32px; }
    .ms-preview-gallery { min-width: 0; }
    .ms-preview-thumbnails { display: flex; gap: 6px; margin-top: 12px; overflow-x: auto; }
    .ms-preview-thumbnails button { flex: 0 0 58px; width: 58px; height: 58px; padding: 2px; overflow: hidden; border: 1px solid #e5e5e5; border-radius: 10px; background: white; cursor: pointer; }
    .ms-preview-thumbnails button.selected { border-color: #171717; box-shadow: 0 0 0 1px #171717; }
    .ms-preview-thumbnails img { width: 100%; height: 100%; object-fit: cover; border-radius: 7px; }
    .ms-preview-image-caption { color: #777; text-align: center; font-size: 11px; margin: 16px 0 0; }
    .ms-preview-top span { padding: 3px 10px; border: 1px solid #e5e5e5; border-radius: 99px; }
    .ms-preview-variant-heading { display: flex; align-items: center; justify-content: space-between; gap: 8px; border-top: 1px solid #eee; margin-top: 12px; }
    .ms-preview-variant-heading small { color: #777; font-size: 11px; }
    .ms-preview { grid-template-columns: repeat(2, minmax(0, 1fr)); overflow-x: hidden; }
    .ms-preview-gallery, .ms-preview-details { min-width: 0; max-width: 100%; }
    .ms-preview-image { width: 100%; min-width: 0; max-width: 100%; }
    .ms-preview-image img { display: block; max-width: 100%; max-height: 100%; }
    .ms-preview-thumbnails { min-width: 0; max-width: 100%; }
    .ms-promo { height: 26px; font-size: 11px; background: #151515; }
    .ms-header { height: 64px; padding: 0 max(24px, calc((100vw - 1248px) / 2)); gap: 20px; }
    .ms-brand-mark { width: 34px; height: 34px; border-radius: 10px; background: #151515; color: white; font-size: 17px; }
    .ms-brand b { font-size: 13px; letter-spacing: -.02em; }
    .ms-brand small { text-transform: none; font-size: 10px; letter-spacing: 0; }
    .ms-nav { gap: 24px; }
    .ms-nav a { font-weight: 500; font-size: 12px; white-space: nowrap; }
    .ms-sale, .ms-new { padding: 2px 6px; border-radius: 99px; font-size: 9px; background: #fff0f2; color: #e11d48; }
    .ms-new { background: #e6f8ef; color: #15803d; }
    .ms-search { display: flex; align-items: center; gap: 8px; width: 220px; height: 34px; padding: 0 11px; color: #777; background: #f8f8f8; border: 1px solid #e8e8e8; border-radius: 99px; cursor: pointer; font-size: 13px; }
    .ms-search span { flex: 1; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 11px; }
    .ms-search kbd { font-size: 10px; padding: 2px 4px; border: 1px solid #ddd; border-radius: 5px; }
    .ms-actions .ms-cart { background: #151515; }
    .ms-cart-badge { background: #fff; }
    #modern-categories { max-width: 1152px; margin: 40px auto 64px; padding: 0 16px; }
    #modern-categories .ms-section-head { gap: 20px; align-items: flex-end; margin-bottom: 32px; }
    #modern-categories .ms-section-head h2 { font-size: 30px; line-height: 1.2; font-weight: 700; }
    #modern-categories .ms-section-head > a { font-size: 12px; font-weight: 500; white-space: nowrap; }
    .ms-category-label { display: inline-flex; gap: 8px; padding: 3px 11px; margin-bottom: 8px; border: 1px solid #e5e5e5; border-radius: 99px; font-size: 12px; line-height: 16px; }
    .ms-category-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
    .ms-category-card { display: flex; flex-direction: column; min-width: 0; height: 144px; padding: 20px; color: #171717; background: #fff; border: 1px solid #e5e5e5; border-radius: 16px; box-shadow: 0 1px 2px #0000000d; transition: border-color .2s, box-shadow .2s, transform .2s; }
    .ms-category-card:hover { border-color: #bbb; box-shadow: 0 4px 12px #00000010; transform: translateY(-2px); }
    .ms-category-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: auto; }
    .ms-category-icon { width: 76px; height: 56px; display: grid; place-items: center; overflow: hidden; border-radius: 10px; background: #f4f4f4; font-size: 20px; }
    .ms-category-card .ms-category-icon img { width: 100%; height: 100%; object-fit: cover; }
    .ms-category-count { padding: 3px 9px; color: #777; background: #f6f6f6; border: 1px solid #eee; border-radius: 99px; font-size: 10px; }
    .ms-category-name { position: static; display: flex; justify-content: space-between; align-items: center; }
    .ms-category-name b { font-size: 16px; font-weight: 650; }
    .ms-category-description { display: block; color: #777; font-size: 12px; margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ms-preview-variants button { display: inline-flex; align-items: center; gap: 8px; padding: 5px 10px 5px 5px; text-align: left; font-size: 12px; }
    .ms-preview-variants button small { display: block; margin-top: 2px; color: #666; font-size: 11px; }
    .ms-variant-image { width: 38px; height: 38px; object-fit: cover; border-radius: 6px; background: #f3f3f3; flex-shrink: 0; }
    .ms-footer {
        background: var(--ms-paper);
        padding: 50px max(28px, calc((100vw - 1320px) / 2));
        display: flex;
        align-items: center;
        gap: 40px;
        margin-top: 80px;
        border-top: 1px solid var(--ms-line);
    }
    .ms-footer p {
        color: var(--ms-muted);
        font-size: 13px;
        flex: 1;
    }
    .ms-footer > div:last-child {
        display: flex;
        gap: 24px;
    }
    @media(max-width: 900px) {
        .ms-header { grid-template-columns: 1fr auto; padding: 0 16px; }
        .ms-nav { display: none; }
        .ms-hero { grid-template-columns: 1fr; margin: 16px; }
        .ms-category-grid { grid-template-columns: repeat(2, 1fr); }
        .ms-product-grid { grid-template-columns: repeat(2, 1fr); }
        .ms-search { width: 38px; justify-content: center; padding: 0; }
        .ms-search span, .ms-search kbd { display: none; }
        .ms-category-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media(max-width: 600px) {
        .ms-category-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        #modern-categories .ms-section-head { align-items: flex-start; flex-direction: column; gap: 12px; }
        #modern-categories .ms-section-head h2 { font-size: 24px; }
        .ms-category-card { height: 130px; padding: 14px; }
        .ms-category-name b { font-size: 14px; }
        .ms-stats { gap: 24px; }
        .ms-stats b { font-size: 26px; }
        .ms-products-section .ms-section-head { align-items: flex-start; flex-direction: column; gap: 12px; }
        .ms-products-section .ms-section-head h2 { font-size: 24px; }
        .ms-product-grid { gap: 10px; }
        .ms-product-card { padding: 9px; }
        .ms-product-info > a { font-size: 13px; }
        .ms-products-more a { font-size: 12px; padding: 12px 16px; }
    }
    @media(max-width: 700px) { .ms-preview { grid-template-columns: minmax(0, 1fr); padding: 24px; } .ms-preview-image { max-height: none; } }
    #modern-categories .ms-category-card { position:relative; isolation:isolate; overflow:hidden; height:240px; padding:22px; border:0; color:#fff; background:#303238; justify-content:flex-end; }
    #modern-categories .ms-category-icon { position:absolute; z-index:-2; inset:0; width:100%; height:100%; border-radius:0; background:#303238; color:#fff; font-size:50px; }
    #modern-categories .ms-category-card .ms-category-icon img { width:100%; height:100%; object-fit:cover; }
    #modern-categories .ms-category-card::after { content:''; position:absolute; z-index:-1; inset:0; background:linear-gradient(180deg,rgba(0,0,0,.12),rgba(0,0,0,.12) 35%,rgba(0,0,0,.78)); }
    #modern-categories .ms-category-top { margin:0; }
    #modern-categories .ms-category-count { position:absolute; top:16px; right:16px; color:white; background:#0008; border-color:#ffffff35; }
    #modern-categories .ms-category-name b { font-size:21px; font-weight:700; text-shadow:0 1px 4px #0008; }
    #modern-categories .ms-category-description { color:#eee; text-shadow:0 1px 3px #000; }
    .ms-flash-section { padding-top:0; }
    .ms-flash { border-radius:20px; padding:28px; color:#fff; background:linear-gradient(115deg,#38131d,#861f25); }
    .ms-flash-heading { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; margin-bottom:22px; }
    .ms-flash-kicker { color:#ffc482; font-size:11px; font-weight:700; letter-spacing:.12em; }
    .ms-flash-heading h2 { margin:6px 0; color:#fff; font-size:28px; }
    .ms-flash-heading p { margin:0; color:#f5dad7; font-size:13px; }
    .ms-flash-heading a { color:#fff; font-size:13px; white-space:nowrap; }
    .ms-flash-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; }
    .ms-flash-card { display:flex; flex-direction:column; overflow:hidden; border-radius:14px; background:white; color:#171717; }
    .ms-flash-image { display:block; aspect-ratio:1; overflow:hidden; background:#f4f4f4; }
    .ms-flash-image img { width:100%; height:100%; object-fit:contain; }
    .ms-flash-body { display:flex; flex:1; flex-direction:column; gap:12px; padding:15px; }
    .ms-flash-body a { color:#171717; font-weight:650; }
    .ms-flash-body strong { margin-top:auto; color:#bb432e; }
    .ms-flash-body button { border:0; border-radius:99px; background:#c75d3b; color:white; padding:12px; font-weight:700; cursor:pointer; }
    .ms-flash-empty { display:flex; align-items:center; flex-wrap:wrap; gap:10px 18px; padding:24px; border:1px solid #ffffff30; border-radius:14px; background:#ffffff10; }
    .ms-flash-empty span { color:#f5dad7; font-size:13px; }
    .ms-flash-empty a { margin-left:auto; color:white; font-size:13px; font-weight:700; }
    @media(max-width:700px) { #modern-categories .ms-category-card { height:180px; padding:16px; } #modern-categories .ms-category-name b { font-size:16px; } .ms-flash { padding:20px; } .ms-flash-heading { align-items:flex-start; flex-direction:column; } .ms-flash-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .ms-flash-empty a { margin-left:0; } }
</style>
</div>

