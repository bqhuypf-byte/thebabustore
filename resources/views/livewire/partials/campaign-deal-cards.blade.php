                @foreach($dealItems as $item)
                @php $product=$item->product; $available=$product->variants->filter(fn($v)=>\App\Support\PurchaseValidation::available($v)); $variant=$available->sortBy('price')->first(); $image=$variant?->image ?: (($product->images ?: [])[0] ?? null); @endphp
                <article class="ss-card bf-card" wire:key="campaign-item-{{ $item->id }}" @if($campaignCardFilter ?? false) x-show.important="saleCategory === 'all' || saleCategory === '{{ $product->category_id }}'" @endif>
                    <div class="bf-card-masthead" aria-hidden="true"><b>BF</b><span>BLACK FRIDAY</span></div>
                    @if($item->badge)<span class="bf-badge">{{ $item->badge }}</span>@endif
                    <div class="ss-image"><a href="{{ url('/products/'.$product->slug) }}">@if($image)<img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" loading="lazy" width="400" height="400">@endif</a><button class="ss-quick" type="button" @if($campaignHomeCards ?? false) wire:click="openPreview({{ $product->id }})" @else wire:click="$dispatch('open-product-quick-view', { productId: {{ $product->id }} })" @endif>Xem nhanh</button></div>
                    <a class="ss-name" href="{{ url('/products/'.$product->slug) }}">{{ $product->name }}</a>
                    <div class="ss-card-bottom"><strong>@if($available->count()>1)<small class="bf-from">Từ </small>@endif{{ number_format($variant?->price ?? $product->price,0,',','.') }}₫</strong>@include('livewire.partials.purchase-card-button',['purchaseCardProduct'=>$product])</div>
                </article>
                @endforeach
