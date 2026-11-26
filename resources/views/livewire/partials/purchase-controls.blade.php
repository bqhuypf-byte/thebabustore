@php
    $purchaseMessage = \App\Support\PurchaseValidation::message($purchaseProduct, $purchaseOptions);
    $purchaseLimit = \App\Support\PurchaseValidation::limit($purchaseVariant);
    $purchaseOos = $purchaseMessage === 'Hết hàng';
@endphp
<div class="purchase-validation" aria-live="polite">
    @if($purchaseVariant)<p class="purchase-variant-meta">{{ \App\Support\PurchaseValidation::available($purchaseVariant) ? 'Còn hàng' : 'Hết hàng' }}</p>@endif
    @if($purchaseMessage)<p class="purchase-message">{{ $purchaseMessage }}</p>@endif
    @if($purchaseVariant && $purchaseVariant->stock_quantity !== null && $purchaseLimit > 0 && $purchaseLimit <= 5)<p class="purchase-low-stock">Chỉ còn {{ $purchaseLimit }} sản phẩm</p>@endif
    @error('purchase')<p class="purchase-error" role="alert">{{ $message }}</p>@enderror
</div>
<div class="{{ $quantityClass ?? 'qv-quantity' }} purchase-quantity">
    <label for="purchase-quantity-{{ $quantityProperty }}">Số lượng:</label>
    <button type="button" wire:click="changePurchaseQuantity(-1)" @disabled($purchaseMessage || (int)${$quantityProperty} <= 1) aria-label="Giảm số lượng">−</button>
    <input id="purchase-quantity-{{ $quantityProperty }}" type="number" min="1" max="{{ max(1, $purchaseLimit) }}" step="1" inputmode="numeric" wire:model.live.debounce.250ms="{{ $quantityProperty }}" @disabled((bool)$purchaseMessage) aria-label="Số lượng">
    <button type="button" wire:click="changePurchaseQuantity(1)" @disabled($purchaseMessage || (int)${$quantityProperty} >= $purchaseLimit) aria-label="Tăng số lượng">+</button>
</div>
<div class="{{ $actionsClass ?? 'qv-actions' }} purchase-actions">
    <button type="button" class="{{ $addClass ?? '' }}" wire:click="{{ $addAction }}" wire:loading.attr="disabled" wire:target="{{ $addAction }},{{ $buyAction }},{{ $quantityProperty }}" @disabled((bool)$purchaseMessage)><span>{{ $purchaseOos ? 'Hết hàng' : 'Thêm vào giỏ' }}</span></button>
    <button type="button" class="{{ $buyClass ?? '' }}" wire:click="{{ $buyExpression }}" wire:loading.attr="disabled" wire:target="{{ $addAction }},{{ $buyAction }},{{ $quantityProperty }}" @disabled((bool)$purchaseMessage)><span>{{ $purchaseOos ? 'Hết hàng' : 'Mua ngay' }}</span></button>
</div>
