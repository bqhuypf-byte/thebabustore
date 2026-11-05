<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static ?string $slug = 'coupons';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Mã giảm giá';
    protected static ?string $navigationGroup = 'Quản lý bán hàng';
    protected static ?int $navigationSort = 8;

    public static function getPluralModelLabel(): string
    {
        return 'Mã giảm giá';
    }

    public static function getModelLabel(): string { return 'Mã giảm giá'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin chương trình')->schema([
                Forms\Components\TextInput::make('code')->label('Mã giảm giá (Voucher Code)')->required()->unique(ignoreRecord: true)->maxLength(50),
                Forms\Components\TextInput::make('name')->label('Tên chương trình / Tiêu đề ưu đãi')->required()->maxLength(255),
                Forms\Components\Select::make('discount_type')->label('Hình thức giảm giá')->options([
                    'percentage' => 'Giảm theo phần trăm (%)',
                    'fixed' => 'Giảm số tiền cố định (VNĐ)',
                    'free_shipping' => 'Miễn phí vận chuyển',
                ])->default('percentage')->required()->live(),
                Forms\Components\TextInput::make('value')->label('Giá trị giảm')->numeric()->minValue(0)->required(),
                Forms\Components\TextInput::make('max_discount')->label('Mức giảm tối đa (VNĐ)')->numeric()->minValue(0),
                Forms\Components\TextInput::make('minimum_order')->label('Giá trị đơn hàng tối thiểu (VNĐ)')->numeric()->default(0)->minValue(0),
            ])->columns(2),
            Forms\Components\Section::make('Giới hạn & hiệu lực')->schema([
                Forms\Components\TextInput::make('usage_limit')->label('Tổng lượt dùng tối đa')->numeric()->minValue(1),
                Forms\Components\TextInput::make('usage_limit_per_customer')->label('Lượt dùng tối đa / khách')->numeric()->default(1)->minValue(1),
                Forms\Components\TextInput::make('used_count')->label('Số lượt đã sử dụng')->numeric()->default(0)->disabled()->dehydrated(),
                Forms\Components\Toggle::make('is_active')->label('Đang kích hoạt')->default(true),
                Forms\Components\DateTimePicker::make('starts_at')->label('Ngày bắt đầu hiệu lực'),
                Forms\Components\DateTimePicker::make('ends_at')->label('Ngày kết thúc / Hết hạn'),
                Forms\Components\Select::make('scope')->label('Phạm vi áp dụng')->options([
                    'all' => 'Toàn bộ sản phẩm trên cửa hàng',
                    'products' => 'Sản phẩm được chọn',
                    'categories' => 'Danh mục được chọn',
                ])->default('all')->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->label('Mã')->searchable()->copyable(),
            Tables\Columns\TextColumn::make('name')->label('Chương trình')->searchable(),
            Tables\Columns\TextColumn::make('discount_type')->label('Hình thức')->badge(),
            Tables\Columns\TextColumn::make('value')->label('Giá trị')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('used_count')->label('Đã dùng')->sortable(),
            Tables\Columns\IconColumn::make('is_active')->label('Kích hoạt')->boolean(),
            Tables\Columns\TextColumn::make('ends_at')->label('Hết hạn')->dateTime('d/m/Y H:i')->sortable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
