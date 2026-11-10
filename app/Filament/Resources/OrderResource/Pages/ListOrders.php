<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStat;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_activation_link')
                ->label('⚡ Tạo Link Bán Hàng / Kích Hoạt BH')
                ->color('success')
                ->icon('heroicon-o-shield-check')
                ->modalHeading('Tạo nhanh Link Kích Hoạt Sản Phẩm & Bảo Hành')
                ->modalDescription('Chọn mã sản phẩm (SKU) bán cho khách. Hệ thống sẽ tạo mã đơn kích hoạt để bạn gửi link cho khách tự điền Họ tên & SĐT kích hoạt bảo hành.')
                ->modalSubmitActionLabel('Tạo & Lấy Link Kích Hoạt')
                ->form([
                    Forms\Components\Select::make('product_variant_id')
                        ->label('Chọn Sản Phẩm / Biến Thể (Mã SKU)')
                        ->options(function (): array {
                            return ProductVariant::with('product')
                                ->latest('id')
                                ->get()
                                ->mapWithKeys(function (ProductVariant $v): array {
                                    $prodName = $v->product?->name ?? 'Sản phẩm';
                                    $classLabel = OrderResource::formatVariantClassification($v);
                                    $price = number_format((float) ($v->price ?: $v->discount_price), 0, ',', '.') . '₫';
                                    return [$v->id => "[SKU: {$v->sku}] {$prodName} — {$classLabel} ({$price})"];
                                })
                                ->all();
                        })
                        ->searchable()
                        ->required(),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Số lượng')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),

                        Forms\Components\Select::make('payment_status')
                            ->label('Trạng thái thanh toán')
                            ->options([
                                'paid' => 'Đã Thanh Toán',
                                'pending' => 'Chưa Thanh Toán (COD)',
                            ])
                            ->default('paid')
                            ->required(),
                    ]),
                ])
                ->action(function (array $data): void {
                    $variant = ProductVariant::with('product')->findOrFail($data['product_variant_id']);
                    $qty = max(1, (int) ($data['quantity'] ?? 1));
                    $unitPrice = (float) ($variant->price ?: ($variant->discount_price ?? 0));
                    $costPrice = (float) ($variant->discount_price ?? 0);
                    $lineTotal = $unitPrice * $qty;
                    $profit = $costPrice > 0 ? (($unitPrice - $costPrice) * $qty) : 0;

                    $order = DB::transaction(function () use ($variant, $qty, $unitPrice, $lineTotal, $profit, $data) {
                        $userId = Auth::id() ?: (User::query()->value('id') ?: 1);
                        $orderCode = 'BH' . now()->format('ymdHis') . random_int(10, 99);

                        $newOrder = Order::create([
                            'user_id' => $userId,
                            'order_code' => $orderCode,
                            'grand_total' => $lineTotal,
                            'payment_method' => 'cod',
                            'payment_status' => $data['payment_status'] ?? 'paid',
                            'status' => 'new',
                            'currency' => 'VND',
                            'shipping_amount' => 0,
                            'shipping_method' => 'in_store_pickup',
                            'profit_loss' => $profit,
                            'deposit_amount' => 0,
                            'deposit_status' => 'paid',
                            'notes' => 'Tạo link kích hoạt cho SKU: ' . $variant->sku,
                        ]);

                        Address::create([
                            'order_id' => $newOrder->id,
                            'full_name' => 'Khách chờ kích hoạt (' . $variant->sku . ')',
                            'phone' => '0000000000',
                            'province' => '',
                            'district' => '',
                            'ward' => '',
                            'detailed_address' => 'Chờ khách điền thông tin qua link kích hoạt bảo hành',
                        ]);

                        OrderItem::create([
                            'order_id' => $newOrder->id,
                            'product_variant_id' => $variant->id,
                            'quantity' => $qty,
                            'unit_amount' => $unitPrice,
                            'total_amount' => $lineTotal,
                            'profit_loss' => $profit,
                            'warranty' => now(),
                        ]);

                        return $newOrder;
                    });

                    $link = url('/kich-hoat/' . $order->order_code);

                    Notification::make()
                        ->title('Đã tạo Link Kích Hoạt: ' . $order->order_code)
                        ->body('Gửi link này cho khách điền thông tin: ' . $link)
                        ->persistent()
                        ->success()
                        ->send();
                }),

            Actions\Action::make('create_member_order')
                ->label('👤 Tạo đơn giùm khách')
                ->color('info')
                ->icon('heroicon-o-user-plus')
                ->modalHeading('Tạo đơn hàng giùm khách (Thành viên)')
                ->modalDescription('Nhập SĐT khách: nếu đã là thành viên sẽ tự gắn đơn vào tài khoản, nếu chưa sẽ tự tạo tài khoản thành viên mới. Bảo hành được kích hoạt ngay.')
                ->modalWidth('3xl')
                ->modalSubmitActionLabel('Tạo đơn hàng')
                ->form([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Số điện thoại khách')
                            ->tel()
                            ->required()
                            ->regex('/^[0-9+\s.-]{9,15}$/')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $digits = preg_replace('/\D+/', '', (string) $state);
                                if (strlen($digits) < 9) {
                                    return;
                                }
                                $user = User::where('phone', $digits)->orWhere('phone', 'like', '%' . substr($digits, -9))->first();
                                if ($user) {
                                    $set('full_name', $user->name);
                                    $set('detailed_address', $user->detailed_address);
                                }
                            })
                            ->helperText('Thành viên cũ sẽ tự điền tên & địa chỉ.'),
                        Forms\Components\TextInput::make('full_name')
                            ->label('Họ tên khách')
                            ->required(),
                        Forms\Components\TextInput::make('detailed_address')
                            ->label('Địa chỉ')
                            ->columnSpanFull(),
                    ]),
                    Forms\Components\Repeater::make('items')
                        ->label('Sản phẩm')
                        ->schema([
                            Forms\Components\Select::make('product_variant_id')
                                ->label('Sản phẩm / SKU')
                                ->options(fn (): array => ProductVariant::with('product')->latest('id')->get()
                                    ->mapWithKeys(fn (ProductVariant $v) => [$v->id => "[{$v->sku}] " . ($v->product?->name ?? '') . ' — ' . OrderResource::formatVariantClassification($v) . ' (' . number_format((float) $v->price, 0, ',', '.') . '₫)'])
                                    ->all())
                                ->searchable()
                                ->required()
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('quantity')
                                ->label('SL')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->required(),
                        ])
                        ->columns(4)
                        ->minItems(1)
                        ->defaultItems(1)
                        ->addActionLabel('Thêm sản phẩm'),
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái đơn')
                            ->options([
                                'new' => 'Chờ xác nhận',
                                'processing' => 'Xác nhận & Đóng gói',
                                'shipped' => 'Đang vận chuyển',
                                'delivered' => 'Giao thành công',
                            ])
                            ->default('delivered')
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Thanh toán')
                            ->options(['paid' => 'Đã Thanh Toán', 'pending' => 'Chưa Thanh Toán'])
                            ->default('paid')
                            ->required(),
                        Forms\Components\TextInput::make('shipping_amount')
                            ->label('Phí ship')
                            ->numeric()
                            ->default(0),
                    ]),
                    Forms\Components\Textarea::make('notes')->label('Ghi chú')->rows(2),
                ])
                ->action(function (array $data): void {
                    $digits = preg_replace('/\D+/', '', (string) $data['phone']);

                    [$order, $isNewMember] = DB::transaction(function () use ($data, $digits) {
                        $user = User::where('phone', $digits)->orWhere('phone', 'like', '%' . substr($digits, -9))->first();
                        $isNew = false;
                        if (!$user) {
                            $user = User::create([
                                'name' => trim($data['full_name']),
                                'email' => $digits . '@member.thebabustore.vn',
                                'password' => \Illuminate\Support\Facades\Hash::make($digits),
                                'phone' => $digits,
                                'detailed_address' => $data['detailed_address'] ?? null,
                            ]);
                            $isNew = true;
                        }

                        $total = 0;
                        $profitTotal = 0;
                        $lines = [];
                        foreach ($data['items'] as $row) {
                            $variant = ProductVariant::findOrFail($row['product_variant_id']);
                            $qty = max(1, (int) $row['quantity']);
                            $price = (float) $variant->price;
                            $cost = (float) ($variant->discount_price ?? 0);
                            $profit = $cost > 0 ? ($price - $cost) * $qty : 0;
                            $days = (int) ($variant->warranty_days ?: 365);
                            $lines[] = [$variant, $qty, $price, $profit, $days];
                            $total += $price * $qty;
                            $profitTotal += $profit;
                        }
                        $shipping = (float) ($data['shipping_amount'] ?? 0);

                        $order = Order::create([
                            'user_id' => $user->id,
                            'order_code' => 'WEB' . now()->format('ymdHis') . random_int(10, 99),
                            'grand_total' => $total + $shipping,
                            'payment_method' => 'cod',
                            'payment_status' => $data['payment_status'],
                            'status' => $data['status'],
                            'currency' => 'VND',
                            'shipping_amount' => $shipping,
                            'shipping_method' => $shipping > 0 ? 'home_delivery' : 'in_store_pickup',
                            'profit_loss' => $profitTotal,
                            'deposit_amount' => 0,
                            'deposit_status' => 'paid',
                            'notes' => trim('Admin tạo đơn giùm khách. ' . ($data['notes'] ?? '')),
                        ]);

                        Address::create([
                            'order_id' => $order->id,
                            'full_name' => trim($data['full_name']),
                            'phone' => $digits,
                            'province' => '',
                            'district' => '',
                            'ward' => '',
                            'detailed_address' => $data['detailed_address'] ?: 'Nhận tại cửa hàng',
                        ]);

                        foreach ($lines as [$variant, $qty, $price, $profit, $days]) {
                            OrderItem::create([
                                'order_id' => $order->id,
                                'product_variant_id' => $variant->id,
                                'quantity' => $qty,
                                'unit_amount' => $price,
                                'total_amount' => $price * $qty,
                                'profit_loss' => $profit,
                                'warranty' => now()->addDays($days > 0 ? $days : 365),
                            ]);
                        }

                        return [$order, $isNew];
                    });

                    Notification::make()
                        ->title('Đã tạo đơn ' . $order->order_code)
                        ->body($isNewMember
                            ? "Đã tạo thành viên mới. Đăng nhập: {$digits}@member.thebabustore.vn / mật khẩu: {$digits}. Khách tra cứu bảo hành bằng SĐT tại /orders."
                            : 'Đã gắn vào tài khoản thành viên có sẵn. Khách tra cứu bảo hành bằng SĐT tại /orders.')
                        ->persistent()
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStat::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            Tab::make('Tất Cả'),
            'new' => Tab::make('Chờ xác nhận')->query(fn ($query) => $query->where('status', 'new')),
            'processing' => Tab::make('Xác nhận & Đóng gói')->query(fn ($query) => $query->where('status', 'processing')),
            'shipped' => Tab::make('Đang vận chuyển')->query(fn ($query) => $query->where('status', 'shipped')),
            'delivered' => Tab::make('Giao thành công')->query(fn ($query) => $query->where('status', 'delivered')),
            'cancelled' => Tab::make('Đã hủy')->query(fn ($query) => $query->where('status', 'cancelled')),
            'warranty' => Tab::make('Đơn Bảo Hành (RMA)')
                ->badge(fn () => Order::where('order_code', 'like', 'BH%')->orWhere('notes', 'like', '%bảo hành%')->count())
                ->badgeColor('danger')
                ->query(fn ($query) => $query->where('order_code', 'like', 'BH%')->orWhere('notes', 'like', '%bảo hành%')),
        ];
    }

    public function updateOrderStatus(int $orderId, string $status): void
    {
        $allowed = ['new', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            return;
        }

        $order->update(['status' => $status]);

        Notification::make()
            ->title('Đã cập nhật trạng thái đơn hàng ' . ($order->order_code ?: '#' . $order->id))
            ->success()
            ->send();
    }

    public function updatePaymentStatus(int $orderId, string $paymentStatus): void
    {
        $allowed = ['pending', 'paid', 'failed'];
        if (!in_array($paymentStatus, $allowed, true)) {
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            return;
        }

        $order->update(['payment_status' => $paymentStatus]);

        Notification::make()
            ->title('Đã cập nhật thanh toán đơn hàng ' . ($order->order_code ?: '#' . $order->id))
            ->success()
            ->send();
    }
}
