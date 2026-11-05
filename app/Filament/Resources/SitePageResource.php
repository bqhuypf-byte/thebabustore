<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SitePageResource\Pages;
use App\Models\SitePage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SitePageResource extends Resource
{
    protected static ?string $model = SitePage::class;
    protected static ?string $slug = 'pages';
    protected static ?string $navigationIcon = 'heroicon-o-window';
    protected static ?string $navigationLabel = 'Giao diện & SEO các trang';
    protected static ?string $navigationGroup = 'Giao diện & Bố cục';
    protected static ?int $navigationSort = 1;

    public static function getPluralModelLabel(): string { return 'Giao diện & SEO các trang'; }
    public static function getModelLabel(): string { return 'Giao diện trang'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Cấu hình trang')->schema([
                Forms\Components\TextInput::make('name')->label('Tên trang / Khu vực giao diện')->required(),
                Forms\Components\Select::make('type')->label('Loại trang / Bố cục')->options([
                    'home' => 'Trang chủ', 'listing' => 'Trang danh sách', 'detail' => 'Trang chi tiết',
                    'cart' => 'Trang giỏ hàng', 'checkout' => 'Trang thanh toán', 'static' => 'Trang tĩnh', 'layout' => 'Header / Footer / Layout',
                ])->required(),
                Forms\Components\TextInput::make('path')->label('Đường dẫn URL trang')->placeholder('/')->unique(ignoreRecord: true),
                Forms\Components\Toggle::make('is_active')->label('Đang hoạt động')->default(true),
                Forms\Components\KeyValue::make('content')->label('Cấu hình giao diện & Nội dung')->keyLabel('Khối / Thuộc tính')->valueLabel('Giá trị')->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Tối ưu SEO & Chia sẻ Mạng Xã Hội')->schema([
                Forms\Components\TextInput::make('seo_title')->label('Tiêu đề SEO')->maxLength(60),
                Forms\Components\Textarea::make('seo_description')->label('Mô tả SEO')->maxLength(160),
                Forms\Components\FileUpload::make('seo_image')->label('Ảnh chia sẻ (OG Image)')->image()->directory('seo'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Tên trang')->searchable(),
            Tables\Columns\TextColumn::make('type')->label('Loại')->badge(),
            Tables\Columns\TextColumn::make('path')->label('Đường dẫn')->copyable(),
            Tables\Columns\IconColumn::make('is_active')->label('Hoạt động')->boolean(),
            Tables\Columns\TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListSitePages::route('/'), 'create' => Pages\CreateSitePage::route('/create'), 'edit' => Pages\EditSitePage::route('/{record}/edit')];
    }
}
