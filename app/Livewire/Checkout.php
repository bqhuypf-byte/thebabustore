<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Models\Address;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class Checkout extends Component
{
    use LivewireAlert; // Thêm trait LivewireAlert

    public $shipping_amount = 30000; // Set your shipping fee here
    public $shipping_method = 'home_delivery'; // Set your shipping fee here
    public $subtotal; // To hold the subtotal amount
    public $profit_loss; // To hold the subtotal amount
    public $note; // To hold the total amount including shipping
    public $total; // To hold the total amount including shipping
    public $shippingAddress = [
        'full_name' => '',
        'phone' => '',
        'province' => '',
        'district' => '',
        'ward' => '',
        'detailed_address' => '',
    ];
    public $paymentMethod = 'bank'; // Để lưu phương thức thanh toán đã chọn

    public $cartItems = []; // Property to hold cart items

    public function mount()
    {

        $this->cartItems = CartManagement::getCartItemsFromCookie(); // Retrieve cart items from cookie
        if (empty($this->cartItems)) {
            // Chuyển trang đến trang sản phẩm hoặc trang giỏ hàng
            return redirect('/cart'); // Thay 'cart' bằng route mà bạn muốn chuyển đến
        }
        $this->calculateTotals(); // Calculate the totals based on fetched items

        if (auth()->check()) {
            $user = auth()->user();
            $this->shippingAddress['full_name'] = $user->name ?? '';
            $this->shippingAddress['phone'] = $user->phone ?? '';
            $this->shippingAddress['province'] = $user->province ?? '';
            $this->shippingAddress['district'] = $user->district ?? '';
            $this->shippingAddress['ward'] = $user->ward ?? '';
            $this->shippingAddress['detailed_address'] = $user->detailed_address ?? '';
        }
    }
    public function calculateTotals()
    {
        $this->subtotal = 0; // Initialize subtotal to zero
        $this->profit_loss = 0; // Initialize subtotal to zero
        foreach ($this->cartItems as $item) {
            $this->subtotal += $item['total_amount']; // Sum the total amount of each item
            $this->profit_loss += ($item['price'] - $item['discount_price']) * $item['quantity']; // Tính lãi/lỗ
        }
        // dd($this->profit_loss);
        $this->total = $this->subtotal; // Calculate total including shipping fee
    }

    #[On('cart-updated')]
    public function refreshCart(): void
    {
        $this->cartItems = CartManagement::getCartItemsFromSession();
        $this->calculateTotals();
    }

    // Increment the quantity of the specified product
    public function incrementQuantity($product_id)
    {
        $this->cartItems = CartManagement::incrementQuantityToCartItem($product_id); // Update cart items after increment
        $this->calculateTotals(); // Recalculate totals after updating quantity
    }

    // Decrement the quantity of the specified product
    public function decrementQuantity($product_id)
    {
        $this->cartItems = CartManagement::decrementQuantityToCartItem($product_id); // Update cart items after decrement
        $this->calculateTotals(); // Recalculate totals after updating quantity
    }

    // Remove an item from the cart
    public function removeItem($product_id)
    {
        $this->cartItems = CartManagement::removeCartItem($product_id); // Update cart items after removal
        $this->calculateTotals(); // Recalculate totals after removing an item
    }
    public function placeOrder()
    {
        $this->validate([
            'shippingAddress.full_name' => 'required|string|max:255',
            'shippingAddress.phone' => 'required|string|max:30',
            'shippingAddress.province' => 'required|string|max:255',
            'shippingAddress.district' => 'required|string|max:255',
            'shippingAddress.ward' => 'required|string|max:255',
            'shippingAddress.detailed_address' => 'required|string|max:2000',
            'shipping_method' => 'required|in:home_delivery,in_store_pickup',
            'paymentMethod' => 'required|in:bank,cod',
            'note' => 'nullable|string|max:2000',
        ]);

        // Re-read prices and quantities from the server-side cart before creating an order.
        $this->cartItems = CartManagement::getCartItemsFromSession();
        $this->calculateTotals();
        if (count($this->cartItems) !== count(CartManagement::getRawCartItemsFromSession())) {
            throw ValidationException::withMessages(['cart' => 'Một sản phẩm trong giỏ hàng không còn tồn tại. Vui lòng kiểm tra lại giỏ hàng.']);
        }
        // Kiểm tra xem giỏ hàng có trống không
        if (empty($this->cartItems)) {
            $this->alert('error', 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi đặt hàng.');
            return; // Ngừng thực hiện nếu giỏ hàng trống
        }

        // Thực hiện giao dịch cơ sở dữ liệu để đảm bảo tính nhất quán
        $order = DB::transaction(function () {
            $ids = collect($this->cartItems)->pluck('product_variant_id')->all();
            $variants = ProductVariant::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');
            foreach ($this->cartItems as &$item) {
                $variant = $variants->get($item['product_variant_id']);
                if (!$variant || $variant->stock !== 'in_stock') {
                    throw ValidationException::withMessages(['cart' => 'Một phiên bản trong giỏ hàng đã hết hàng. Vui lòng kiểm tra lại giỏ hàng.']);
                }
                $item['price'] = $item['unit_amount'] = (float) $variant->price;
                $item['discount_price'] = (float) $variant->discount_price;
                $item['total_amount'] = $item['unit_amount'] * $item['quantity'];
            }
            unset($item);
            $this->calculateTotals();
            $order_code = $this->generateUniqueOrderCode();

            // Tạo đơn hàng
            if ($this->total > 800000 && $this->paymentMethod !== 'cod') {
                $this->shipping_amount = 0;
            } else {
                $this->shipping_amount = 30000;
            }

            // Create the order
            $order = Order::create([
                'user_id' => auth()->id() ?? 1,
                'grand_total' => $this->total,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'pending',
                'status' => 'new',
                'currency' => 'VND',
                'shipping_method' => $this->shipping_method,
                'order_code' => $order_code,
                'notes' => $this->note,
                'profit_loss' => $this->profit_loss,
                'shipping_amount' => $this->shipping_method === 'in_store_pickup' ? 0 : $this->shipping_amount,
                'deposit_amount' => $this->paymentMethod == 'cod' ? 50000 : 0,
                'deposit_status' => 'pending',
            ]);

            // Tạo địa chỉ giao hàng
            Address::create([
                'order_id' => $order->id,
                'full_name' => $this->shippingAddress['full_name'],
                'phone' => $this->shippingAddress['phone'],
                'province' => $this->shippingAddress['province'],
                'district' => $this->shippingAddress['district'],
                'ward' => $this->shippingAddress['ward'],
                'detailed_address' => $this->shippingAddress['detailed_address'],
            ]);
                // dd($order);
            // Tạo mục đơn hàng cho từng sản phẩm trong giỏ
            foreach ($this->cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'profit_loss' => ($item['unit_amount'] - $item['discount_price']) * $item['quantity'],
                    'unit_amount' => $item['unit_amount'],
                    'total_amount' => $item['total_amount'],
                    'warranty' => now()->addDays($item['warranty_days']) ,
                ]);
            }
            if (auth()->check()) {
                $user = auth()->user();
                $user->update([
                    'name' => $this->shippingAddress['full_name'],
                    'phone' => $this->shippingAddress['phone'],
                    'province' => $this->shippingAddress['province'],
                    'district' => $this->shippingAddress['district'],
                    'ward' => $this->shippingAddress['ward'],
                    'detailed_address' => $this->shippingAddress['detailed_address'],
                ]);
            }
            return $order;
            // if($this->paymentMethod == 'bank'){
            //     return redirect()->route('bank', ['orderCode' => $order->order_code]);
            // }else{
            //     return redirect()->route('thanks', ['orderCode' => $order->order_code]);
            // }


        });
        CartManagement::clearCartItems();

        return redirect()->route('bank', ['orderCode' => $order->order_code]);
    }
    protected function generateUniqueOrderCode()
    {
        do {
            $uniqueNumber = mt_rand(1000000000, 9999999999); // Tạo số ngẫu nhiên 10 chữ số
            $order_code = 'WEB03' . $uniqueNumber; // Tạo mã đơn hàng
        } while (Order::where('order_code', $order_code)->exists()); // Kiểm tra xem mã có tồn tại trong DB không

        return $order_code; // Trả về mã đơn hàng duy nhất
    }
    public function render()
    {
        // dd($this->cartItems);
        return view('livewire.store-checkout', [
            'cartItems' => $this->cartItems, // Pass cart items to the view
        ]);
    }
}
