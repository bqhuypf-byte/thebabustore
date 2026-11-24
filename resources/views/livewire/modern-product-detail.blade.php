<div id="product-page" class="pd-page">
    @php
        $images = collect($product->images ?: [])->merge($product->variants->pluck('image'))->filter()->unique()->values();
        $displayImage = $activeImage ?: $images->first();
        $selected = $product->variants->firstWhere('id', $selectedVariant);
        $warrantyLabel = (int)$selected?->warranty_days === 365 ? '12 tháng' : ($selected?->warranty_days.' ngày');
    @endphp
    @livewire('inc.header')
    <main class="wrap pd-container">
        <nav class="crumbs pd-breadcrumb" aria-label="Breadcrumb">
            <a href="/">Trang chủ</a><span class="sep">›</span><a href="/shop">Sản phẩm</a>
            @if($product->category)<span class="sep">›</span><a href="/shop?category={{ urlencode($product->category->slug) }}">{{ $product->category->name }}</a>@endif
            <span class="sep">›</span><span class="cur">{{ $product->name }}</span>
        </nav>
        <div class="product pd-grid">
            @include('livewire.partials.product-photo-gallery')
            <section class="buy-col pd-details">
                <div class="cat-row"><a class="cat-link" href="/shop?category={{ urlencode($product->category?->slug ?? '') }}">{{ $product->category?->name ?? 'Sản phẩm' }}</a><span class="stock {{ $stock_status !== 'in_stock' ? 'out' : '' }}"><i></i>{{ $stock_status === 'in_stock' ? 'Còn hàng' : 'Hết hàng' }}</span></div>
                <h1 class="title">{{ $product->name }}</h1>
                <div class="meta-row pd-rating"><span class="stars" aria-label="{{ $reviewCount ? $avgRating.' trên 5 sao' : 'Chưa có đánh giá' }}">@foreach(range(1,5) as $star)<span class="{{ $star <= round($avgRating) ? 'filled' : '' }}">★</span>@endforeach</span><button type="button" wire:click="$set('activeTab', 'reviews')">{{ $reviewCount ? $reviewCount.' đánh giá' : 'Chưa có đánh giá' }}</button>@if($product->brand)<span class="dot"></span><span>Thương hiệu: <b>{{ $product->brand->name }}</b></span>@endif</div>
                <div class="price-panel"><div class="price-top"><div class="price pd-price">{{ $price ? number_format($price,0,',','.').'₫' : 'Liên hệ' }}</div><div class="price-note">Giá của {{ $selected?->name ?? $product->name }}</div></div><div class="price-sub"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" style="flex:none" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> <span>Phí và phương thức giao hàng được xác nhận tại bước thanh toán.</span></div></div>
                <div class="picker pd-classification store-option-picker">
                    <div class="picker-head"><h3>Chọn phân loại</h3></div>
                    @if($classificationNotice)<p class="note-box" role="status">{{ $classificationNotice }}</p>@endif
                    @if($classificationGroups)
                        @include('livewire.partials.variant-option-groups', ['selectionAction' => 'selectClassification'])
                    @else
                        <div class="step"><div class="step-label"><span class="step-num">1</span><b>Phiên bản</b><span class="picked">{{ $selected?->name }}</span></div><div class="opt-grid models pd-variants pd-variant-list">
                            @forelse($product->variants as $variant)
                                @php($variantThumb = $variant->image ?: $images->first())
                                <button type="button" class="opt has-thumb {{ $selectedVariant == $variant->id ? 'sel' : '' }}" wire:click="updateVariantDetails({{ $variant->id }})" aria-pressed="{{ $selectedVariant == $variant->id ? 'true' : 'false' }}" @disabled($variant->stock !== 'in_stock')>
                                    <img class="opt-thumb" src="{{ asset('storage/'.$variantThumb) }}" alt="Ảnh phân loại {{ $variant->name }}" loading="lazy">
                                    <span class="opt-copy"><span class="on">{{ $variant->name }}</span><span class="op">{{ number_format($variant->price,0,',','.') }}₫</span>@if($variant->stock !== 'in_stock')<span class="oos">Hết hàng</span>@endif</span>
                                </button>
                            @empty<p>Liên hệ cửa hàng về phiên bản sản phẩm.</p>@endforelse
                        </div></div>
                    @endif
                    <button class="compare-link" type="button" wire:click="$set('activeTab', 'specifications')" onclick="document.getElementById('chi-tiet').scrollIntoView({behavior:'smooth'})">Xem thông số và so sánh các phiên bản →</button>
                </div>
                @error('selectedVariant')<p class="field-error" role="alert">{{ $message }}</p>@enderror
                <div class="qty-row"><span class="lbl">Số lượng:</span><div class="stepper"><button type="button" wire:click="decrementQuantity" aria-label="Giảm số lượng">−</button><output>{{ $quantity }}</output><button type="button" wire:click="incrementQuantity" aria-label="Tăng số lượng">+</button></div><span class="qty-note">{{ $stock_status === 'in_stock' ? 'Sản phẩm đang còn hàng' : 'Liên hệ để kiểm tra hàng về' }}</span></div>
                <div class="cta-row pd-actions"><button class="btn btn-outline" type="button" wire:click="addtocart" @disabled(!$selected || $stock_status !== 'in_stock')>Thêm vào giỏ hàng</button><button class="btn btn-solid" type="button" wire:click="buyNow" @disabled(!$selected || $stock_status !== 'in_stock')>Mua ngay</button></div>
                <div class="trust"><div><span class="feature-check"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg></span><section><b>{{ $selected?->warranty_days ? 'Bảo hành '.$warrantyLabel : 'Bảo hành theo sản phẩm' }}</b><span>Chi tiết theo chính sách cửa hàng</span></section></div><div><span class="feature-check"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span><section><b>Thông tin giao hàng</b><span>Phí xác nhận khi thanh toán</span></section></div><div><span class="feature-check"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></span><section><b>Chính sách đổi trả</b><span><a href="/chinh-sach-doi-tra-hang-hoan-tien">Xem điều kiện áp dụng</a></span></section></div></div>
                <p class="pay-line">Thanh toán linh hoạt: <b>COD</b> · <b>Chuyển khoản ngân hàng</b></p>
            </section>
        </div>
        <section id="chi-tiet" class="tabs-card pd-content">
            <div class="tab-bar pd-tabs" role="tablist" aria-label="Thông tin sản phẩm">
                @foreach(['description'=>'Mô tả chi tiết','specifications'=>'Thông số kỹ thuật','warranty'=>'Chính sách bảo hành','reviews'=>'Đánh giá ('.$reviewCount.')'] as $tab=>$label)<button id="tab-{{ $tab }}" type="button" class="tab-btn {{ $activeTab === $tab ? 'active' : '' }}" role="tab" aria-selected="{{ $activeTab === $tab ? 'true' : 'false' }}" aria-controls="panel-{{ $tab }}" wire:click="$set('activeTab', '{{ $tab }}')">{{ $label }}</button>@endforeach
            </div>
            <section id="panel-description" role="tabpanel" aria-labelledby="tab-description" class="tab-pane pd-panel {{ $activeTab === 'description' ? 'active' : '' }}" @if($activeTab !== 'description') hidden @endif>@include('livewire.partials.product-description')</section>
            <section id="panel-specifications" role="tabpanel" aria-labelledby="tab-specifications" class="tab-pane pd-panel {{ $activeTab === 'specifications' ? 'active' : '' }}" @if($activeTab !== 'specifications') hidden @endif>
                @include('livewire.partials.product-specifications')
                <h3>Thông tin sản phẩm</h3><table class="spec-table"><tbody><tr><th scope="row">Tên sản phẩm</th><td>{{ $product->name }}</td></tr>@if($product->category)<tr><th scope="row">Danh mục</th><td>{{ $product->category->name }}</td></tr>@endif @if($product->brand)<tr><th scope="row">Thương hiệu</th><td>{{ $product->brand->name }}</td></tr>@endif @if($selected)<tr class="hl"><th scope="row">Phiên bản</th><td>{{ $selected->name }}</td></tr>@endif @foreach($classificationGroups as $group)<tr class="hl"><th scope="row">{{ $group['name'] }}</th><td>{{ $group['selected'] }}</td></tr>@endforeach @if($selected?->warranty_days)<tr><th scope="row">Thời hạn bảo hành</th><td>{{ $warrantyLabel }}</td></tr>@endif</tbody></table>
            </section>
            <section id="panel-warranty" role="tabpanel" aria-labelledby="tab-warranty" class="tab-pane pd-panel {{ $activeTab === 'warranty' ? 'active' : '' }}" @if($activeTab !== 'warranty') hidden @endif>@include('livewire.partials.product-warranty')</section>
            <section id="panel-reviews" role="tabpanel" aria-labelledby="tab-reviews" class="tab-pane pd-panel {{ $activeTab === 'reviews' ? 'active' : '' }}" @if($activeTab !== 'reviews') hidden @endif>@include('livewire.partials.product-reviews')</section>
        </section>
        @if($relatedProducts->isNotEmpty())<section class="related pd-related"><div class="related-head"><h2>Sản phẩm liên quan</h2><p>Cùng danh mục tại The BABU Store</p></div><div class="rel-grid">
            @foreach($relatedProducts->take(4) as $related)<article class="rel-card"><a class="rel-img" href="{{ url('/product/'.$related->slug) }}">@if(!empty(($related->images ?: [])[0]))<img src="{{ asset('storage/'.$related->images[0]) }}" alt="{{ $related->name }}" loading="lazy">@endif</a><div class="rel-body"><span class="rel-cat">{{ $related->category?->name }}</span><a class="rel-name" href="{{ url('/product/'.$related->slug) }}">{{ $related->name }}</a><div class="rel-price-row"><span class="rel-price">{{ number_format($related->price,0,',','.') }}₫</span></div><button class="rel-add" type="button" wire:click="$dispatch('open-product-quick-view', { productId: {{ $related->id }} })">Chọn phân loại</button></div></article>@endforeach
        </div></section>@endif
    </main>
    <div class="sticky-bar pd-mobile-bar"><div class="sp"><small>{{ $selected?->name ?? $product->name }}</small><b>{{ $price ? number_format($price,0,',','.') . '₫' : 'Liên hệ' }}</b></div><button class="btn btn-outline" type="button" wire:click="addtocart" @disabled(!$selected || $stock_status !== 'in_stock')>Thêm vào giỏ</button><button class="btn btn-solid" type="button" wire:click="buyNow" @disabled(!$selected || $stock_status !== 'in_stock')>Mua ngay</button></div>
    @livewire('inc.footer')
    @livewire('product-quick-view')
</div>
