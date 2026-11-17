<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;

use function Laravel\Prompts\select;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Đơn hàng';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'Quản lý bán hàng';
    protected static ?string $recordTitleAttribute = 'order_code';

    public static function getModelLabel(): string
    {
        return 'Đơn hàng';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Đơn hàng';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['order_code', 'address.phone', 'address.full_name', 'user.name'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        /** @var Order $record */
        return array_filter([
            'Mã đơn hàng' => $record->order_code,
            'Số điện thoại' => $record->address?->phone,
            'Người nhận' => $record->address?->full_name ?: $record->user?->name,
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Thông Tin Đơn Hàng') // Tiêu đề section
                        ->schema([
                            Placeholder::make('order_header_overview')
                                ->hiddenLabel()
                                ->visible(fn (?Order $record): bool => $record !== null)
                                ->content(function (?Order $record): HtmlString {
                                    if (!$record) {
                                        return new HtmlString('');
                                    }

                                    $orderCode = e($record->order_code ?: ('#' . $record->id));
                                    $phone = e($record->address?->phone ?: 'Chưa cập nhật SĐT');
                                    $receiver = e($record->address?->full_name ?: ($record->user?->name ?: 'Khách hàng'));
                                    $createdAt = $record->created_at ? e($record->created_at->format('H:i:s d/m/Y')) : '—';

                                    $addrParts = array_filter([
                                        $record->address?->detailed_address,
                                        $record->address?->ward,
                                        $record->address?->district,
                                        $record->address?->province,
                                    ], fn ($p) => filled($p));
                                    $addressText = e(!empty($addrParts) ? implode(', ', $addrParts) : ($record->shipping_method === 'in_store_pickup' ? 'Nhận tại cửa hàng' : 'Chưa cập nhật địa chỉ'));

                                    return new HtmlString("
                                        <div style='display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:14px 18px;border-radius:12px;border:1px solid rgba(37,99,235,0.25);background:rgba(37,99,235,0.06);'>
                                            <div style='display:flex;align-items:center;gap:14px;flex-wrap:wrap;'>
                                                <div style='padding:6px 14px;border-radius:8px;background:#2563eb;color:#ffffff;font-family:monospace;font-size:15px;font-weight:800;letter-spacing:0.03em;'>
                                                    MÃ ĐƠN: {$orderCode}
                                                </div>
                                                <div style='padding:6px 12px;border-radius:8px;background:rgba(16,185,129,0.15);color:#059669;font-size:14px;font-weight:700;'>
                                                    📞 SĐT: {$phone}
                                                </div>
                                                <div style='font-size:14px;font-weight:700;'>
                                                    👤 Người nhận: {$receiver}
                                                </div>
                                            </div>
                                            <div style='font-size:13px;color:#64748b;'>
                                                🕒 Đặt lúc: <strong>{$createdAt}</strong> • 📍 {$addressText}
                                            </div>
                                        </div>
                                    ");
                                })
                                ->columnSpanFull(),

                            TextInput::make('order_code')
                                ->label('Mã Đơn Hàng')
                                ->default(fn () => 'WEB' . now()->format('ymdHis') . random_int(10, 99))
                                ->required()
                                ->dehydrated()
                                ->extraInputAttributes(['style' => 'font-family: monospace; font-weight: 800; color: #2563eb; font-size: 14px;']),

                            Forms\Components\DatePicker::make('purchase_date')->label('Ngày mua đã xác nhận (bảo hành)')->maxDate(today())->disabled(fn (?Order $record)=>$record?->warrantyActivations()->exists())->helperText('Đơn cũ cần nhân viên xác nhận. Không thay ngày sau khi đã ghi nhận kích hoạt.'),
                            Select::make('user_id')
                                ->label('Khách Hàng') // Đổi nhãn sang tiếng Việt
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required(fn (?Order $record)=>$record?->order_source !== 'offline'),

                            Select::make('payment_method')
                                ->label('Phương Thức Thanh Toán') // Đổi nhãn sang tiếng Việt
                                ->options([
                                    'cod' => '(COD)',
                                    'bank' => 'Ngân hàng',
                                    'appotabank' => 'AppotaBank',
                                ])
                                ->required(),

                            Select::make('payment_status')
                                ->label('Trạng Thái Thanh Toán') // Đổi nhãn sang tiếng Việt
                                ->options([
                                    'pending' => 'Đang Chờ',
                                    'paid'    => 'Đã Thanh Toán',
                                    'failed'  => 'Thất Bại'
                                ])
                                ->default('pending')
                                ->required(),

                            // Thêm Shipping Method và Shipping Amount ở đây
                            Select::make('shipping_method')
                                ->label('Phương Thức Vận Chuyển') // Đổi nhãn sang tiếng Việt
                                ->options([
                                    'in_store_pickup' => 'Nhận tại cửa hàng',
                                    'home_delivery' => 'Giao hàng tận nơi',
                                ])
                                ->required()
                                ->reactive(),

                            TextInput::make('shipping_amount')
                                ->label('Phí Vận Chuyển') // Đổi nhãn sang tiếng Việt
                                ->numeric() // Chuyển thành input dạng số
                                ->required()
                                ->default(0),

                            TextInput::make('grand_total')
                                ->label('Tổng Cộng') // Đổi nhãn sang tiếng Việt
                                ->numeric() // Chuyển thành input dạng số
                                ->required()
                                ->default(0),

                            TextInput::make('deposit_amount')
                                ->label('Tiền Cọc') // Đổi nhãn sang tiếng Việt
                                ->numeric() // Chuyển thành input dạng số
                                ->required()
                                ->default(0),

                            ToggleButtons::make('deposit_status')
                                ->inline()
                                ->default('pending')
                                ->required()
                                ->label('Trạng Thái Tiền Cọc') // Đổi nhãn sang tiếng Việt
                                ->options([
                                    'pending' => 'Đang Chờ',
                                    'paid'    => 'Đã Thanh Toán',
                                    'failed'  => 'Thất Bại'
                                ]),

                            ToggleButtons::make('status')
                                ->inline()
                                ->default('new')
                                ->required()
                                ->label('Trạng Thái Đơn Hàng') // Đổi nhãn sang tiếng Việt
                                ->options([
                                    'new' => 'Chờ xác nhận',
                                    'processing' => 'Xác nhận & Đóng gói',
                                    'shipped'    => 'Đang vận chuyển',
                                    'delivered' => 'Giao thành công',
                                    'cancelled'  => 'Đã hủy'
                                ])
                                ->colors([
                                    'new' => 'info',
                                    'processing' => 'warning',
                                    'shipped'    => 'success',
                                    'delivered' => 'success',
                                    'cancelled'  => 'danger'
                                ])->icons([
                                    'new' => 'heroicon-m-sparkles',
                                    'processing' => 'heroicon-m-arrow-path',
                                    'shipped'    => 'heroicon-m-truck',
                                    'delivered' => 'heroicon-m-check-badge',
                                    'cancelled'  => 'heroicon-m-x-circle'
                                ]),



                            Textarea::make('notes')
                                ->label('Ghi Chú') // Đổi nhãn sang tiếng Việt
                                ->columnSpanFull()
                        ])->columns(2),

                    Section::make('Sản Phẩm Đơn Hàng') // Tiêu đề section
                        ->schema([
                            Repeater::make('items')
                                ->label('Danh sách sản phẩm & biến thể trong đơn')
                                ->relationship()
                                ->itemLabel(function (array $state): ?string {
                                    $variantId = $state['product_variant_id'] ?? null;
                                    if (!$variantId) {
                                        return 'Sản phẩm mới';
                                    }
                                    $variant = ProductVariant::with('product')->find($variantId);
                                    if (!$variant) {
                                        return 'Sản phẩm #' . $variantId;
                                    }
                                    $sku = $variant->sku ?: 'CHƯA CÓ SKU';
                                    $prodName = $variant->product?->name ?? 'Sản phẩm';
                                    $varName = self::formatVariantClassification($variant);
                                    $qty = (int) ($state['quantity'] ?? 1);
                                    return "[SKU: {$sku}] {$prodName} — {$varName} (SL: x{$qty})";
                                })
                                ->schema([
                                    Placeholder::make('variant_visual_preview')
                                        ->label('Hình Ảnh & Mã Sản Phẩm / Biến Thể')
                                        ->content(function (Get $get): HtmlString {
                                            $variantId = $get('product_variant_id');
                                            if (!$variantId) {
                                                return new HtmlString("<div style='padding:10px 12px;border-radius:10px;border:1px dashed rgba(148,163,184,0.4);color:#64748b;font-size:13px;'>Chưa chọn biến thể sản phẩm</div>");
                                            }
                                            $variant = ProductVariant::with('product')->find($variantId);
                                            if (!$variant) {
                                                return new HtmlString("<div style='padding:10px 12px;border-radius:10px;border:1px dashed rgba(239,68,68,0.4);color:#ef4444;font-size:13px;'>Không tìm thấy biến thể #{$variantId}</div>");
                                            }

                                            $imgUrl = self::resolveVariantImageUrl($variant);
                                            $imgHtml = $imgUrl
                                                ? "<a href='" . e($imgUrl) . "' target='_blank' style='flex-shrink:0;display:block;'><img src='" . e($imgUrl) . "' style='width:58px;height:58px;object-fit:cover;border-radius:10px;border:1px solid rgba(148,163,184,0.35);background:#fff;' alt=''></a>"
                                                : "<div style='width:58px;height:58px;border-radius:10px;background:rgba(59,130,246,0.12);color:#3b82f6;display:grid;place-items:center;font-weight:800;font-size:11px;flex-shrink:0;'>NO IMG</div>";

                                            $prodName = e($variant->product?->name ?? 'Sản phẩm không xác định');
                                            $sku = e($variant->sku ?: 'N/A');
                                            $classLabel = e(self::formatVariantClassification($variant));

                                            return new HtmlString("
                                                <div style='display:flex;align-items:center;gap:12px;padding:8px 12px;border-radius:12px;border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.03);'>
                                                    {$imgHtml}
                                                    <div style='min-width:0;flex:1;line-height:1.45;'>
                                                        <div style='font-weight:700;font-size:13.5px;margin-bottom:4px;'>{$prodName}</div>
                                                        <div style='display:flex;align-items:center;gap:6px;flex-wrap:wrap;'>
                                                            <span style='padding:2px 8px;border-radius:6px;background:rgba(37,99,235,0.14);color:#2563eb;font-family:monospace;font-weight:800;font-size:12px;'>Mã SP / SKU: {$sku}</span>
                                                            <span style='padding:2px 8px;border-radius:6px;background:rgba(16,185,129,0.14);color:#059669;font-weight:700;font-size:12px;'>Loại: {$classLabel}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            ");
                                        })
                                        ->columnSpan(6),

                                    Select::make('product_variant_id')
                                        ->label('Chọn Sản Phẩm / Loại Biến Thể (SKU)')
                                        ->relationship(
                                            name: 'product_variant',
                                            titleAttribute: 'sku',
                                            modifyQueryUsing: fn (Builder $query) => $query->with('product')
                                        )
                                        ->getOptionLabelFromRecordUsing(function (ProductVariant $record): string {
                                            $prodName = $record->product?->name ?? 'Sản phẩm';
                                            $classLabel = self::formatVariantClassification($record);
                                            return "[SKU: {$record->sku}] {$prodName} — {$classLabel}";
                                        })
                                        ->searchable(['sku', 'name'])
                                        ->preload()
                                        ->required()
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            $variant = ProductVariant::find($state);
                                            $unitPrice = $variant ? ($variant->price ?: ($variant->discount_price ?? 0)) : 0;
                                            $qty = (int) ($get('quantity') ?: 1);
                                            $set('unit_amount', $unitPrice);
                                            $set('total_amount', $unitPrice * $qty);
                                        })
                                        ->columnSpan(6),

                                    TextInput::make('quantity')
                                        ->label('Số Lượng')
                                        ->numeric()
                                        ->required()
                                        ->default(1)
                                        ->minValue(1)
                                        ->columnSpan(4)
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            $unitAmount = $get('unit_amount');
                                            $set('total_amount', $state * $unitAmount);
                                        }),
                                    TextInput::make('unit_amount')
                                        ->label('Đơn Giá')
                                        ->numeric()
                                        ->required()
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan(4),

                                    TextInput::make('total_amount')
                                        ->label('Tổng Giá')
                                        ->numeric()
                                        ->required()
                                        ->dehydrated()
                                        ->columnSpan(4)
                                ])->columns(12),

                            Placeholder::make('grand_total_placeholder')
                                ->label('Tổng Cộng') // Đổi nhãn sang tiếng Việt
                                ->content(function (Get $get, Set $set) {
                                    $total = 0;
                                    if (!$repeaters = $get('items')) {
                                        return $total;
                                    }

                                    foreach ($repeaters as $key => $repeater) {
                                        $total += $get("items.{$key}.total_amount") ?? 0; // Cộng dồn tổng giá
                                    }
                                    $set('grand_total', $total + ($get('shipping_amount') ?? 0));
                                    return Number::currency($total + ($get('shipping_amount') ?? 0), "VND");
                                }),

                            Hidden::make('grand_total')
                                ->default(0)
                        ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['items.product_variant.product', 'address', 'user']))
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Tìm bằng Mã đơn hàng (WEB...) hoặc Số điện thoại...')
            ->content(view('filament.tables.orders-card-list'))
            ->columns([
                TextColumn::make('order_code')
                    ->label('Mã Đơn Hàng')
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $term = trim($search);
                        return $query->where(function (Builder $q) use ($term) {
                            $q->where('order_code', 'like', "%{$term}%")
                                ->orWhereHas('address', function (Builder $aq) use ($term) {
                                    $aq->where('phone', 'like', "%{$term}%")
                                        ->orWhere('full_name', 'like', "%{$term}%");
                                })
                                ->orWhereHas('user', fn (Builder $uq) => $uq->where('name', 'like', "%{$term}%"))
                                ->orWhereHas('items.product_variant', function (Builder $vq) use ($term) {
                                    $vq->where('sku', 'like', "%{$term}%")
                                        ->orWhere('name', 'like', "%{$term}%");
                                });
                        });
                    }),
                TextColumn::make('address.phone')
                    ->label('Số Điện Thoại')
                    ->searchable(),
                TextColumn::make('address.full_name')
                    ->label('Tên Người Nhận')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Khách Hàng')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('items_detail')
                    ->label('Sản Phẩm / Loại Biến Thể / Mã SKU')
                    ->state(function (Order $record): HtmlString {
                        $items = $record->items;
                        if (!$items || $items->isEmpty()) {
                            return new HtmlString("<span style='color:#94a3b8;font-size:12px;'>Chưa có sản phẩm</span>");
                        }

                        $rows = [];
                        foreach ($items as $item) {
                            $variant = $item->product_variant;
                            $prodName = e($variant?->product?->name ?? 'Sản phẩm đã xóa');
                            $sku = e($variant?->sku ?: 'N/A');
                            $classLabel = e(self::formatVariantClassification($variant));
                            $qty = (int) ($item->quantity ?: 1);

                            $imgUrl = self::resolveVariantImageUrl($variant);
                            $imgHtml = $imgUrl
                                ? "<img src='" . e($imgUrl) . "' style='width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid rgba(148,163,184,0.35);background:#fff;flex-shrink:0;' alt=''>"
                                : "<span style='width:44px;height:44px;border-radius:8px;background:rgba(59,130,246,0.12);color:#3b82f6;display:inline-grid;place-items:center;font-weight:800;font-size:10px;flex-shrink:0;'>IMG</span>";

                            $rows[] = "
                                <div style='display:flex;align-items:center;gap:10px;padding:4px 0;min-width:290px;'>
                                    {$imgHtml}
                                    <div style='min-width:0;line-height:1.35;'>
                                        <div style='font-weight:700;font-size:13px;white-space:normal;'>{$prodName}</div>
                                        <div style='display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin-top:3px;'>
                                            <span style='padding:1px 6px;border-radius:5px;background:rgba(37,99,235,0.14);color:#2563eb;font-family:monospace;font-weight:800;font-size:11.5px;'>SKU: {$sku}</span>
                                            <span style='padding:1px 6px;border-radius:5px;background:rgba(16,185,129,0.14);color:#059669;font-weight:700;font-size:11.5px;'>Loại: {$classLabel}</span>
                                            <span style='padding:1px 6px;border-radius:5px;background:rgba(244,63,94,0.14);color:#e11d48;font-weight:800;font-size:11.5px;'>x{$qty}</span>
                                        </div>
                                    </div>
                                </div>
                            ";
                        }

                        return new HtmlString("<div style='display:flex;flex-direction:column;gap:4px;'>" . implode('', $rows) . "</div>");
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('items.product_variant', function (Builder $q) use ($search) {
                            $q->where('sku', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhereHas('product', fn (Builder $pq) => $pq->where('name', 'like', "%{$search}%"));
                        });
                    }),

                TextColumn::make('grand_total')
                    ->label('Tổng Cộng') // Đổi nhãn sang tiếng Việt
                    ->numeric()
                    ->sortable()
                    ->money('VND'),

                TextColumn::make('profit_loss')
                    ->label('Lợi nhuận') // Đổi nhãn sang tiếng Việt
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        // Định dạng số thành tiền tệ Việt Nam
                        $formattedAmount = number_format($state, 0, ',', ','); // Định dạng theo kiểu 000.000
                        return "<span style='color: green;'>₫$formattedAmount</span>"; // Đổi màu chữ thành xanh
                    })
                    ->html(), // Cho phép HTML trong cột

                TextColumn::make('payment_method')
                    ->label('Phương Thức Thanh Toán') // Đổi nhãn sang tiếng Việt
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'cod' => 'Giao hàng và thu tiền tại nhà (COD)',
                        'bank' => 'Ngân hàng',

                        default => 'Chưa xác định', // Xử lý trường hợp không hợp lệ
                    }),

                TextColumn::make('payment_status')
                    ->label('Trạng Thái Thanh Toán') // Đổi nhãn sang tiếng Việt
                    ->searchable()
                    ->sortable(),

                SelectColumn::make('status')
                    ->label('Trạng Thái Đơn Hàng') // Đổi nhãn sang tiếng Việt
                    ->options([
                        'new' => 'Chờ xác nhận', // Đổi giá trị sang tiếng Việt
                        'processing' => 'Xác nhận & Đóng gói', // Đổi giá trị sang tiếng Việt
                        'shipped'    => 'Đang vận chuyển', // Đổi giá trị sang tiếng Việt
                        'delivered' => 'Giao thành công', // Đổi giá trị sang tiếng Việt
                        'cancelled'  => 'Đã hủy' // Đổi giá trị sang tiếng Việt
                    ])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('shipping_method')
                    ->label('Phương Thức Vận Chuyển')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'in_store_pickup' => 'Nhận tại cửa hàng',
                        'home_delivery' => 'Giao hàng tận nơi',
                        default => 'Chưa xác định',
                    }),

                TextColumn::make('shipping_amount')
                    ->label('Phí Vận Chuyển') // Đổi nhãn sang tiếng Việt
                    ->sortable()
                    ->money('VND'),

                TextColumn::make('created_at')
                    ->label('Ngày Đặt Hàng') // Đổi nhãn sang tiếng Việt
                    ->dateTime()
                    ->sortable()
                    ,

                TextColumn::make('updated_at')
                    ->label('Ngày Cập Nhật') // Đổi nhãn sang tiếng Việt
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                // Có thể thêm bộ lọc tại đây nếu cần
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Xem'), // Đổi nhãn sang tiếng Việt
                    Tables\Actions\EditAction::make()
                        ->label('Chỉnh Sửa'), // Đổi nhãn sang tiếng Việt
                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa'), // Đổi nhãn sang tiếng Việt
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa Nhiều'), // Đổi nhãn sang tiếng Việt
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'success' : 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function resolveVariantImageUrl(?ProductVariant $variant): ?string
    {
        if (!$variant) {
            return null;
        }

        $raw = $variant->image;
        if (is_array($raw)) {
            $raw = reset($raw) ?: null;
        }

        if (!$raw || !is_string($raw)) {
            $productImages = $variant->product?->images;
            if (is_array($productImages) && !empty($productImages)) {
                $first = reset($productImages);
                if (is_string($first) && $first !== '') {
                    $raw = $first;
                }
            }
        }

        if (!$raw || !is_string($raw)) {
            return null;
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }

        $clean = ltrim($raw, '/');
        if (str_starts_with($clean, 'storage/')) {
            return asset($clean);
        }

        return asset('storage/' . $clean);
    }

    public static function formatVariantClassification(?ProductVariant $variant): string
    {
        if (!$variant) {
            return 'Mặc định';
        }

        $name = trim((string) $variant->name);
        $pairs = [];

        if (is_array($variant->classification) && !empty($variant->classification)) {
            foreach ($variant->classification as $k => $v) {
                if ($k !== '' && $v !== '' && !is_array($v)) {
                    $pairs[] = "{$k}: {$v}";
                }
            }
        }

        if ($name !== '' && !empty($pairs)) {
            return $name . ' (' . implode(' • ', $pairs) . ')';
        }

        if (!empty($pairs)) {
            return implode(' • ', $pairs);
        }

        return $name !== '' ? $name : 'Tiêu chuẩn';
    }
}
