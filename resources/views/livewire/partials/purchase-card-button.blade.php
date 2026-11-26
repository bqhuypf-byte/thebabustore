@php
    $cardAvailable = $purchaseCardProduct->variants->contains(fn ($variant) => \App\Support\PurchaseValidation::available($variant));
    $cardVariantCount = $purchaseCardProduct->variants()->count();
    $cardBuyNow = (bool) $purchaseCardProduct->on_sale;
    $cardToken = (string) \Illuminate\Support\Str::uuid();
@endphp
<button type="button" class="{{ $purchaseCardClass ?? '' }}" wire:click="purchaseCard({{ $purchaseCardProduct->id }}, '{{ $cardToken }}', {{ $cardBuyNow ? 'true' : 'false' }})" wire:loading.attr="disabled" wire:target="purchaseCard" @disabled(!$cardAvailable)>
    <span>{{ !$cardAvailable ? 'Hết hàng' : ($cardBuyNow ? 'Mua ngay' : ($cardVariantCount === 1 ? 'Thêm vào giỏ' : 'Chọn phiên bản')) }}</span>
</button>
