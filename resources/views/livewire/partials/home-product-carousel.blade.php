@php
    $carouselScope = fn ($query) => $query->where('is_active', true)
        ->whereHas('variants', fn ($variants) => $variants->where('stock', 'in_stock'))
        ->with(['variants' => fn ($variants) => $variants->where('stock', 'in_stock')->orderBy('price')])
        ->latest('id')->limit(16);
    $carouselQuery = \App\Models\Product::query();
    $carouselScope($carouselQuery);
    $carouselLatest = $carouselQuery->get();
    $carouselCategories = \App\Models\Category::where('is_active', true)
        ->whereHas('product_cat', fn ($products) => $products->where('is_active', true)->whereHas('variants', fn ($variants) => $variants->where('stock', 'in_stock')))
        ->with(['product_cat' => $carouselScope])->orderBy('ordinal_number')->get();
    $featuredPool = $carouselLatest->concat($carouselCategories->flatMap(fn ($category) => $category->product_cat))->unique('id')->values();
    $featuredSold = \App\Models\OrderItem::query()->join('product_variants', 'product_variants.id', '=', 'order_items.product_variant_id')
        ->whereIn('product_variants.product_id', $featuredPool->pluck('id'))
        ->whereHas('order', fn ($q) => $q->whereIn('status', ['delivered', 'completed']))
        ->selectRaw('product_variants.product_id, SUM(order_items.quantity) as sold')->groupBy('product_variants.product_id')->pluck('sold', 'product_id');
    $carouselGroups = collect([
        ['key' => 'best', 'name' => 'Bán chạy', 'products' => $featuredPool->filter(fn ($product) => ($featuredSold[$product->id] ?? 0) > 0)->sortByDesc(fn ($product) => $featuredSold[$product->id])->take(8)],
        ['key' => 'new', 'name' => 'Hàng mới về', 'products' => $carouselLatest],
        ['key' => 'combo', 'name' => 'Combo setup', 'products' => $featuredPool->filter(fn ($product) => preg_match('/\bcombo\b/iu', $product->name))->take(8)],
    ]);
