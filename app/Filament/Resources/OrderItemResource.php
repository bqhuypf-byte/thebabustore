<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Chi tiết đơn hàng';
    protected static ?string $navigationGroup = 'Quản lý bán hàng';
    protected static ?int $navigationSort = 7;
    public static function getPluralModelLabel(): string
    {
        return 'Chi tiết đơn hàng';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông Tin Sản Phẩm & Thời Hạn Bảo Hành')
                    ->schema([
                        Forms\Components\Select::make('product_variant_id')
                            ->label('Sản Phẩm / Biến Thể (SKU)')
                            ->relationship('product_variant', 'sku')
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\ProductVariant $record) => "[SKU: {$record->sku}] " . ($record->product?->name ?? 'Sản phẩm') . ' — ' . OrderResource::formatVariantClassification($record))
                            ->searchable(['sku', 'name'])
                            ->preload()
                            ->required(),

                        Forms\Components\DateTimePicker::make('warranty')
                            ->label('Hạn Bảo Hành Đến Ngày')
                            ->required(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Số Lượng')
                            ->numeric()
                            ->default(1)
                            ->required(),

                        Forms\Components\TextInput::make('unit_amount')
                            ->label('Đơn Giá (VNĐ)')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('Tổng Tiền (VNĐ)')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('profit_loss')
                            ->label('Lợi Nhuận (VNĐ)')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['product_variant.product', 'order.address']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('variant_display_image')
                    ->label('Hình Ảnh')
                    ->state(function (OrderItem $record): ?string {
                        $variant = $record->product_variant;
                        if (!$variant) {
                            return null;
                        }
                        if (!empty($variant->image) && is_string($variant->image)) {
                            return $variant->image;
                        }
                        $prodImages = $variant->product?->images;
                        if (is_array($prodImages) && !empty($prodImages)) {
                            return reset($prodImages) ?: null;
                        }
                        return null;
                    })
                    ->disk('public')
                    ->square()
                    ->size(52),
                Tables\Columns\TextColumn::make('product_variant.sku')
                    ->label('Mã Sản Phẩm (SKU)')
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_variant.product.name')
                    ->label('Sản Phẩm & Loại Biến Thể')
                    ->description(fn (OrderItem $record): string => 'Loại: ' . OrderResource::formatVariantClassification($record->product_variant))
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('order.order_code')
                    ->label('Mã Đơn Hàng / BH')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('order.address.full_name')
                    ->label('Khách Hàng & SĐT')
                    ->description(fn (OrderItem $record): string => '📞 ' . ($record->order?->address?->phone ?: 'Chưa cập nhật'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('order.address', function (Builder $aq) use ($search) {
                            $aq->where('full_name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('quantity') // Số lượng
                    ->label('Số Lượng') // Thêm label
                    ->numeric()
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_amount') // Giá trị đơn vị
                    ->label('Giá Trị Đơn Vị') // Thêm label
                    ->numeric()
                    ->searchable()
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount') // Tổng số tiền
                    ->label('Tổng Số Tiền') // Thêm label
                    ->numeric()
                    ->searchable()
                    ->money('VND')

                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at') // Thời gian tạo
                    ->label('Thời Gian Tạo') // Thêm label
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at') // Thời gian cập nhật
                    ->label('Thời Gian Cập Nhật') // Thêm label
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Start of Selection
                    // Start of Selection
                    Tables\Columns\TextColumn::make('profit_loss') // Lợi nhuận/ lỗ
                        ->label('Lợi Nhuận/Lỗ') // Thêm label
                        ->money('VND')
                        ->sortable()
                        ->searchable()
                        ->badge()
                        ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                    Tables\Columns\TextColumn::make('warranty')
                        ->label('Hạn Bảo Hành')
                        ->dateTime('d/m/Y')
                        ->badge()
                        ->sortable()
                        ->color(fn ($state) => $state < now() ? 'danger' : 'success'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\Action::make('activation_link')
                    ->label('Link Kích Hoạt')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->url(fn (OrderItem $record): string => url('/kich-hoat/' . ($record->order?->order_code ?: '')))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->label('Sửa BH'),
                Tables\Actions\Action::make('view_order')
                    ->label('Xem Đơn Hàng')
                    ->url(fn ($record) => \App\Filament\Resources\OrderResource::getUrl('edit', ['record' => $record->order_id]))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa Hàng Loạt'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make() // Hành động xóa hàng loạt
                        ->label('Xóa Hàng Loạt'), // Thêm label cho hành động
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
