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
            Forms\Components\KeyValue::make('classification')->label('Nhóm phân loại')->keyLabel('Tên nhóm')->valueLabel('Giá trị')->helperText('Ví dụ: Dòng máy → Pro X, Màu sắc → Black, Mic → Có mic. Dùng cùng tên nhóm cho các biến thể của sản phẩm.')->columnSpanFull(),
            \App\Filament\Forms\ProductContentFields::variantSpecifications(),
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
        return $table
            ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->with('product'))
            ->columns([
                Tables\Columns\ImageColumn::make('display_image')
                    ->label('Hình Ảnh')
                    ->state(function (ProductVariant $record): ?string {
                        if (!empty($record->image) && is_string($record->image)) {
                            return $record->image;
                        }
                        $prodImages = $record->product?->images;
                        if (is_array($prodImages) && !empty($prodImages)) {
                            return reset($prodImages) ?: null;
                        }
                        return null;
                    })
                    ->disk('public')
                    ->square()
                    ->size(52),
                Tables\Columns\TextColumn::make('sku')
                    ->label('Mã Sản Phẩm (SKU)')
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Sản Phẩm')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Loại & Biến Thể')
                    ->description(fn (ProductVariant $record): string => OrderResource::formatVariantClassification($record))
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá Bán')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Tồn Kho')
                    ->badge()
                    ->color(fn (string $state) => $state === 'in_stock' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày Cập Nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('sku_activation_link')
                    ->label('Link Kích Hoạt')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->url(fn (ProductVariant $record): string => url('/kich-hoat?sku=' . urlencode((string) $record->sku)))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListProductVariants::route('/'), 'create' => Pages\CreateProductVariant::route('/create'), 'edit' => Pages\EditProductVariant::route('/{record}/edit')];
    }
}
