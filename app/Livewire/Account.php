<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Url;
use Livewire\Component;

class Account extends Component
{
    #[Url(as: 'tab', except: 'overview', history: true)]
    public string $activeTab = 'overview';

    // Filter tab for orders (lưu lại trạng thái hoạt động)
    #[Url(as: 'status', except: 'all', history: true)]
    public string $orderStatusFilter = 'all';

    #[Url(as: 'search', except: '', history: true)]
    public string $orderSearch = '';

    #[Url(as: 'from', except: '', history: true)]
    public string $dateFrom = '';

    #[Url(as: 'to', except: '', history: true)]
    public string $dateTo = '';

    // Warranty Claim / Service tracking (Quá trình người mua gửi bảo hành & trạng thái thông báo)
    #[Url(as: 'claim_status', except: 'all', history: true)]
    public string $warrantyClaimFilter = 'all';

    // Profile edit form
    public string $name = '';
    public string $phone = '';
    public string $detailed_address = '';
    public string $province = '';
    public string $district = '';
    public string $ward = '';
    public bool $profileUpdated = false;

    // Password change form
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    public bool $passwordUpdated = false;

    public function mount(): void
    {
        $validTabs = ['overview', 'orders', 'warranty', 'profile', 'password'];
        $requestedTab = request()->query('tab', $this->activeTab);
        if (in_array($requestedTab, $validTabs, true)) {
            $this->activeTab = $requestedTab;
        }

        $user = Auth::user();
        if ($user) {
            $this->name = (string) $user->name;
            $this->phone = (string) $user->phone;
            $this->detailed_address = (string) $user->detailed_address;
            $this->province = (string) $user->province;
            $this->district = (string) $user->district;
            $this->ward = (string) $user->ward;

            if ($user->phone) {
                $this->warrantyInput = (string) $user->phone;
                $this->warrantyQuery = (string) $user->phone;
            }
        }
    }

    public function setWarrantySearchType(string $type): void
    {
        if (in_array($type, ['phone', 'code'], true)) {
            $this->warrantySearchType = $type;
        }
    }

    public function checkAccountWarranty(): void
    {
        $this->warrantyQuery = trim($this->warrantyInput);
    }

    public function clearAccountWarrantyQuery(): void
    {
        $this->warrantyQuery = '';
        $this->warrantyInput = '';
    }

    public function setTab(string $tab): void
    {
        $validTabs = ['overview', 'orders', 'warranty', 'profile', 'password'];
        if (in_array($tab, $validTabs, true)) {
            $this->activeTab = $tab;
            $this->profileUpdated = false;
            $this->passwordUpdated = false;
        }
    }

    public function setOrderStatusFilter(string $status): void
    {
        $this->orderStatusFilter = $status;
    }

    public function setWarrantyClaimFilter(string $status): void
    {
        $this->warrantyClaimFilter = $status;
    }

    public static function mapClaimStatus(Order $order): string
    {
        $st = strtolower((string) $order->status);
        return match ($st) {
            'delivered', 'completed' => 'returned',
            'fixed', 'resolved' => 'fixed',
            'repairing' => 'repairing',
            'dispatching', 'shipped', 'shipping' => 'dispatching',
            'processing' => 'repairing',
            default => 'received',
        };
    }

