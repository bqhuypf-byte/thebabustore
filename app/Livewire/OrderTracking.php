<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class OrderTracking extends Component
{
    #[Url(as: 'q', except: '', history: true)]
    public string $query = '';

    #[Url(as: 'status', except: 'all', history: true)]
    public string $statusFilter = 'all';

    // Giữ tương thích nếu có link/test truyền orderCode hoặc phone
    public string $orderCode = '';
    public string $phone = '';

    public function mount(): void
    {
        if ($this->query === '') {
            $fromRequest = trim((string) request()->query('order_code', request()->query('phone', '')));
            if ($fromRequest !== '') {
                $this->query = $fromRequest;
            }
        }
    }

    public function setStatusFilter(string $status): void
    {
        if (in_array($status, ['all', 'pending', 'shipping', 'completed', 'cancelled'], true)) {
            $this->statusFilter = $status;
        }
    }

    public function refreshOrders(): void
    {
        // Trigger re-render to fetch latest order statuses from DB
    }

    public function clearSearch(): void
    {
        $this->query = '';
        $this->statusFilter = 'all';
    }

    public static function normalizeStatusGroup(?string $status): string
    {
        return match (strtolower(trim((string) $status))) {
            'shipped', 'shipping' => 'shipping',
            'delivered', 'completed' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            default => 'pending', // 'new', 'pending', 'processing'
        };
    }

    protected function fetchMatchedOrders(): Collection
    {
        $q = trim($this->query);
        $digits = preg_replace('/\D+/', '', $q);
        $isPhoneQuery = strlen($digits) >= 9;
        $corePhone = $isPhoneQuery ? substr($digits, -9) : null;
        $cleanCode = preg_replace('/^#/', '', $q);

        // Nếu chưa nhập từ khóa: chỉ hiển thị danh sách đơn của tài khoản đang đăng nhập
        if ($q === '') {
            if (! Auth::check()) {
                return collect();
            }

            return Order::with(['address', 'items.product_variant.product'])
                ->where('user_id', Auth::id())
                ->latest('created_at')
                ->get();
        }

        // Tránh lộ đơn hàng khi khách chưa đăng nhập mà gõ chuỗi quá ngắn (< 4 ký tự)
        if (! Auth::check() && mb_strlen($cleanCode) < 4 && ! $isPhoneQuery) {
            return collect();
        }

        return Order::with(['address', 'items.product_variant.product'])
            ->where(function ($builder) use ($cleanCode, $q, $isPhoneQuery, $corePhone, $digits) {
                if (mb_strlen($cleanCode) >= 3) {
                    $builder->where('order_code', $cleanCode)
                        ->orWhere('order_code', 'like', '%' . $cleanCode . '%')
                        ->orWhereHas('items.product_variant', function ($vq) use ($cleanCode) {
                            $vq->where('sku', $cleanCode)
                                ->orWhere('sku', 'like', '%' . $cleanCode . '%');
                        });
                }

                if ($isPhoneQuery && $corePhone) {
                    $builder->orWhereHas('address', function ($addr) use ($corePhone, $digits) {
                        $addr->where('phone', 'like', '%' . $corePhone)
                            ->orWhere('phone', $digits)
                            ->orWhere('phone', '0' . $corePhone)
                            ->orWhere('phone', '+84' . $corePhone);
                    });
                }

                if (Auth::check()) {
                    $builder->orWhere(function ($own) use ($q) {
                        $own->where('user_id', Auth::id())
                            ->whereHas('items.product_variant.product', function ($prod) use ($q) {
                                $prod->where('name', 'like', '%' . $q . '%');
                            });
                    });
                }
            })
            ->latest('created_at')
            ->limit(50)
            ->get();
    }

    public function render()
    {
        $allOrders = $this->fetchMatchedOrders();

        $counts = [
            'all' => $allOrders->count(),
            'pending' => $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === 'pending')->count(),
            'shipping' => $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === 'shipping')->count(),
            'completed' => $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === 'completed')->count(),
            'cancelled' => $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === 'cancelled')->count(),
        ];

        $filteredOrders = $this->statusFilter === 'all'
            ? $allOrders
            : $allOrders->filter(fn (Order $o) => self::normalizeStatusGroup($o->status) === $this->statusFilter)->values();

        return view('livewire.order-tracking', [
            'orders' => $filteredOrders,
            'counts' => $counts,
            'hasQuery' => trim($this->query) !== '',
        ]);
    }
}
