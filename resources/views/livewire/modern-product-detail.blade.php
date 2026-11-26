<div id="product-page" class="pd-page">
    @php
        $images = collect($product->images ?: [])->merge($product->variants->pluck('image'))->filter()->unique()->values();
        $displayImage = $activeImage ?: $images->first();
        $selected = $product->variants->firstWhere('id', $selectedVariant);
        $purchasePrice = $selected?->price ?? $product->variants->filter(fn($variant) => \App\Support\PurchaseValidation::available($variant))->min('price') ?? $product->price;
        $purchaseBlocked = \App\Support\PurchaseValidation::message($product, $purchaseOptions);
        $detailCampaign = \App\Models\PromotionCampaign::productBanner();
        $detailAvailable = $selected ? \App\Support\PurchaseValidation::available($selected) : $product->variants->contains(fn($variant) => \App\Support\PurchaseValidation::available($variant));

        $warrantyLabel = (int)$selected?->warranty_days === 365 ? '12 tháng' : ($selected?->warranty_days.' ngày');
    @endphp
    @livewire('inc.header')
    <main class="wrap pd-container">
        <nav class="crumbs pd-breadcrumb" aria-label="Breadcrumb">
            <a href="/">Trang chủ</a><span class="sep">›</span><a href="/shop">Sản phẩm</a>
            @if($product->category)<span class="sep">›</span><a href="/shop?category={{ urlencode($product->category->slug) }}">{{ $product->category->name }}</a>@endif
            <span class="sep">›</span><span class="cur">{{ $product->name }}</span>
        </nav>
        <div class="pd-product-frame">
        <div class="pd-product-heading">
            <h1 class="title">{{ $product->name }}</h1>
            <div class="meta-row pd-rating"><span class="stars" aria-label="{{ $reviewCount ? $avgRating.' trên 5 sao' : 'Chưa có đánh giá' }}">@foreach(range(1,5) as $star)<span class="{{ $star <= round($avgRating) ? 'filled' : '' }}">★</span>@endforeach</span><button type="button" wire:click="$set('activeTab', 'reviews')">{{ $reviewCount ? $reviewCount.' đánh giá' : 'Chưa có đánh giá' }}</button>@if($product->brand)<span class="dot"></span><span>Thương hiệu: <b>{{ $product->brand->name }}</b></span>@endif</div>
            <a class="pd-heading-specs" href="#chi-tiet" wire:click="$set('activeTab', 'specifications')">Thông số kỹ thuật</a>
        </div>
        <div class="product pd-grid">
            @include('livewire.partials.product-photo-gallery')
            <section class="buy-col pd-details">
                @if($detailCampaign)<a class="pd-purchase-banner pd-campaign-banner" href="{{ $detailCampaign->publicUrl() }}" aria-label="{{ $detailCampaign->headline }}"><img src="{{ Storage::url($detailCampaign->product_banner_image) }}" alt="{{ $detailCampaign->headline }}" loading="lazy"></a>@endif

                <div class="picker pd-classification store-option-picker">
                    <div class="picker-head"><h3>Chọn phân loại</h3></div>
                    @if($classificationNotice)<p class="note-box" role="status">{{ $classificationNotice }}</p>@endif
                    @if($classificationGroups)
                        @include('livewire.partials.variant-option-groups', ['selectionAction' => 'selectClassification'])
                    @else
                        <div class="step"><div class="step-label"><span class="step-num">1</span><b>Phiên bản</b><span class="picked">{{ $selected?->name }}</span></div><div class="opt-grid models pd-variants pd-variant-list">
                            @forelse($product->variants as $variant)
                                @php $variantThumb = $variant->image ?: $images->first(); @endphp
                                <button type="button" class="opt has-thumb {{ $selectedVariant == $variant->id ? 'sel' : '' }}" wire:click="updateVariantDetails({{ $variant->id }})" aria-pressed="{{ $selectedVariant == $variant->id ? 'true' : 'false' }}" @disabled(!\App\Support\PurchaseValidation::available($variant))>
                                    <img class="opt-thumb" src="{{ asset('storage/'.$variantThumb) }}" alt="Ảnh phân loại {{ $variant->name }}" loading="lazy">
                                    <span class="opt-copy"><span class="on">{{ $variant->name }}</span><span class="op">{{ number_format($variant->price,0,',','.') }}₫</span>@if($variant->stock !== 'in_stock')<span class="oos">Hết hàng</span>@endif</span>
                                </button>
                            @empty<p>Liên hệ cửa hàng về phiên bản sản phẩm.</p>@endforelse
                        </div></div>
                    @endif
                </div>
                <div class="cat-row"><a class="cat-link" href="/shop?category={{ urlencode($product->category?->slug ?? '') }}">{{ $product->category?->name ?? 'Sản phẩm' }}</a><span class="stock {{ !$detailAvailable ? 'out' : '' }}"><i></i>{{ $detailAvailable ? 'Còn hàng' : 'Hết hàng' }}</span></div>
                <div class="price-panel"><div class="price-top"><div class="price pd-price">{{ $purchasePrice ? number_format($purchasePrice,0,',','.').'₫' : 'Liên hệ' }}</div><div class="price-note">{{ $selected ? $selected->name : 'Giá từ' }}</div></div></div>
                @if($settings?->announcement_enabled && filled($settings->announcement_text))<details class="pd-promotion-card"><summary>Ưu đãi từ BABU Store <span aria-hidden="true">⌄</span></summary><p>{{ $settings->announcement_text }}</p>@if($settings->announcement_link)<a href="{{ $settings->announcement_link }}">{{ $settings->announcement_button_text ?: 'Xem chi tiết' }}</a>@endif</details>@endif
                @error('selectedVariant')<p class="field-error" role="alert">{{ $message }}</p>@enderror
                @include('livewire.partials.purchase-controls', ['purchaseProduct'=>$product, 'purchaseVariant'=>$selected, 'quantityProperty'=>'quantity', 'quantityClass'=>'qty-row', 'actionsClass'=>'cta-row pd-actions', 'addClass'=>'btn btn-outline', 'buyClass'=>'btn btn-solid', 'addAction'=>'addtocart', 'buyAction'=>'buyNow', 'buyExpression'=>'buyNow'])
            </section>
            <aside class="pd-retail-top-rail" aria-label="Thông số sản phẩm">@include('livewire.partials.product-retail-spec-card')</aside>
        </div>
        </div>
        <div class="pd-retail-information">
        <div class="pd-retail-main-content">
        <section id="chi-tiet" class="tabs-card pd-content">
            <div class="tab-bar pd-tabs" role="tablist" aria-label="Thông tin sản phẩm">
                @foreach(['description'=>'Mô tả chi tiết','specifications'=>'Thông số kỹ thuật','warranty'=>'Chính sách bảo hành','reviews'=>'Đánh giá ('.$reviewCount.')'] as $tab=>$label)<button id="tab-{{ $tab }}" type="button" class="tab-btn {{ $activeTab === $tab ? 'active' : '' }}" role="tab" aria-selected="{{ $activeTab === $tab ? 'true' : 'false' }}" aria-controls="panel-{{ $tab }}" wire:click="$set('activeTab', '{{ $tab }}')" @if($tab === 'reviews') onclick="document.getElementById('panel-reviews').scrollIntoView({behavior:'smooth',block:'start'})" @endif>{{ $label }}</button>@endforeach
            </div>
            <section id="panel-description" role="tabpanel" aria-labelledby="tab-description" class="tab-pane pd-panel {{ in_array($activeTab, ['description','reviews']) ? 'active' : '' }}" @if(!in_array($activeTab, ['description','reviews'])) hidden @endif>@include('livewire.partials.product-description')</section>
            <section id="panel-specifications" role="tabpanel" aria-labelledby="tab-specifications" class="tab-pane pd-panel {{ $activeTab === 'specifications' ? 'active' : '' }}" @if($activeTab !== 'specifications') hidden @endif>
                @include('livewire.partials.product-specifications')
                <h3>Thông tin sản phẩm</h3><table class="spec-table"><tbody><tr><th scope="row">Tên sản phẩm</th><td>{{ $product->name }}</td></tr>@if($product->category)<tr><th scope="row">Danh mục</th><td>{{ $product->category->name }}</td></tr>@endif @if($product->brand)<tr><th scope="row">Thương hiệu</th><td>{{ $product->brand->name }}</td></tr>@endif @if($selected)<tr class="hl"><th scope="row">Phiên bản</th><td>{{ $selected->name }}</td></tr>@endif @foreach($classificationGroups as $group)<tr class="hl"><th scope="row">{{ $group['name'] }}</th><td>{{ $group['selected'] }}</td></tr>@endforeach @if($selected?->warranty_days)<tr><th scope="row">Thời hạn bảo hành</th><td>{{ $warrantyLabel }}</td></tr>@endif</tbody></table>
            </section>
            <section id="panel-warranty" role="tabpanel" aria-labelledby="tab-warranty" class="tab-pane pd-panel {{ $activeTab === 'warranty' ? 'active' : '' }}" @if($activeTab !== 'warranty') hidden @endif>@include('livewire.partials.product-warranty')</section>

        </section>
            <section id="panel-reviews" role="tabpanel" aria-labelledby="tab-reviews" class="tab-pane pd-panel active pd-retail-reviews">@include('livewire.partials.product-reviews')</section>
        </div>
        <aside class="pd-retail-content-rail" aria-label="Thông số và tin tức">
            @include('livewire.partials.product-retail-spec-card')
            @php $productNews = \App\Models\Blog::query()->where('status','published')->where(fn($q) => $q->whereNull('published_at')->orWhere('published_at','<=',now()))->latest('published_at')->take(4)->get(); @endphp
            @if($productNews->isNotEmpty())<section class="pd-retail-card pd-retail-news"><h2>Tin tức mới nhất</h2>@foreach($productNews as $news)<a href="/blog/{{ $news->slug }}">@if($news->banner)<img src="{{ Storage::url($news->banner) }}" alt="" loading="lazy">@endif<span>{{ $news->title }}<small>{{ ($news->published_at ?? $news->created_at)?->format('d/m/Y') }}</small></span></a>@endforeach</section>@else<section class="pd-retail-card pd-retail-news"><h2>Tin tức &amp; hướng dẫn</h2><a href="/blogs">Khám phá các bài viết của BABU Store</a></section>@endif
        </aside>
        </div>
        @error('cardPurchase')<p class="purchase-error" role="alert">{{ $message }}</p>@enderror
        @if($relatedProducts->isNotEmpty())<section class="related pd-related"><div class="related-head"><h2>Sản phẩm liên quan</h2><p>Cùng danh mục tại The BABU Store</p></div><div class="rel-grid">
            @foreach($relatedProducts->take(4) as $related)<article class="rel-card"><a class="rel-img" href="{{ url('/product/'.$related->slug) }}">@if(!empty(($related->images ?: [])[0]))<img src="{{ asset('storage/'.$related->images[0]) }}" alt="{{ $related->name }}" loading="lazy">@endif</a><div class="rel-body"><span class="rel-cat">{{ $related->category?->name }}</span><a class="rel-name" href="{{ url('/product/'.$related->slug) }}">{{ $related->name }}</a><div class="rel-price-row"><span class="rel-price">{{ number_format($related->price,0,',','.') }}₫</span></div>@include('livewire.partials.purchase-card-button', ['purchaseCardProduct'=>$related, 'purchaseCardClass'=>'rel-add'])</div></article>@endforeach
        </div></section>@endif
    </main>
    <div class="sticky-bar pd-mobile-bar pd-purchase-sticky"><div class="pd-purchase-sticky-inner">
        <div class="pd-sticky-product">@if($displayImage)<img src="{{ Storage::url($displayImage) }}" alt="">@endif<div><strong>{{ $product->name }}</strong>@if($selected)<small>{{ $selected->name }}</small>@endif</div></div>
        <div class="pd-sticky-subtotal"><small>{{ $selected ? 'Tạm tính' : 'Giá từ' }}</small><b>{{ $purchasePrice ? number_format($purchasePrice * ($selected ? max(1,(int)$quantity) : 1),0,',','.').'₫' : 'Liên hệ' }}</b></div>
        <button class="btn btn-outline" type="button" wire:click="addtocart" wire:loading.attr="disabled" wire:target="addtocart,buyNow" @disabled((bool)$purchaseBlocked)>{{ $purchaseBlocked === 'Hết hàng' ? 'Hết hàng' : 'Thêm vào giỏ' }}</button>
        <button class="btn btn-solid" type="button" wire:click="buyNow" wire:loading.attr="disabled" wire:target="addtocart,buyNow" @disabled((bool)$purchaseBlocked)>{{ $purchaseBlocked === 'Hết hàng' ? 'Hết hàng' : 'Mua ngay' }}</button>
    </div></div>
    @livewire('inc.footer')
    @livewire('product-quick-view')
</div>
