<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $slug = 'roles';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Vai trò & Phân quyền';
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string
    {
        return 'Vai trò & Phân quyền';
    }

    public static function getModelLabel(): string { return 'Vai trò'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin vai trò')->schema([
                Forms\Components\TextInput::make('name')->label('Tên vai trò')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                Forms\Components\TextInput::make('slug')->label('Mã vai trò (Slug)')->required()->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')->label('Mô tả vai trò & Trách nhiệm')->columnSpanFull(),
                Forms\Components\Toggle::make('is_super_admin')->label('Quản trị viên tối cao (Super Admin) - Toàn quyền')->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Phân quyền chi tiết từng Collection')->schema([
                Forms\Components\KeyValue::make('permissions')->label('Dữ liệu phân quyền')->keyLabel('Phân hệ / Collection')->valueLabel('Quyền: view, create, update, delete')->addActionLabel('Thêm phân quyền')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Tên vai trò')->searchable(),
            Tables\Columns\TextColumn::make('slug')->label('Mã vai trò')->badge(),
            Tables\Columns\IconColumn::make('is_super_admin')->label('Super Admin')->boolean(),
            Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Người dùng'),
            Tables\Columns\TextColumn::make('updated_at')->label('Cập nhật')->dateTime('d/m/Y H:i')->sortable(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