    public static function getClaimStatusMeta(string $statusKey): array
    {
        return match ($statusKey) {
            'returned' => ['label' => 'Đã trả khách', 'class' => 'is-returned'],
            'fixed' => ['label' => 'Đã sửa xong', 'class' => 'is-fixed'],
            'repairing' => ['label' => 'Đang sửa', 'class' => 'is-repairing'],
            'dispatching' => ['label' => 'Đang điều phối', 'class' => 'is-dispatching'],
            default => ['label' => 'Đã tiếp nhận', 'class' => 'is-received'],
        };
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\s.-]{9,15}$/'],
            'detailed_address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->update([
                'name' => trim($this->name),
                'phone' => preg_replace('/\s+/', '', trim($this->phone)),
                'detailed_address' => trim($this->detailed_address),
            ]);
            $this->profileUpdated = true;
        }
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user = Auth::user();
        if (!$user || !Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Mật khẩu hiện tại không chính xác.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->passwordUpdated = true;
    }

    public static function getWarrantyStatus(OrderItem $item): array
    {
        $order = $item->order;
        $isDelivered = $order && in_array(strtolower((string) $order->status), ['delivered', 'completed'], true);
        $isManualBH = $order && str_starts_with((string) $order->order_code, 'BH');
        $hasWarranty = !empty($item->warranty);

        if (!$hasWarranty && !$isDelivered && !$isManualBH) {
            return [
                'state' => 'pending',
                'label' => 'Chưa kích hoạt',
                'class' => 'pending',
                'end_formatted' => 'Chờ kích hoạt',
                'days_left' => 0,
            ];
        }

        $end = $hasWarranty ? \Illuminate\Support\Carbon::parse($item->warranty) : null;
        if ($end && $end->isFuture()) {
            $days = max(0, (int) now()->diffInDays($end, false));
            return [
                'state' => 'active',
                'label' => 'Đang bảo hành',
                'class' => 'active',
                'end_formatted' => $end->format('d/m/Y'),
                'days_left' => $days,
            ];
        }

        return [
            'state' => 'expired',
            'label' => 'Hết hạn bảo hành',
            'class' => 'expired',
            'end_formatted' => $end ? $end->format('d/m/Y') : 'Đã kết thúc',
            'days_left' => 0,
        ];
    }

    public function render()
    {
        $user = Auth::user();
        $userPhone = $user ? preg_replace('/\D+/', '', (string) $user->phone) : '';
        $corePhone = strlen($userPhone) >= 9 ? substr($userPhone, -9) : null;

        // Fetch all orders for current user (or matched by phone)
        $allOrdersQuery = Order::with(['items.product_variant.product', 'address'])
            ->where(function ($q) use ($user, $corePhone) {
                if ($user) {
                    $q->where('user_id', $user->id);
                }
                if ($corePhone) {
                    $q->orWhereHas('address', fn ($a) => $a->where('phone', 'like', '%' . $corePhone));
                }
            })
            ->latest('created_at');

        $allOrders = $allOrdersQuery->get();

        // Recent orders for Overview tab
        $recentOrders = $allOrders->take(4);

        // TAB 2: Filtered orders for Lịch sử mua hàng (CellphoneS style)
        $filteredOrders = $allOrders;
        if ($this->orderStatusFilter !== 'all') {
            $filteredOrders = $filteredOrders->filter(function (Order $order) {
                $group = match (strtolower((string) $order->status)) {
                    'shipped', 'shipping' => 'shipping',
                    'delivered', 'completed' => 'delivered',
                    'cancelled', 'canceled' => 'cancelled',
                    'processing' => 'processing',
                    default => 'new',
                };
                return $group === $this->orderStatusFilter;
            });
        }
        if ($this->dateFrom !== '') {
            try {
                $from = \Illuminate\Support\Carbon::parse($this->dateFrom)->startOfDay();
                $filteredOrders = $filteredOrders->filter(fn (Order $o) => $o->created_at && $o->created_at->gte($from));
            } catch (\Throwable $e) {}
        }
        if ($this->dateTo !== '') {
            try {
                $to = \Illuminate\Support\Carbon::parse($this->dateTo)->endOfDay();
                $filteredOrders = $filteredOrders->filter(fn (Order $o) => $o->created_at && $o->created_at->lte($to));
            } catch (\Throwable $e) {}
        }
        if (trim($this->orderSearch) !== '') {
            $term = mb_strtolower(trim($this->orderSearch));
            $filteredOrders = $filteredOrders->filter(function (Order $order) use ($term) {
                if (str_contains(mb_strtolower((string) $order->order_code), $term)) return true;
                foreach ($order->items as $item) {
                    if (str_contains(mb_strtolower((string) ($item->product_variant?->product?->name ?? '')), $term)) return true;
                    if (str_contains(mb_strtolower((string) ($item->product_variant?->sku ?? '')), $term)) return true;
                }
                return false;
            });
        }

        $purchaseOrderCounts = [
            'all' => $allOrders->count(),
            'new' => $allOrders->filter(fn ($o) => in_array(strtolower((string) $o->status), ['new', 'pending', 'confirmed']))->count(),
            'processing' => $allOrders->filter(fn ($o) => strtolower((string) $o->status) === 'processing')->count(),
            'shipping' => $allOrders->filter(fn ($o) => in_array(strtolower((string) $o->status), ['shipping', 'shipped']))->count(),
            'delivered' => $allOrders->filter(fn ($o) => in_array(strtolower((string) $o->status), ['delivered', 'completed']))->count(),
            'cancelled' => $allOrders->filter(fn ($o) => in_array(strtolower((string) $o->status), ['cancelled', 'canceled']))->count(),
        ];

        // TAB 3: Warranty Claims / Service tracking (Quá trình gửi bảo hành & trạng thái thông báo)
        $warrantyClaimOrders = $allOrders->filter(function (Order $o) {
            return str_starts_with((string) $o->order_code, 'BH') || str_contains(mb_strtolower((string) $o->notes), 'bảo hành');
        });

        $claimCounts = [
            'all' => $warrantyClaimOrders->count(),
            'received' => $warrantyClaimOrders->filter(fn ($o) => self::mapClaimStatus($o) === 'received')->count(),
            'dispatching' => $warrantyClaimOrders->filter(fn ($o) => self::mapClaimStatus($o) === 'dispatching')->count(),
            'repairing' => $warrantyClaimOrders->filter(fn ($o) => self::mapClaimStatus($o) === 'repairing')->count(),
            'fixed' => $warrantyClaimOrders->filter(fn ($o) => self::mapClaimStatus($o) === 'fixed')->count(),
            'returned' => $warrantyClaimOrders->filter(fn ($o) => self::mapClaimStatus($o) === 'returned')->count(),
        ];

        $filteredClaims = $warrantyClaimOrders;
        if ($this->warrantyClaimFilter !== 'all') {
            $filteredClaims = $filteredClaims->filter(fn ($o) => self::mapClaimStatus($o) === $this->warrantyClaimFilter);
        }

        $settings = Setting::query()->first();

        return view('livewire.account', [
            'user' => $user,
            'recentOrders' => $recentOrders,
            'orders' => $filteredOrders,
            'allOrdersCount' => $allOrders->count(),
            'purchaseOrderCounts' => $purchaseOrderCounts,
            'warrantyClaimOrders' => $warrantyClaimOrders,
            'filteredClaims' => $filteredClaims,
            'claimCounts' => $claimCounts,
            'activeWarrantyCount' => $warrantyClaimOrders->count(),
            'settings' => $settings,
        ]);
    }
}
