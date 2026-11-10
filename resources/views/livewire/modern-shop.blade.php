<div class="ms-root">
<div class="modern-storefront">
    <div class="ms-promo">Miễn phí vận chuyển với mã <strong>FREESHIP</strong> · Giảm 10% với mã <strong>VANXUAN10</strong></div>
    <header class="ms-header">
        <a class="ms-brand" href="/" aria-label="The Babu Store">
            <span class="ms-brand-mark">B</span>
            <span><b>THE BABU</b><small>GAMING GEAR</small></span>
        </a>
        <nav class="ms-nav">
            <a href="/">Trang chủ</a>
            <a href="/shop" style="color: #000; font-weight: 800;">Cửa hàng</a>
            <a href="/shop?category=keycap-phu-kien">Keycap & phụ kiện</a>
            <a href="/shop?category=ban-phim-co">Bàn phím cơ</a>
            <a href="/shop?category=chuot-gaming">Chuột gaming</a>
        </nav>
        <div class="ms-actions">
            <a href="javascript:void(0);" onclick="Livewire.dispatch('open-search-palette')" aria-label="Tìm kiếm">⌕</a>
            <a href="/account" aria-label="Tài khoản">♙</a>
            <a class="ms-cart" href="/cart" aria-label="Giỏ hàng">
                <span>Giỏ hàng</span>
                <span class="ms-cart-badge">0</span>
            </a>
        </div>
    </header>

    <main class="ms-section" style="margin-top: 40px;">
        <div class="ms-section-head" style="align-items: center;">
            <div>
                <span class="ms-eyebrow">THE BABU STORE</span>
                <h2>Cửa Hàng Gaming Gear & Phím Cơ</h2>
                <p style="color: var(--ms-muted); margin: 6px 0 0; font-size: 14px;">Hiển thị {{ $products->total() ?? count($products) }} sản phẩm</p>
            </div>
            <div>
                <select wire:model.live="sort" style="padding: 10px 16px; border: 1px solid var(--ms-line); border-radius: 99px; font-size: 13px; font-weight: 600; background: #fff; outline: none; cursor: pointer;">
                    <option value="featured">Nổi bật nhất</option>
                    <option value="best_seller">Bán chạy nhất</option>
                    <option value="price_asc">Giá tăng dần</option>
                    <option value="price_desc">Giá giảm dần</option>
                    <option value="latest">Mới nhất</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 260px 1fr; gap: 32px; align-items: start;">
            <!-- Sidebar bộ lọc -->
            <aside style="background: #fbfbf9; border: 1px solid var(--ms-line); border-radius: 18px; padding: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <strong style="font-size: 16px;">Bộ lọc</strong>
                    <button type="button" wire:click="resetFilter" style="border: none; background: transparent; font-size: 12px; color: #ef4444; font-weight: 600; cursor: pointer;">Đặt lại</button>
                </div>

                <!-- Danh mục -->
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--ms-muted); margin-bottom: 12px;">Danh mục</div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($categories as $cat)
                            <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; cursor: pointer; color: #334155;">
                                <input type="checkbox" wire:model.live="selectedCategories" value="{{ $cat->id }}" style="accent-color: #111;">
                                <span>{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Khoảng giá -->
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--ms-muted); margin-bottom: 12px;">Khoảng giá</div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; cursor: pointer; color: #334155;">
                            <input type="radio" wire:model.live="priceRange" value="under_500" style="accent-color: #111;">
                            <span>Dưới 500.000₫</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; cursor: pointer; color: #334155;">
                            <input type="radio" wire:model.live="priceRange" value="500_1000" style="accent-color: #111;">
                            <span>500.000₫ – 1.000.000₫</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; cursor: pointer; color: #334155;">
                            <input type="radio" wire:model.live="priceRange" value="1000_2000" style="accent-color: #111;">
                            <span>1.000.000₫ – 2.000.000₫</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; cursor: pointer; color: #334155;">
                            <input type="radio" wire:model.live="priceRange" value="over_2000" style="accent-color: #111;">
                            <span>Trên 2.000.000₫</span>
                        </label>
                    </div>
                </div>

                <!-- Thương hiệu -->
                @if(isset($brands) && count($brands) > 0)
                    <div>
                        <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--ms-muted); margin-bottom: 12px;">Thương hiệu</div>
                        <div style="display: flex; flex-direction: column; gap: 8px; max-height: 200px; overflow-y: auto;">
                            @foreach($brands as $brand)
                                <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; cursor: pointer; color: #334155;">
                                    <input type="checkbox" wire:model.live="selectedBrands" value="{{ $brand->id }}" style="accent-color: #111;">
                                    <span>{{ $brand->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>

            <!-- Lưới sản phẩm -->
            <div>
                @if(count($products) > 0)
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                        @foreach($products as $product)
                            @php
                                $rawImages = data_get($product, 'images', []);
                                if (is_string($rawImages)) { $rawImages = json_decode($rawImages, true) ?: []; }
                                $image = is_array($rawImages) ? ($rawImages[0] ?? null) : null;
                                $variant = \Illuminate\Support\Facades\DB::table('product_variants')->where('product_id', $product->id)->orderBy('id')->first();
                                $price = data_get($variant, 'sale_price') ?: data_get($variant, 'price') ?: data_get($product, 'price');
                                $oldPrice = data_get($variant, 'price');
                                $discountPercent = ($oldPrice && $price && $oldPrice > $price) ? round((($oldPrice - $price) / $oldPrice) * 100) : null;
                            @endphp
                            <article class="ms-product-card">
                                <a class="ms-product-image" href="/product/{{ $product->slug }}">
                                    @if($image)<img src="{{ asset('storage/'.$image) }}" alt="{{ $product->name }}">@else<span class="ms-image-fallback">BABU</span>@endif
                                    @if($discountPercent)
                                        <span class="ms-badge">-{{ $discountPercent }}%</span>
                                    @endif
                                    <span class="ms-quick">Xem nhanh</span>
                                </a>
                                <div class="ms-product-info">
                                    <small>{{ $product->category?->name ?? 'GAMING GEAR' }}</small>
                                    <a href="/product/{{ $product->slug }}">{{ $product->name }}</a>
                                    <div>
                                        <b>{{ $price ? number_format($price, 0, ',', '.').'₫' : 'Liên hệ' }}</b>
                                        @if($oldPrice && $price && $oldPrice > $price)
                                            <del>{{ number_format($oldPrice, 0, ',', '.') }}₫</del>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div style="margin-top: 40px;">
                        {{ $products->links('livewire.store-pagination') }}
                    </div>
                @else
                    <div style="background: #fbfbf9; border: 1px dashed var(--ms-line); border-radius: 18px; padding: 60px 20px; text-align: center;">
                        <div style="font-size: 32px; margin-bottom: 10px;">🔍</div>
                        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px;">Không tìm thấy sản phẩm phù hợp</h3>
                        <p style="color: var(--ms-muted); font-size: 14px; margin-bottom: 18px;">Vui lòng thử chọn khoảng giá khác hoặc đặt lại bộ lọc.</p>
                        <button type="button" wire:click="resetFilter" style="background: var(--ms-ink); color: #fff; border: none; padding: 12px 24px; border-radius: 99px; font-weight: 700; font-size: 13px; cursor: pointer;">Đặt lại bộ lọc</button>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <footer class="ms-footer">
        <div class="ms-brand">
            <span class="ms-brand-mark">B</span>
            <span><b>THE BABU</b><small>GAMING GEAR</small></span>
        </div>
        <p>Góc máy chuẩn gu cho game thủ & dân văn phòng.</p>
        <div>
            <a href="/shop">Sản phẩm</a>
            <a href="/tra-cuu-don-hang">Tra cứu đơn</a>
            <a href="/blogs">Cẩm nang</a>
            <a href="/admin">Quản trị</a>
        </div>
    </footer>
</div>
</div>

