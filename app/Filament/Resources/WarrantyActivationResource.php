<?php

namespace App\Filament\Resources;

use App\Models\WarrantyActivation;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WarrantyActivationResource extends Resource
{
    protected static ?string $model = WarrantyActivation::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Bảo hành đã kích hoạt';

    protected static ?string $navigationGroup = 'Quản lý bán hàng';

    protected static ?int $navigationSort = 9;

    public static function getModelLabel(): string
    {
        return 'Bảo hành';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bảo hành đã kích hoạt';
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->canAccessPanel(\Filament\Facades\Filament::getPanel('admin'));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('activated_at', 'desc')->columns([
            Tables\Columns\TextColumn::make('order.order_code')->label('Mã đơn')->searchable(),
            Tables\Columns\TextColumn::make('customer_name')->label('Khách hàng')->searchable(),
            Tables\Columns\TextColumn::make('customer_phone')->label('SĐT')->searchable(),
            Tables\Columns\TextColumn::make('productVariant.product.name')->label('Sản phẩm')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('productVariant.name')->label('Phiên bản')->wrap(),
            Tables\Columns\TextColumn::make('quantity')->label('SL'),
            Tables\Columns\TextColumn::make('serial')->label('Serial')->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('purchase_date')->label('Ngày mua')->date('d/m/Y'),
            Tables\Columns\TextColumn::make('warranty_days')->label('Số ngày BH'),
            Tables\Columns\TextColumn::make('expires_at')->label('Hạn đến')->date('d/m/Y'),
            Tables\Columns\TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn ($record) => $record->expires_at->copy()->endOfDay()->isPast() ? 'Hết hạn' : 'Đang bảo hành')->color(fn ($record) => $record->expires_at->copy()->endOfDay()->isPast() ? 'gray' : 'success'),
            Tables\Columns\TextColumn::make('activated_by')->label('Người kích hoạt')->formatStateUsing(fn ($state) => $state === 'staff' ? 'Nhân viên' : 'Khách hàng'),
        ])->filters([
            Tables\Filters\SelectFilter::make('expiry')->label('Hiệu lực')->options(['active' => 'Đang bảo hành', 'expired' => 'Hết hạn'])->query(function ($query, array $data) {
                return match ($data['value'] ?? null) {
                    'active' => $query->whereDate('expires_at', '>=', today()),'expired' => $query->whereDate('expires_at', '<', today()),default => $query
                };
            }),
            Tables\Filters\SelectFilter::make('activated_by')->label('Nguồn kích hoạt')->options(['staff' => 'Nhân viên', 'customer' => 'Khách hàng']),
        ])->actions([Tables\Actions\Action::make('order')->label('Mở đơn / cấp lại link')->url(fn ($record) => OrderResource::getUrl('view', ['record' => $record->order_id]))])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => WarrantyActivationResource\Pages\ListWarrantyActivations::route('/')];
    }
}
