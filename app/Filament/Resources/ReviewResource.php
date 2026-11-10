<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Product;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'Quản lý chung';
    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'Đánh giá';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Đánh giá';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin đánh giá')
                    ->schema([
                        Select::make('product_id')
                            ->label('Sản phẩm')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->label('Họ tên người đánh giá')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(150),

                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(20),

                        Select::make('rating')
                            ->label('Số sao')
                            ->options([
                                1 => '1 Sao ★',
                                2 => '2 Sao ★★',
                                3 => '3 Sao ★★★',
                                4 => '4 Sao ★★★★',
                                5 => '5 Sao ★★★★★',
                            ])
                            ->required(),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'pending' => 'Chờ duyệt',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối',
                            ])
                            ->default('pending')
                            ->required(),

                        Toggle::make('is_verified_purchase')
                            ->label('Đã xác thực mua hàng')
                            ->default(false),

                        Textarea::make('comment')
                            ->label('Nội dung nhận xét')
                            ->rows(4)
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),

                TextColumn::make('product.name')
                    ->label('Sản phẩm')
                    ->searchable()
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->product?->name),

                TextColumn::make('name')
                    ->label('Khách hàng')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Số sao')
                    ->formatStateUsing(fn ($state) => str_repeat('★', (int) $state) . " ({$state})")
                    ->sortable()
                    ->color('warning'),

                TextColumn::make('comment')
                    ->label('Nội dung')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->comment),

                IconColumn::make('is_verified_purchase')
                    ->label('Đã mua')
                    ->boolean(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        default => 'Chờ duyệt',
                    })
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                    ]),
                SelectFilter::make('product_id')
                    ->label('Sản phẩm')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->action(fn ($record) => $record->update(['status' => 'approved'])),

                Tables\Actions\Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'rejected')
                    ->action(fn ($record) => $record->update(['status' => 'rejected'])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkApprove')
                        ->label('Duyệt hàng loạt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'approved'])),

                    Tables\Actions\BulkAction::make('bulkReject')
                        ->label('Từ chối hàng loạt')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'rejected'])),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}

