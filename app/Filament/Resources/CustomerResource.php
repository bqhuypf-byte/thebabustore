<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $slug = 'customers';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Khách hàng';
    protected static ?string $navigationGroup = 'Quản lý bán hàng';
    protected static ?int $navigationSort = 10;

    public static function getPluralModelLabel(): string { return 'Khách hàng'; }
    public static function getModelLabel(): string { return 'Khách hàng'; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_admin', false);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Họ và tên')->required(),
            Forms\Components\TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('phone')->label('Số điện thoại')->tel(),
            Forms\Components\TextInput::make('province')->label('Tỉnh / Thành phố'),
            Forms\Components\TextInput::make('district')->label('Quận / Huyện'),
            Forms\Components\TextInput::make('ward')->label('Phường / Xã'),
            Forms\Components\Textarea::make('detailed_address')->label('Địa chỉ chi tiết')->columnSpanFull(),
            Forms\Components\Hidden::make('is_admin')->default(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Khách hàng')->searchable(),
            Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
            Tables\Columns\TextColumn::make('phone')->label('Số điện thoại')->searchable(),
            Tables\Columns\TextColumn::make('orders_count')->counts('orders')->label('Đơn hàng')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->label('Ngày đăng ký')->dateTime('d/m/Y H:i')->sortable(),
        ])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListCustomers::route('/'), 'create' => Pages\CreateCustomer::route('/create'), 'edit' => Pages\EditCustomer::route('/{record}/edit')];
    }
}
