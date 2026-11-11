<div class="store-mega-backdrop" x-cloak x-show="megaOpen" x-transition.opacity @click="megaOpen = false" aria-hidden="true"></div>
<section id="store-product-menu" class="store-mega-panel" x-ref="megaPanel" x-cloak x-show="megaOpen" x-transition.opacity.duration.150ms
    @mouseenter="clearTimeout(megaTimer)" @mouseleave="closeMegaLater()" aria-label="Khám phá sản phẩm">
    <div class="store-mega-inner">
        <div class="store-mega-top"><span>SẢN PHẨM NỔI BẬT</span><button type="button" class="store-mega-close" @click="if (window.matchMedia('(min-width:851px)').matches) $refs.megaTrigger.focus(); megaOpen = false" aria-label="Đóng danh mục sản phẩm">×</button></div>
        <div class="store-mega-categories" aria-label="Chọn danh mục">
            <button type="button" @mouseenter="megaCategory = 'all'" @focus="megaCategory = 'all'" @click="megaCategory = 'all'" :class="{ 'is-selected': megaCategory === 'all' }" :aria-pressed="(megaCategory === 'all').toString()">Tất cả sản phẩm</button>
            @foreach($menuCategories as $menuCategory)
                <button type="button" @mouseenter="megaCategory = '{{ $menuCategory->id }}'" @focus="megaCategory = '{{ $menuCategory->id }}'" @click="megaCategory = '{{ $menuCategory->id }}'" :class="{ 'is-selected': megaCategory === '{{ $menuCategory->id }}' }" :aria-pressed="(megaCategory === '{{ $menuCategory->id }}').toString()">{{ $menuCategory->name }}</button>
            @endforeach
        </div>
        @php $megaGroups = collect([['key' => 'all', 'name' => 'Khám phá gaming gear', 'url' => '/shop', 'products' => $menuProducts]])->concat($menuCategories->map(fn ($category) => ['key' => (string) $category->id, 'name' => $category->name, 'url' => '/shop?category='.urlencode($category->slug), 'products' => $category->product_cat])); @endphp
        @foreach($megaGroups as $megaGroup)
            <div class="store-mega-group" x-show="megaCategory === '{{ $megaGroup['key'] }}'" @if($megaGroup['key'] !== 'all') x-cloak @endif wire:key="mega-group-{{ $megaGroup['key'] }}">
                <div class="store-mega-heading"><h2>{{ $megaGroup['name'] }}</h2><a href="{{ $megaGroup['url'] }}">Xem tất cả <span aria-hidden="true">↗</span></a></div>
                <div class="store-mega-products" tabindex="0" aria-label="Sản phẩm {{ $megaGroup['name'] }}">
                    @forelse($megaGroup['products'] as $megaProduct)
                        @php $megaImage = ($megaProduct->images ?: [])[0] ?? null; $megaVariant = $megaProduct->variants->first(); @endphp
                        <a class="store-mega-product" href="/product/{{ $megaProduct->slug }}">
                            <div class="store-mega-image">@if($megaImage)<img src="{{ Storage::url($megaImage) }}" alt="{{ $megaProduct->name }}" loading="lazy">@else<span>{{ $megaProduct->name }}</span>@endif</div>
                            <strong>{{ $megaProduct->name }}</strong>
                            <span class="store-mega-price">{{ number_format($megaVariant?->price ?? $megaProduct->price, 0, ',', '.') }}₫</span>
                        </a>
                    @empty
                        <p class="store-mega-empty">Danh mục đang được cập nhật. <a href="/shop">Khám phá cửa hàng →</a></p>
                    @endforelse
                </div>
                <div class="store-mega-controls"><span>Khám phá sản phẩm đang có sẵn tại BABU</span><div><button type="button" @click="$el.closest('.store-mega-group').querySelector('.store-mega-products').scrollBy({left: -500, behavior: 'smooth'})" aria-label="Sản phẩm trước">←</button><button type="button" @click="$el.closest('.store-mega-group').querySelector('.store-mega-products').scrollBy({left: 500, behavior: 'smooth'})" aria-label="Sản phẩm tiếp theo">→</button></div></div>
            </div>
        @endforeach
    </div>
</section>