@endphp
<section class="home-catalog" aria-labelledby="home-catalog-title" x-data="{ active: 'best' }">
    <div class="home-catalog-heading"><div><h2 id="home-catalog-title">Sản phẩm nổi bật</h2><p>Khám phá gaming gear cho góc máy của bạn.</p></div><a href="/shop">Xem tất cả</a></div>
    <div class="home-catalog-tabs" role="tablist" aria-label="Nhóm sản phẩm nổi bật" @keydown.arrow-right.prevent="const tabs = [...$el.querySelectorAll('[role=tab]')]; const next = tabs[(tabs.indexOf(document.activeElement) + 1) % tabs.length]; next.focus(); next.click()" @keydown.arrow-left.prevent="const tabs = [...$el.querySelectorAll('[role=tab]')]; const prev = tabs[(tabs.indexOf(document.activeElement) + tabs.length - 1) % tabs.length]; prev.focus(); prev.click()">
        @foreach($carouselGroups as $group)
            <button type="button" role="tab" id="catalog-tab-{{ $group['key'] }}" aria-controls="catalog-panel-{{ $group['key'] }}" :aria-selected="(active === '{{ $group['key'] }}').toString()" :tabindex="active === '{{ $group['key'] }}' ? 0 : -1" :class="{ 'is-active': active === '{{ $group['key'] }}' }" @click="active = '{{ $group['key'] }}'">{{ $group['name'] }}</button>
        @endforeach
    </div>
    @foreach($carouselGroups as $group)
        <div id="catalog-panel-{{ $group['key'] }}" class="home-catalog-panel" role="tabpanel" aria-labelledby="catalog-tab-{{ $group['key'] }}" x-show="active === '{{ $group['key'] }}'" @if($group['key'] !== 'best') x-cloak @endif wire:key="home-catalog-{{ $group['key'] }}"
            x-data="{
                progress: 0, atStart: true, atEnd: false, observer: null,
                sync() { const track = this.$refs.track; const max = Math.max(0, track.scrollWidth - track.clientWidth); this.progress = max > 0 ? track.scrollLeft / max * 100 : 0; this.atStart = track.scrollLeft <= 2; this.atEnd = max - track.scrollLeft <= 2; },
                step(direction) { const track = this.$refs.track; const card = track.querySelector('.home-catalog-card'); if (!card) return; const gap = parseFloat(getComputedStyle(track).columnGap) || 0; track.scrollBy({ left: direction * (card.getBoundingClientRect().width + gap), behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' }); },
                init() { this.observer = new ResizeObserver(() => this.sync()); this.observer.observe(this.$refs.track); this.$nextTick(() => this.sync()); this.$watch('active', () => this.$nextTick(() => { this.$refs.track.scrollTo({left: 0, behavior: 'instant'}); this.sync(); })); },
                destroy() { this.observer?.disconnect(); }
            }">
            <div class="home-catalog-track" x-ref="track" @scroll.passive="sync()" tabindex="0" aria-label="Sản phẩm {{ $group['name'] }}" @keydown.arrow-right.prevent="step(1)" @keydown.arrow-left.prevent="step(-1)">
                @forelse($group['products'] as $product)
                    @php
                        $image = $product->variants->first(fn ($variant) => filled($variant->image))?->image ?: (($product->images ?: [])[0] ?? null);
                        $price = $product->variants->min('price');
                        $priceVaries = $product->variants->max('price') > $price;
                        $variantImages = $product->variants->filter(fn ($variant) => filled($variant->image))->unique('image')->values();
                    @endphp
                    <article class="home-catalog-card" wire:key="catalog-{{ $group['key'] }}-{{ $product->id }}" x-data="{ image: @js($image ? Storage::url($image) : null) }">
                        <div class="home-catalog-image">
                            <a href="/product/{{ $product->slug }}" aria-label="{{ $product->name }}">@if($image)<img :src="image" src="{{ Storage::url($image) }}" alt="{{ $product->name }}" loading="lazy" width="480" height="480">@else<span>BABU STORE</span>@endif</a>
                            @if($product->on_sale)<span class="home-catalog-badge">Ưu đãi</span>@endif
                            <button class="home-catalog-quick" type="button" wire:click="openPreview({{ $product->id }})" aria-label="Xem nhanh {{ $product->name }}">Xem nhanh</button>
                        </div>
                        <div class="home-catalog-body">
                            <a class="home-catalog-name" href="/product/{{ $product->slug }}">{{ $product->name }}</a>
                            <div class="home-product-meta">@if($product->approved_reviews_count > 0)<span class="home-product-star">★</span> {{ number_format($product->approved_reviews_avg, 1) }} · {{ $product->approved_reviews_count }} đánh giá @else Chưa có đánh giá @endif  · Đã bán {{ number_format($featuredSold[$product->id] ?? 0, 0, ',', '.') }}</div>
                            <div class="home-catalog-price">@if($priceVaries)<span>Từ </span>@endif{{ number_format($price ?? $product->price, 0, ',', '.') }}₫</div>
                            @if($variantImages->isNotEmpty())
                                <div class="home-catalog-variants" aria-label="Ảnh phiên bản {{ $product->name }}">
                                    @foreach($variantImages->take(4) as $variant)
                                        <button type="button" @click="image = @js(Storage::url($variant->image))" :aria-pressed="(image === @js(Storage::url($variant->image))).toString()" :class="{ 'is-selected': image === @js(Storage::url($variant->image)) }" aria-label="Xem ảnh {{ $variant->name }}" title="{{ $variant->name }}"><img src="{{ Storage::url($variant->image) }}" alt="" loading="lazy" width="24" height="24"></button>
                                    @endforeach
                                    @if($variantImages->count() > 4)<button type="button" class="home-catalog-more" wire:click="openPreview({{ $product->id }})" aria-label="Xem thêm phiên bản {{ $product->name }}">+{{ $variantImages->count() - 4 }}</button>@endif
                                </div>
                            @endif
                            @include('livewire.partials.purchase-card-button', ['purchaseCardProduct'=>$product, 'purchaseCardClass'=>'home-product-add'])
                        </div>
                    </article>
                @empty
                    <p class="home-catalog-empty">Sản phẩm đang được cập nhật.</p>
                @endforelse
            </div>
            <div class="home-catalog-controls">
                <input class="home-catalog-progress" type="range" min="0" max="100" step="1" :value="progress" :style="{ '--catalog-progress': progress + '%' }" @input="$refs.track.scrollLeft = ($refs.track.scrollWidth - $refs.track.clientWidth) * $event.target.value / 100" :disabled="atStart && atEnd" aria-label="Vị trí danh sách sản phẩm {{ $group['name'] }}">
                <div class="home-catalog-arrows"><button type="button" @click="step(-1)" :disabled="atStart" aria-label="Sản phẩm trước">‹</button><button type="button" @click="step(1)" :disabled="atEnd" aria-label="Sản phẩm tiếp theo">›</button></div>
            </div>
        </div>
    @endforeach
</section>