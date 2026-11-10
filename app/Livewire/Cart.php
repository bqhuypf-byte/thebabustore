<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use Livewire\Component;
use Livewire\Attributes\On;

class Cart extends Component
{
    public $cartItems = [];
    public $totalPrice = 0;
    public $totalQuantity = 0;

    public function mount()
    {
        $this->cartItems = CartManagement::getCartItemsFromCookie();
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->totalPrice = array_reduce($this->cartItems, function ($carry, $item) {
            return $carry + ($item['total_amount'] ?? ($item['price'] * $item['quantity']));
        }, 0);

        $this->totalQuantity = array_reduce($this->cartItems, function ($carry, $item) {
            return $carry + $item['quantity'];
        }, 0);
    }

    #[On('cart-updated')]
    public function refreshCart(): void
    {
        $this->cartItems = CartManagement::getCartItemsFromSession();
        $this->calculateTotals();
    }

    public function incrementQuantity($productId)
    {
        $this->cartItems = CartManagement::incrementQuantityToCartItem($productId);
        $this->calculateTotals();
        $this->dispatch('cart-updated');
    }

    public function decrementQuantity($productId)
    {
        $this->cartItems = CartManagement::decrementQuantityToCartItem($productId);
        $this->calculateTotals();
        $this->dispatch('cart-updated');
    }

    public function removeItem($productId)
    {
        $this->cartItems = CartManagement::removeCartItem($productId);
        $this->calculateTotals();
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.store-cart', [
            'cartItems' => $this->cartItems,
            'totalPrice' => $this->totalPrice,
            'totalQuantity' => $this->totalQuantity,
        ]);
    }
}
