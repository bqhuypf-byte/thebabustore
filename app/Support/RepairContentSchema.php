<?php
namespace App\Support;
use Filament\Forms\Components as F;
class RepairContentSchema
{
    public static function tab(): F\Tabs\Tab
    {
        return F\Tabs\Tab::make('Babu Care')->icon('heroicon-o-wrench-screwdriver')->schema([
            F\Placeholder::make('repair_help')->label('Dịch vụ sửa chữa')->content('Nội dung mặc định được giữ khi chưa lưu. Giá linh kiện là tham khảo thị trường, không bao gồm công thay. Dịch vụ luôn báo giá sau kiểm tra; không nhập giá sửa mạch vào bảng linh kiện.'),
            F\Repeater::make('repair_services')->label('Nhóm dịch vụ')->afterStateHydrated(function ($component, $state) { if ($state === null) $component->state(RepairServicesContent::defaults()); })->schema([
                F\Select::make('id')->label('Nhóm')->options(['keyboard'=>'Bàn phím','cleaning'=>'Bảo dưỡng bàn phím','mouse'=>'Chuột','headphones'=>'Tai nghe'])->required()->distinct(),
                F\TextInput::make('title')->label('Tiêu đề nhóm')->required()->maxLength(160),
                F\Textarea::make('description')->label('Giới thiệu')->maxLength(600),
                F\Repeater::make('items')->label('Hạng mục')->schema([
                    F\TextInput::make('name')->label('Tên dịch vụ')->required()->maxLength(160),
                    F\Select::make('kind')->label('Nhu cầu')->options(['repair'=>'Sửa lỗi','replacement'=>'Thay linh kiện','cleaning'=>'Vệ sinh / bảo dưỡng'])->required()->default('repair'),
                    F\Textarea::make('symptoms')->label('Triệu chứng')->maxLength(600),
                    F\Textarea::make('scope')->label('Phạm vi kiểm tra / xử lý')->maxLength(800),
                    F\TextInput::make('reference_price_vnd')->label('Giá dịch vụ tham khảo (VNĐ)')->numeric()->minValue(1)->nullable()->helperText('Không hiển thị số tiền với loại Sửa lỗi. Phải có URL nguồn.'),
                    F\TextInput::make('price_unit')->label('Đơn vị / điều kiện giá')->maxLength(120),
                    F\TextInput::make('image')->label('Đường dẫn ảnh nội bộ')->maxLength(500)->helperText('Ví dụ: images/repair/reference/service-1.png'),
                    F\Repeater::make('tiers')->label('Các mức giá')->schema([
                        F\TextInput::make('label')->label('Hạng mục')->required()->maxLength(160),
                        F\TextInput::make('price')->label('Giá tham khảo')->numeric()->minValue(1)->required(),
                        F\TextInput::make('condition')->label('Điều kiện')->maxLength(200),
                    ])->defaultItems(0)->collapsible(),
                    F\TextInput::make('source_url')->label('URL nguồn giá')->url()->maxLength(2048),
                ])->collapsible()->itemLabel(fn (array $state): ?string => $state['name'] ?? null)->defaultItems(0),
            ])->collapsible()->itemLabel(fn (array $state): ?string => $state['title'] ?? null)->defaultItems(0),
            F\Repeater::make('repair_parts')->label('Linh kiện tham khảo')->afterStateHydrated(function ($component, $state) { if ($state === null) $component->state(RepairServicesContent::partDefaults()); })->schema([
                F\TextInput::make('name')->label('Tên linh kiện')->required()->maxLength(160),
                F\TextInput::make('variant')->label('Mã / biến thể')->maxLength(160),
                F\Select::make('device')->label('Thiết bị')->options(['mouse'=>'Chuột','keyboard'=>'Bàn phím','headphones'=>'Tai nghe'])->required(),
                F\TextInput::make('price')->label('Giá tham khảo (VNĐ)')->numeric()->minValue(1)->nullable(),
                F\TextInput::make('unit')->label('Đơn vị')->required()->maxLength(40),
                F\Toggle::make('verified')->label('Đã kiểm chứng đúng mã, giá và đơn vị')->default(false),
                F\TextInput::make('source_name')->label('Nhà bán / nguồn')->maxLength(100),
                F\TextInput::make('source_url')->label('URL nguồn trực tiếp')->url()->maxLength(2048)->required(fn (\Filament\Forms\Get $get) => (bool) $get('verified')),
                F\DatePicker::make('checked_at')->label('Ngày kiểm tra')->maxDate(now())->required(fn (\Filament\Forms\Get $get) => (bool) $get('verified')),
                F\Textarea::make('note')->label('Ghi chú tương thích / giới hạn')->maxLength(600),
            ])->columns(2)->collapsible()->itemLabel(fn (array $state): ?string => $state['name'] ?? null)->defaultItems(0),
        ]);
    }
}
