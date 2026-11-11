<div class="store-mega-backdrop" x-cloak x-show="megaOpen" x-transition.opacity @click="megaOpen = false" aria-hidden="true"></div>
<section id="store-product-menu" class="store-mega-panel" :class="{ 'has-sidebar': megaMode === 'all' }" x-ref="megaPanel" x-cloak x-show="megaOpen" x-transition.opacity.duration.150ms
    @mouseenter="clearTimeout(megaTimer)" @mouseleave="closeMegaLater()" aria-label="Khám phá sản phẩm">
    <div class="store-mega-inner">
        <div class="store-mega-top"><button type="button" class="store-mega-close" @click="if (window.matchMedia('(min-width:851px)').matches) $refs.megaTrigger.focus(); megaOpen = false" aria-label="Đóng danh mục sản phẩm">×</button></div>
        <div class="store-mega-categories" aria-label="Chọn danh mục">
            @foreach($menuCategories as $menuCategory)
                <a href="/shop?category={{ urlencode($menuCategory->slug) }}" @mouseenter="megaCategory = '{{ $menuCategory->id }}'" @focus="megaCategory = '{{ $menuCategory->id }}'" :class="{ 'is-selected': megaCategory === '{{ $menuCategory->id }}' }" :aria-current="megaCategory === '{{ $menuCategory->id }}' ? 'true' : 'false'">{{ $menuCategory->name }}</a>
            @endforeach
        </div>
        @php $megaGroups = collect([['key' => 'all', 'name' => 'Khám phá gaming gear', 'url' => '/shop', 'products' => $menuProducts]])->concat($menuCategories->map(fn ($category) => ['key' => (string) $category->id, 'name' => $category->name, 'url' => '/shop?category='.urlencode($category->slug), 'products' => $category->product_cat])); @endphp
        @foreach($megaGroups as $megaGroup)
            <div class="store-mega-group" x-show="megaCategory === '{{ $megaGroup['key'] }}'" @if($megaGroup['key'] !== 'all') x-cloak @endif wire:key="mega-group-{{ $megaGroup['key'] }}">
                <div class="store-mega-heading"><div><span class="store-mega-kicker">SẢN PHẨM NỔI BẬT</span><h2>{{ $megaGroup['name'] }}</h2></div><a href="{{ $megaGroup['url'] }}">Xem tất cả <span aria-hidden="true">›</span></a></div>
                <div class="store-mega-products" tabindex="0" aria-label="Sản phẩm {{ $megaGroup['name'] }}">
                    @forelse($megaGroup['products'] as $megaProduct)
                        @php $megaVariant = $megaProduct->variants->first(); $megaImage = $megaProduct->variants->first(fn ($variant) => filled($variant->image))?->image ?: (($megaProduct->images ?: [])[0] ?? null); @endphp
                        <a class="store-mega-product" href="/product/{{ $megaProduct->slug }}" title="{{ $megaProduct->name }}">
                            <div class="store-mega-image">@if($megaImage)<img src="{{ Storage::url($megaImage) }}" alt="{{ $megaProduct->name }}" loading="lazy">@else<span>{{ $megaProduct->name }}</span>@endif</div>
                            <strong>{{ preg_replace('/^(?:Bàn phím(?: cơ| HE)?|Chuột(?: gaming)?|Tai nghe(?: gaming)?|Switch)\s+/iu', '', $megaProduct->name) }}</strong>
                        </a>
                    @empty
                        <p class="store-mega-empty">Danh mục đang được cập nhật. <a href="/shop">Khám phá cửa hàng →</a></p>
                    @endforelse
                </div>
                <div class="store-mega-controls"><div><button type="button" @click="$el.closest('.store-mega-group').querySelector('.store-mega-products').scrollBy({left: -500, behavior: 'smooth'})" aria-label="Sản phẩm trước">←</button><button type="button" @click="$el.closest('.store-mega-group').querySelector('.store-mega-products').scrollBy({left: 500, behavior: 'smooth'})" aria-label="Sản phẩm tiếp theo">→</button></div></div>
            </div>
        @endforeach
    </div>
</section>