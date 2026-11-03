<?php

namespace App\Livewire\Inc;

use App\Models\Activity;
use Livewire\Component;
use App\Models\Category; // Thêm dòng này để sử dụng mô hình Category
use App\Helpers\CartManagement;

class Header extends Component
{
    public $cartItems = [];
    public $totalPrice = 0;
    public $totalQuantity = 0; // Thêm thuộc tính để lưu tổng số sản phẩm
    public $categories = []; // Khai báo biến categories

    public function mount()
    {
        // Lấy giỏ hàng từ cookie
        $this->cartItems = CartManagement::getCartItemsFromCookie();
        $this->calculateTotal();
        $this->calculateTotalQuantity(); // Tính tổng số lượng sản phẩm
        $this->categories = Category::with('subcategories')
        ->where('show_in_header', true) // Chỉ lấy các danh mục có show_in_header = 1
        ->orderBy('ordinal_number', 'asc') // Đặt thứ tự trước khi lấy
        ->get();

        $today = now()->format('Y-m-d');

        $activity = Activity::where('date', $today)->first();
        if ($activity) {
            $activity->increment('view_count');
        } else {
            Activity::create([
                'view_count' => 1,
                'date' => $today,
            ]);
        }
    }

    public function calculateTotal()
    {
        $this->totalPrice = array_reduce($this->cartItems, function ($carry, $item) {
            return $carry + ($item['total_amount'] ?? ($item['price'] * $item['quantity']));
        }, 0);
    }

    public function calculateTotalQuantity()
    {
        $this->totalQuantity = array_reduce($this->cartItems, function ($carry, $item) {
            return $carry + $item['quantity'];
        }, 0);
    }

    public function incrementQuantity($productId)
    {
        $this->cartItems = CartManagement::incrementQuantityToCartItem($productId);
        $this->calculateTotal(); // Cập nhật tổng
        $this->calculateTotalQuantity(); // Cập nhật tổng số lượng
    }

    public function decrementQuantity($productId)
    {
        $this->cartItems = CartManagement::decrementQuantityToCartItem($productId);
        $this->calculateTotal(); // Cập nhật tổng
        $this->calculateTotalQuantity(); // Cập nhật tổng số lượng
    }

    public function removeItem($productId)
    {
        $this->cartItems = CartManagement::removeCartItem($productId);
        $this->calculateTotal(); // Cập nhật tổng
        $this->calculateTotalQuantity(); // Cập nhật tổng số lượng
    }

    public function render()
    {
        return view('livewire.inc.header', [
            'cartItems' => $this->cartItems,
            'totalPrice' => $this->totalPrice,
            'totalQuantity' => $this->totalQuantity, // Truyền tổng số lượng sản phẩm sang view
            'categories' => $this->categories, // Truyền danh mục sang view
        ]);
    }
}
