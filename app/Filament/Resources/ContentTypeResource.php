<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentTypeResource\Pages;
use App\Models\ContentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ContentTypeResource extends Resource
{
    protected static ?string $model = ContentType::class;
    protected static ?string $slug = 'content-types';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'Loại nội dung';
    protected static ?string $navigationGroup = 'Quản lý nội dung';
    protected static ?int $navigationSort = 3;

    public static function getPluralModelLabel(): string { return 'Loại nội dung'; }
    public static function getModelLabel(): string { return 'Loại nội dung'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Tên loại nội dung')->required()->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            Forms\Components\TextInput::make('slug')->label('Đường dẫn tĩnh (Slug)')->required()->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')->label('Mô tả loại nội dung')->columnSpanFull(),
            Forms\Components\TextInput::make('icon')->label('Biểu tượng (Heroicon)')->placeholder('heroicon-o-document-text'),
            Forms\Components\TextInput::make('sort_order')->label('Thứ tự ưu tiên')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('Đang hoạt động')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Tên loại')->searchable(),
            Tables\Columns\TextColumn::make('slug')->label('Slug'),
            Tables\Columns\TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
            Tables\Columns\IconColumn::make('is_active')->label('Hoạt động')->boolean(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListContentTypes::route('/'), 'create' => Pages\CreateContentType::route('/create'), 'edit' => Pages\EditContentType::route('/{record}/edit')];
    }
}
