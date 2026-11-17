<?php

namespace App\Filament\Support;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\WarrantyService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class WarrantyOrderActions
{
    public static function variantSelect(): Forms\Components\Select
    {
        return Forms\Components\Select::make('product_variant_id')->label('Sản phẩm / phiên bản')->required()->searchable()
            ->getSearchResultsUsing(fn (string $search) => ProductVariant::with('product')->where(fn ($q) => $q->where('sku', 'like', '%'.$search.'%')->orWhereHas('product', fn ($p) => $p->where('name', 'like', '%'.$search.'%')))->limit(40)->get()->mapWithKeys(fn ($v) => [$v->id => $v->product?->name.' · '.OrderResource::formatVariantClassification($v).' ['.$v->sku.']']))
            ->getOptionLabelUsing(fn ($value) => ($v = ProductVariant::with('product')->find($value)) ? $v->product?->name.' · '.OrderResource::formatVariantClassification($v) : null);
    }

    public static function create(): Actions\Action
    {
        return Actions\Action::make('create_offline_warranty_order')->label('Tạo đơn offline / bảo hành')->icon('heroicon-o-shield-check')->modalHeading('Đơn mua tại cửa hàng')->modalDescription('Không tạo tài khoản khách. Chỉ ghi nhận đơn và ngày mua; kích hoạt bảo hành là thao tác riêng. Thanh toán để trạng thái chưa xác nhận.')
            ->form([
                Forms\Components\TextInput::make('phone')->label('Số điện thoại')->tel()->required()->rules(WarrantyService::phoneRules()),
                Forms\Components\TextInput::make('name')->label('Tên khách (không bắt buộc)')->maxLength(120),
                Forms\Components\DatePicker::make('purchase_date')->label('Ngày mua')->default(now()->toDateString())->maxDate(today())->required(),
                Forms\Components\Repeater::make('items')->label('Sản phẩm')->schema([self::variantSelect(), Forms\Components\TextInput::make('quantity')->label('Số lượng')->integer()->minValue(1)->maxValue(999)->default(1)->required()])->minItems(1)->defaultItems(1),
            ])->action(function (array $data, $livewire) {
                $order = app(WarrantyService::class)->createOffline($data);
                Notification::make()->title('Đã tạo đơn offline '.$order->order_code)->success()->send();
                $livewire->redirect(OrderResource::getUrl('view', ['record' => $order]));
            });
    }

    public static function activate(): Actions\Action
    {
        return Actions\Action::make('activate_order_warranty')->label('Kích hoạt bảo hành')->icon('heroicon-o-shield-check')->color('success')->visible(fn (Order $record) => $record->status !== 'cancelled')
            ->mountUsing(function (Forms\Form $form, Order $record) {
                $form->fill(['purchase_date' => $record->purchase_date?->toDateString(), 'name' => $record->address?->full_name, 'phone' => $record->address?->phone, 'variants' => $record->items->pluck('product_variant_id')->unique()->values()->all()]);
            })
            ->form([
                Forms\Components\DatePicker::make('purchase_date')->label('Ngày mua đã xác nhận')->required()->maxDate(today())->disabled(fn (Order $record) => $record->warrantyActivations()->exists())->dehydrated(),
                Forms\Components\TextInput::make('name')->label('Tên khách')->required()->minLength(2)->maxLength(120),
                Forms\Components\TextInput::make('phone')->label('Số điện thoại')->required()->rules(WarrantyService::phoneRules()),
                Forms\Components\CheckboxList::make('variants')->label('Mặt hàng kích hoạt')->options(fn (Order $record) => $record->items()->with('product_variant.product')->get()->mapWithKeys(fn ($i) => [$i->product_variant_id => $i->product_variant?->product?->name.' · '.$i->product_variant?->name.' · BH '.((int) $i->product_variant?->warranty_days).' ngày']))->required()->rules(['array', 'min:1']),
                Forms\Components\TextInput::make('serial')->label('Serial / ghi chú định danh (không bắt buộc)')->maxLength(255),
            ])->action(function (Order $record, array $data) {
                DB::transaction(function () use ($record, $data) {
                    $record = Order::lockForUpdate()->findOrFail($record->id);
                    if (! $record->warrantyActivations()->exists()) {
                        $record->update(['purchase_date' => $data['purchase_date']]);
                    }
                    app(WarrantyService::class)->activate($record, $data['variants'], $data['name'], $data['phone'], $data['serial'] ?? null, 'staff', auth()->id());
                });
                Notification::make()->title('Đã lưu bảo hành. Mặt hàng đã kích hoạt được giữ nguyên, không gia hạn.')->success()->send();
            });
    }

    public static function link(): Actions\Action
    {
        return Actions\Action::make('warranty_activation_link')->label('Tạo / cấp lại link')->icon('heroicon-o-link')->visible(fn (Order $record) => $record->purchase_date && $record->status !== 'cancelled')
            ->modalHeading('Link tự kích hoạt — có hiệu lực 30 ngày')->modalDescription('Mỗi lần mở chức năng này sẽ cấp link mới và vô hiệu link cũ. Nhân viên sao chép để gửi khách, hệ thống không tự gửi SMS/Zalo.')
            ->mountUsing(function (Forms\Form $form, Order $record) {
                $link = app(WarrantyService::class)->generateLink($record);
                $form->fill(['share' => ['link' => $link, 'message' => 'The BABU Store gửi bạn link kích hoạt bảo hành đơn #'.$record->order_code.': '.$link.' . Link có hiệu lực 30 ngày. Thời hạn bảo hành tính từ ngày mua.']]);
            })->form([Forms\Components\ViewField::make('share')->view('filament.forms.warranty-share')])->modalSubmitAction(false)->modalCancelActionLabel('Đóng');
    }
}
