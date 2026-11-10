<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ProductContentFields
{
    public static function sections(): Repeater
    {
        return Repeater::make('content_sections')
            ->label('Các mục mô tả có cấu trúc')
            ->helperText('Thêm các mục theo thứ tự: giới thiệu, điểm nổi bật, tính năng, phụ kiện. Kéo để đổi thứ tự; tắt Hiển thị để ẩn. Khi có mục hiển thị, website dùng các mục này thay phần mô tả cũ; bản gốc vẫn được giữ.')
            ->schema([
                TextInput::make('title')->label('Tiêu đề mục')->required()->maxLength(200),
                Textarea::make('body')->label('Nội dung văn bản')->rows(3)->maxLength(20000)->helperText('Dùng cho nội dung ngắn. Có thể dùng trình soạn thảo bên dưới cho ảnh, danh sách và bảng.'),
                \Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor::make('html')->label('Nội dung chi tiết và hình ảnh')->fileAttachmentsDirectory(directory: 'products/sections')->columnSpanFull()->helperText('Website luôn áp dụng font Roboto và cỡ chữ theo mẫu chung.'),
                FileUpload::make('image')->label('Ảnh minh họa')->image()->directory('products/sections'),
                TextInput::make('image_alt')->label('Mô tả ảnh')->maxLength(255),
                Toggle::make('visible')->label('Hiển thị')->default(true),
            ])
            ->defaultItems(0)
            ->reorderableWithButtons()
            ->collapsible()
            ->collapsed()
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
            ->columnSpanFull();
    }

    public static function specifications(): Repeater
    {
        return Repeater::make('specification_groups')
            ->label('Nhóm thông số kỹ thuật')
            ->helperText('Ví dụ: Thông số kỹ thuật, Thiết kế, Kết nối, Pin. Mỗi thông số hiển thị gọn trên 1 dòng gồm Tên thông số và Giá trị (giống hệt Next.js Admin).')
            ->schema([
                TextInput::make('title')
                    ->label('Tên nhóm thông số')
                    ->placeholder('VD: Thông số kỹ thuật')
                    ->required()
                    ->maxLength(150),

                Repeater::make('rows')
                    ->label('Danh sách thông số')
                    ->schema([
                        TextInput::make('label')
                            ->label('Tên thông số')
                            ->placeholder('VD: Kết nối / Trọng lượng / Góc ngả...')
                            ->required()
                            ->maxLength(150)
                            ->columnSpan(5),

                        TextInput::make('value')
                            ->label('Giá trị')
                            ->placeholder('VD: 3 Mode (Type-C / 2.4G / Bluetooth)...')
                            ->required()
                            ->maxLength(2000)
                            ->columnSpan(7),

                        Hidden::make('source_label'),
                        Hidden::make('source_url'),
                        Hidden::make('verification_status')->default('verified'),
                    ])
                    ->columns(12)
                    ->minItems(1)
                    ->defaultItems(1)
                    ->reorderableWithButtons()
                    ->addActionLabel('Thêm dòng thông số'),
            ])
            ->defaultItems(0)
            ->reorderableWithButtons()
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => ($state['title'] ?? 'Nhóm thông số') . ' (' . count($state['rows'] ?? []) . ' dòng)')
            ->addActionLabel('Thêm nhóm thông số')
            ->columnSpanFull();
    }

    public static function research(): Section
    {
        return Section::make('Hồ sơ biên tập SEO — chỉ hiển thị trong admin')
            ->description('Lưu căn cứ để sửa bài sau này. Giá, tồn kho và bảo hành vẫn quản lý ở phiên bản.')
            ->schema([
                TextInput::make('seo_research.primary_keyword')->label('Từ khóa chính')->maxLength(200),
                Textarea::make('seo_research.secondary_keywords')->label('Từ khóa bổ trợ')->rows(2),
                TextInput::make('seo_research.reviewed_on')->label('Ngày đối chiếu')->maxLength(30),
                Textarea::make('seo_research.unknown_notes')->label('Chưa xác nhận / cần bổ sung')->rows(3),
                Textarea::make('seo_research.conflict_notes')->label('Mâu thuẫn và quyết định biên tập')->rows(3),
                Repeater::make('seo_research.sources')->label('Nguồn đã tra cứu')->schema([
                    TextInput::make('title')->label('Tên nguồn')->required(),
                    TextInput::make('url')->label('URL')->url()->required()->maxLength(2048),
                    Textarea::make('note')->label('Phạm vi sử dụng')->rows(2),
                ])->defaultItems(0)->collapsible()->collapsed(),
                Repeater::make('seo_research.images')->label('Ảnh shop đã đối chiếu')->schema([
                    TextInput::make('path')->label('Đường dẫn ảnh trong thư viện')->required(),
                    Textarea::make('note')->label('Nội dung quan sát / giới hạn')->rows(2),
                ])->defaultItems(0)->collapsible()->collapsed(),
            ])
            ->collapsible()
            ->collapsed();
    }

    public static function optionOrder(): Repeater
    {
        return Repeater::make('option_group_order')
            ->label('Thứ tự nhóm lựa chọn')
            ->schema([
                TextInput::make('name')->label('Tên nhóm')->required()->maxLength(100)->distinct(),
            ])
            ->helperText('Nhập đúng tên nhóm trong các biến thể, rồi sắp xếp thứ tự khách chọn. Ví dụ: Phiên bản → Màu sắc → Switch.')
            ->defaultItems(0)
            ->reorderableWithButtons()
            ->collapsible()
            ->collapsed()
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nhóm lựa chọn')
            ->columnSpanFull();
    }

    public static function variantSpecifications(): KeyValue
    {
        return KeyValue::make('specifications')
            ->label('Thông số riêng của biến thể')
            ->keyLabel('Tên thông số')
            ->valueLabel('Giá trị')
            ->reorderable()
            ->helperText('Dùng đúng tên thông số chung để thay giá trị khi chọn phiên bản này, ví dụ Pin → 10000mAh.')
            ->columnSpanFull();
    }
}
