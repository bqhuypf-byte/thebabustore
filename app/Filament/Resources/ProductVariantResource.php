<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;
    protected static ?string $slug = 'product-variants';
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationLabel = 'Biến thể sản phẩm';
    protected static ?string $navigationGroup = 'Quản lý bán hàng';
    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string { return 'Biến thể sản phẩm'; }
    public static function getModelLabel(): string { return 'Biến thể sản phẩm'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')->label('Sản phẩm')->relationship('product', 'name')->searchable()->preload()->required(),
            Forms\Components\TextInput::make('name')->label('Tên biến thể')->required(),
            Forms\Components\TextInput::make('sku')->label('Mã SKU')->required()->unique(ignoreRecord: true),
            Forms\Components\Select::make('stock')->label('Trạng thái tồn kho')->options(['in_stock' => 'Còn hàng', 'out_of_stock' => 'Tạm hết hàng'])->default('in_stock')->required(),
            Forms\Components\TextInput::make('price')->label('Giá bán')->numeric()->required(),
            Forms\Components\TextInput::make('discount_price')->label('Giá ưu đãi')->numeric(),
            Forms\Components\TextInput::make('warranty_days')->label('Thời hạn bảo hành (ngày)')->numeric()->default(0),
            Forms\Components\FileUpload::make('image')->label('Ảnh biến thể')->image()->directory('product_variants'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image')->label('Ảnh')->square(),
            Tables\Columns\TextColumn::make('product.name')->label('Sản phẩm')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('name')->label('Biến thể')->searchable(),
            Tables\Columns\TextColumn::make('sku')->label('SKU')->copyable()->searchable(),
            Tables\Columns\TextColumn::make('price')->label('Giá bán')->money('VND')->sortable(),
            Tables\Columns\TextColumn::make('stock')->label('Tồn kho')->badge()->color(fn (string $state) => $state === 'in_stock' ? 'success' : 'danger'),
            Tables\Columns\TextColumn::make('updated_at')->label('Ngày cập nhật')->dateTime('d/m/Y H:i')->sortable(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListProductVariants::route('/'), 'create' => Pages\CreateProductVariant::route('/create'), 'edit' => Pages\EditProductVariant::route('/{record}/edit')];
    }
}
