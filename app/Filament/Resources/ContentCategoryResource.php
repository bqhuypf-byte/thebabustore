<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentCategoryResource\Pages;
use App\Models\ContentCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ContentCategoryResource extends Resource
{
    protected static ?string $model = ContentCategory::class;
    protected static ?string $slug = 'content-categories';
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Danh mục nội dung';
    protected static ?string $navigationGroup = 'Quản lý nội dung';
    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string { return 'Danh mục nội dung'; }
    public static function getModelLabel(): string { return 'Danh mục nội dung'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin danh mục')->schema([
                Forms\Components\TextInput::make('name')->label('Tên danh mục nội dung')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                Forms\Components\TextInput::make('slug')->label('Đường dẫn tĩnh (Slug)')->required()->unique(ignoreRecord: true),
                Forms\Components\FileUpload::make('image')->label('Ảnh')->image()->directory('content-categories'),
                Forms\Components\Textarea::make('description')->label('Mô tả chuyên mục nội dung'),
                Forms\Components\Select::make('content_type_id')->label('Thuộc loại nội dung')->relationship('type', 'name')->searchable()->preload(),
                Forms\Components\Select::make('parent_id')->label('Danh mục cha')->relationship('parent', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('sort_order')->label('Thứ tự ưu tiên hiển thị')->numeric()->default(0),
            ])->columns(2),
            Forms\Components\Section::make('Cấu hình SEO (Tìm kiếm & Mạng xã hội)')->schema([
                Forms\Components\TextInput::make('seo_title')->label('Tiêu đề SEO (Title)')->maxLength(60),
                Forms\Components\Textarea::make('seo_description')->label('Mô tả SEO (Meta Description)')->maxLength(160),
                Forms\Components\FileUpload::make('seo_image')->label('Ảnh đại diện chia sẻ (Meta Image / OG)')->image()->directory('seo'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image')->label('Ảnh')->square(),
            Tables\Columns\TextColumn::make('name')->label('Tên danh mục')->searchable(),
            Tables\Columns\TextColumn::make('type.name')->label('Loại nội dung'),
            Tables\Columns\TextColumn::make('parent.name')->label('Danh mục cha'),
            Tables\Columns\TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListContentCategories::route('/'), 'create' => Pages\CreateContentCategory::route('/create'), 'edit' => Pages\EditContentCategory::route('/{record}/edit')];
    }
}
