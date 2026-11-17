<?php

namespace App\Filament\Resources;

use App\Models\WarrantyRequest;
use App\Services\WarrantyService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarrantyRequestResource extends Resource
{
    protected static ?string $model = WarrantyRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Yêu cầu xác minh bảo hành';

    protected static ?string $navigationGroup = 'Quản lý bán hàng';

    protected static ?int $navigationSort = 8;

    public static function getModelLabel(): string
    {
        return 'Yêu cầu bảo hành';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Yêu cầu xác minh bảo hành';
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->canAccessPanel(\Filament\Facades\Filament::getPanel('admin'));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\TextColumn::make('customer_name')->label('Khách hàng')->searchable(),
            Tables\Columns\TextColumn::make('customer_phone')->label('SĐT')->searchable(),
            Tables\Columns\TextColumn::make('productVariant.product.name')->label('Sản phẩm')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('productVariant.name')->label('Phiên bản')->wrap(),
            Tables\Columns\TextColumn::make('serial')->label('Serial')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('notes')->label('Khách cung cấp')->wrap()->limit(120),
            Tables\Columns\TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn ($state) => ['pending' => 'Chờ xác minh', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối'][$state] ?? $state)->color(fn ($state) => match ($state) {
                'approved' => 'success','rejected' => 'danger',default => 'warning'
            }),
            Tables\Columns\TextColumn::make('order.order_code')->label('Mã đơn')->searchable(),
            Tables\Columns\TextColumn::make('review_notes')->label('Kết quả xác minh')->wrap()->toggleable(),
            Tables\Columns\TextColumn::make('created_at')->label('Ngày gửi')->dateTime('d/m/Y H:i'),
        ])->filters([Tables\Filters\SelectFilter::make('status')->label('Trạng thái')->options(['pending' => 'Chờ xác minh', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối'])])->actions([
            Tables\Actions\Action::make('approve')->label('Duyệt & kích hoạt')->color('success')->visible(fn ($record) => $record->status === 'pending')->requiresConfirmation()->modalDescription('Xác minh bằng chứng mua hàng trước khi duyệt. Hạn bảo hành tính từ ngày mua được xác nhận, không phải ngày duyệt.')
                ->form([Forms\Components\DatePicker::make('purchase_date')->label('Ngày mua đã xác minh')->required()->maxDate(today()), Forms\Components\Textarea::make('review_notes')->label('Căn cứ xác minh')->required()->maxLength(1000)])
                ->action(function (WarrantyRequest $record, array $data) {
                    app(WarrantyService::class)->approve($record, $data['purchase_date'], auth()->id(), $data['review_notes']);
                    Notification::make()->title('Đã duyệt và ghi nhận bảo hành')->success()->send();
                }),
            Tables\Actions\Action::make('reject')->label('Từ chối')->color('danger')->visible(fn ($record) => $record->status === 'pending')->requiresConfirmation()
                ->form([Forms\Components\Textarea::make('review_notes')->label('Lý do từ chối')->required()->maxLength(1000)])
                ->action(function (WarrantyRequest $record, array $data) {
                    DB::transaction(function () use ($record, $data) {
                        $record = WarrantyRequest::lockForUpdate()->findOrFail($record->id);
                        if ($record->status !== 'pending') {
                            throw ValidationException::withMessages(['status' => 'Yêu cầu đã được xử lý.']);
                        }$record->update(['status' => 'rejected', 'review_notes' => $data['review_notes'], 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
                    });
                    Notification::make()->title('Đã từ chối yêu cầu')->success()->send();
                }),
            Tables\Actions\Action::make('order')->label('Mở đơn / cấp link')->url(fn ($record) => $record->order_id ? OrderResource::getUrl('view', ['record' => $record->order_id]) : null)->visible(fn ($record) => (bool) $record->order_id),
        ])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => WarrantyRequestResource\Pages\ListWarrantyRequests::route('/')];
    }
}
