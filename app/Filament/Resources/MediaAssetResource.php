<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaAssetResource\Pages;
use App\Models\MediaAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;
    protected static ?string $slug = 'media';
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Kho ảnh & Media';
    protected static ?string $navigationGroup = 'Quản lý bán hàng';
    protected static ?int $navigationSort = 9;

    public static function getPluralModelLabel(): string
    {
        return 'Kho ảnh & Media';
    }

    public static function getModelLabel(): string { return 'Media'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('file_path')->label('Tệp Media')->directory('media')->required()->imageEditor()->downloadable()->columnSpanFull(),
            Forms\Components\TextInput::make('name')->label('Tên tệp')->required()->maxLength(255),
            Forms\Components\TextInput::make('alt_text')->label('Văn bản thay thế (Alt text)')->maxLength(255),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->contentGrid(['md' => 2, 'xl' => 4])->columns([
            Tables\Columns\ImageColumn::make('file_path')->label('Xem trước')->height(120)->square(),
            Tables\Columns\TextColumn::make('name')->label('Tên')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('created_at')->label('Ngày tải')->dateTime('d/m/Y H:i')->sortable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaAssets::route('/'),
            'create' => Pages\CreateMediaAsset::route('/create'),
            'edit' => Pages\EditMediaAsset::route('/{record}/edit'),
        ];
    }
}
