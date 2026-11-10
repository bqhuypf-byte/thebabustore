<div>
    <div
        x-data="{
            open: @entangle('isOpen'),
            selected: @entangle('selectedIndex'),
            init() {
                window.addEventListener('keydown', (e) => {
                    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                        e.preventDefault();
                        const inlineInput = document.querySelector('.store-inline-search input');
                        if (inlineInput) { inlineInput.focus(); return; }
                        this.open = true;
                        $wire.open();
                        this.$nextTick(() => {
                            const input = document.getElementById('search-palette-input');
                            if (input) input.focus();
                        });
                    }
                    if (e.key === 'Escape' && this.open) {
                        e.preventDefault();
                        this.open = false;
                        $wire.close();
                    }
                });
            }
        }"
        x-show="open"
        x-cloak
        style="display: none;"
    >
        <!-- Backdrop -->
        <div
            class="palette-backdrop"
            @click="open = false; $wire.close()"
            style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 99999; animation: fadeIn 0.15s ease;"
        ></div>

        <!-- Palette Modal Container -->
        <div style="position: fixed; inset: 0; z-index: 100000; overflow-y: auto; padding: 60px 16px 24px; display: flex; justify-content: center; align-items: flex-start; pointer-events: none;">
            <div
                class="palette-card"
                @click.stop
                style="pointer-events: auto; width: 100%; max-width: 640px; background: #ffffff; border-radius: 14px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; border: 1px solid #e2e8f0; max-height: calc(100vh - 100px); display: flex; flex-direction: column;"
            >
                <!-- Search Input Bar -->
                <div style="display: flex; align-items: center; padding: 14px 18px; border-bottom: 1px solid #f1f5f9; gap: 12px; background: #ffffff;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input
                        id="search-palette-input"
                        wire:model.live.debounce.250ms="query"
                        type="text"
                        placeholder="Tìm kiếm sản phẩm, thương hiệu, danh mục... (gõ tối thiểu 2 ký tự)"
                        style="flex: 1; min-width: 0; border: none; outline: none; font-size: 15px; color: #1e293b; background: transparent;"
                        x-init="$watch('open', value => { if (value) { $nextTick(() => $el.focus()); } })"
                        @keydown.arrow-down.prevent="$wire.selectNext({{ count($products) }})"
                        @keydown.arrow-up.prevent="$wire.selectPrev({{ count($products) }})"
                        @keydown.enter="
                            const items = document.querySelectorAll('.palette-product-item');
                            if (items.length > 0 && items[selected]) {
                                window.location.href = items[selected].getAttribute('data-url');
                            } else if ($wire.query.trim().length > 0) {
                                window.location.href = '/search?query=' + encodeURIComponent($wire.query.trim());
                            }
                        "
                    />
                    @if(!empty($query))
                        <button
                            type="button"
                            wire:click="$set('query', '')"
                            style="border: none; background: transparent; cursor: pointer; color: #94a3b8; padding: 4px;"
                            title="Xóa tìm kiếm"
                        >
                            ✕
                        </button>
                    @endif
                    <kbd style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 8px; font-size: 11px; color: #64748b; font-family: monospace;">ESC</kbd>
                </div>

                <!-- Results Box -->
                <div style="flex: 0 1 auto; min-height: 0; overflow-y: auto; padding: 12px;">
                    @if(empty(trim($query)))
                        <div style="padding: 24px; text-align: center; color: #94a3b8; font-size: 13px;">
                            Nhập từ khóa hoặc dùng phím tắt <kbd style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 2px 6px;">⌘K</kbd> / <kbd style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 2px 6px;">Ctrl+K</kbd> để tìm kiếm nhanh
                        </div>
                    @elseif(mb_strlen(trim($query)) < 2)
                        <div style="padding: 16px; text-align: center; color: #94a3b8; font-size: 13px;">
                            Vui lòng nhập thêm ký tự để tìm kiếm...
                        </div>
                    @else
                        <!-- Danh mục khớp -->
                        @if($categories->isNotEmpty())
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em; margin: 4px 8px 8px;">Danh mục phù hợp</div>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px; padding: 0 4px;">
                                    @foreach($categories as $cat)
                                        <a
                                            href="/shop?category={{ $cat->slug }}"
                                            style="display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; font-size: 13px; color: #334155; text-decoration: none; transition: all 0.15s;"
                                            onmouseover="this.style.background='#e2e8f0'; this.style.borderColor='#cbd5e1'"
                                            onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0'"
                                        >
                                            <span style="color: #80BB35;">📁</span>
                                            <span>{{ $cat->name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Sản phẩm khớp -->
                        @if($products->isNotEmpty())
                            <div>
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em; margin: 8px 8px;">Sản phẩm ({{ $products->count() }})</div>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    @foreach($products as $index => $item)
                                        @php
                                            $firstImage = is_array($item->images) ? ($item->images[0] ?? null) : null;
                                        @endphp
                                        <a
                                            href="/product/{{ $item->slug }}"
                                            data-url="/product/{{ $item->slug }}"
                                            class="palette-product-item"
                                            :class="{ 'palette-item-selected': selected === {{ $index }} }"
                                            @mouseenter="selected = {{ $index }}"
                                            style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: inherit; transition: background 0.1s;"
                                            :style="selected === {{ $index }} ? 'background: #f1f5f9;' : 'background: transparent;'"
                                        >
                                            <div style="width: 44px; height: 44px; border-radius: 6px; background: #f8fafc; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #e2e8f0;">
                                                @if($firstImage)
                                                    <img src="{{ Storage::url($firstImage) }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: contain;">
                                                @else
                                                    <span style="color: #cbd5e1; font-size: 16px;">⌨</span>
                                                @endif
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div style="font-size: 11px; color: #80BB35; font-weight: 600;">{{ $item->category?->name ?? 'Gear' }}</div>
                                                <div style="font-size: 14px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item->name }}</div>
                                            </div>
                                            <div style="text-align: right; flex-shrink: 0;">
                                                <div style="font-size: 14px; font-weight: 700; color: #ef4444;">{{ number_format($item->price, 0, ',', '.') }}₫</div>
                                                @if($item->discount_price > 0)
                                                    <div style="font-size: 11px; color: #94a3af; text-decoration: line-through;">{{ number_format($item->discount_price, 0, ',', '.') }}₫</div>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            @if($categories->isEmpty())
                                <div style="padding: 32px 16px; text-align: center;">
                                    <div style="font-size: 24px; margin-bottom: 6px;">🔍</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #334155;">Không tìm thấy sản phẩm nào</div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Thử tìm kiếm với từ khóa khác hoặc kiểm tra chính tả</div>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>

                <!-- Footer Bar -->
                @if(mb_strlen(trim($query)) >= 2)
                    <div style="padding: 10px 18px; border-top: 1px solid #f1f5f9; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #64748b;">
                        <a href="/search?query={{ urlencode(trim($query)) }}" style="color: #80BB35; font-weight: 600; text-decoration: none;">
                            Xem tất cả kết quả cho "{{ trim($query) }}" →
                        </a>
                        <span style="color: #94a3b8; font-size: 11px;">Điều hướng: ↑ ↓ | Chọn: ↵ | Đóng: ESC</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

