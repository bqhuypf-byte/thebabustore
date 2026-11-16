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
    $carouselGroups = collect([['key' => 'new', 'name' => 'Mới nhất', 'products' => $carouselLatest]])
        ->concat($carouselCategories->map(fn ($category) => ['key' => (string) $category->id, 'name' => $category->name, 'products' => $category->product_cat]));
@endphp
<section class="home-catalog" aria-labelledby="home-catalog-title" x-data="{ active: 'new' }">
    <div class="home-catalog-heading"><h2 id="home-catalog-title">Sản phẩm theo danh mục</h2><p>Khám phá gaming gear cho góc máy của bạn.</p></div>
    <div class="home-catalog-tabs" role="tablist" aria-label="Danh mục sản phẩm" @keydown.arrow-right.prevent="const tabs = [...$el.querySelectorAll('[role=tab]')]; const next = tabs[(tabs.indexOf(document.activeElement) + 1) % tabs.length]; next.focus(); next.click()" @keydown.arrow-left.prevent="const tabs = [...$el.querySelectorAll('[role=tab]')]; const prev = tabs[(tabs.indexOf(document.activeElement) + tabs.length - 1) % tabs.length]; prev.focus(); prev.click()">
        @foreach($carouselGroups as $group)
            <button type="button" role="tab" id="catalog-tab-{{ $group['key'] }}" aria-controls="catalog-panel-{{ $group['key'] }}" :aria-selected="(active === '{{ $group['key'] }}').toString()" :tabindex="active === '{{ $group['key'] }}' ? 0 : -1" :class="{ 'is-active': active === '{{ $group['key'] }}' }" @click="active = '{{ $group['key'] }}'">{{ $group['name'] }}</button>
        @endforeach
    </div>
    @foreach($carouselGroups as $group)
        <div id="catalog-panel-{{ $group['key'] }}" class="home-catalog-panel" role="tabpanel" aria-labelledby="catalog-tab-{{ $group['key'] }}" x-show="active === '{{ $group['key'] }}'" @if($group['key'] !== 'new') x-cloak @endif wire:key="home-catalog-{{ $group['key'] }}"
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
                            <div class="home-catalog-price">@if($priceVaries)<span>Từ </span>@endif{{ number_format($price ?? $product->price, 0, ',', '.') }}₫</div>
                            @if($variantImages->isNotEmpty())
                                <div class="home-catalog-variants" aria-label="Ảnh phiên bản {{ $product->name }}">
                                    @foreach($variantImages->take(4) as $variant)
                                        <button type="button" @click="image = @js(Storage::url($variant->image))" :aria-pressed="(image === @js(Storage::url($variant->image))).toString()" :class="{ 'is-selected': image === @js(Storage::url($variant->image)) }" aria-label="Xem ảnh {{ $variant->name }}" title="{{ $variant->name }}"><img src="{{ Storage::url($variant->image) }}" alt="" loading="lazy" width="24" height="24"></button>
                                    @endforeach
                                    @if($variantImages->count() > 4)<button type="button" class="home-catalog-more" wire:click="openPreview({{ $product->id }})" aria-label="Xem thêm phiên bản {{ $product->name }}">+{{ $variantImages->count() - 4 }}</button>@endif
                                </div>
                            @endif
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