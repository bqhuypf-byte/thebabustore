<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog'; // Thay đổi biểu tượng
    protected static ?string $navigationLabel = 'Cài đặt Website';

    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?int $navigationSort = 4;

    protected static string $title = 'Quản Lý Cài Đặt'; // Thêm tiêu đề

    public static function getPluralModelLabel(): string
    {
        return 'Cài đặt Website';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Cài đặt') // Đổi tên tab
                    ->tabs([
                        \App\Support\RepairContentSchema::tab(),
                        Tabs\Tab::make('Trang chủ')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Placeholder::make('homepage_help')
                                    ->label('Bố cục trang chủ')
                                    ->content('Banner đầu trang lấy từ mục Slide; nếu chưa có Slide sẽ dùng Banner Web. Danh mục, sản phẩm và bài viết lấy từ dữ liệu hiện có. Thêm các khối bên dưới theo thứ tự mong muốn; chỉ nhập nội dung và hình ảnh thực tế.'),
                                Forms\Components\Repeater::make('homepage_sections')
                                    ->label('Các khối nội dung sau sản phẩm')
                                    ->afterStateHydrated(function (Forms\Components\Repeater $component, $state, ?Setting $record) {
                                        if ($state === null) {
                                            $component->state(\App\Support\HomepageContent::defaults($record));
                                        }
                                    })
                                    ->schema([
                                        Forms\Components\Toggle::make('enabled')->label('Hiển thị')->default(true),
                                        Forms\Components\Select::make('layout')->label('Bố cục')->options([
                                            'split' => 'Giới thiệu: ảnh và nội dung hai cột',
                                            'banner' => 'Banner: HUB / cộng đồng',
                                            'newsletter' => 'Đăng ký email: ảnh nền',
                                            'catalog' => 'Banner sản phẩm: ảnh nền và chữ nổi',
                                            'cards' => 'Thẻ: người giới thiệu / ảnh khách hàng',
                                            'faq' => 'Câu hỏi thường gặp',
                                            'setup' => 'Góc setup: ba ảnh khách hàng',
                                        ])->required()->default('split')->live(),
                                        Forms\Components\TextInput::make('eyebrow')->label('Dòng nhãn nhỏ')->maxLength(80)->visible(fn (Forms\Get $get) => $get('layout') === 'catalog'),
                                        Forms\Components\TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                                        Forms\Components\Textarea::make('body')->label('Nội dung')->rows(3),
                                        Forms\Components\FileUpload::make('image')->label('Ảnh chính')->image()->disk('public')->directory('homepage')->visible(fn (Forms\Get $get) => in_array($get('layout'), ['split', 'banner', 'catalog', 'newsletter'])),
                                        Forms\Components\Toggle::make('full_link')->label('Bấm toàn bộ banner để mở liên kết')->default(false)->visible(fn (Forms\Get $get) => in_array($get('layout'), ['split', 'banner', 'catalog'])),
                                        Forms\Components\TextInput::make('button_label')->label('Chữ trên nút')->maxLength(100)->visible(fn (Forms\Get $get) => in_array($get('layout'), ['split', 'banner', 'catalog'])),
                                        Forms\Components\TextInput::make('link')->label('Liên kết')->maxLength(2048)->rules(['nullable', 'regex:~^(?:/(?!/)|https?://)[^\s]*$~i'])->helperText('Đường dẫn nội bộ /shop hoặc URL https://...')->visible(fn (Forms\Get $get) => in_array($get('layout'), ['split', 'banner', 'catalog'])),
                                        Forms\Components\Repeater::make('items')->label('Các mục')->schema([
                                            Forms\Components\TextInput::make('title')->label('Tiêu đề / câu hỏi')->required()->maxLength(255),
                                            Forms\Components\Textarea::make('body')->label('Nội dung / câu trả lời')->rows(3),
                                            Forms\Components\FileUpload::make('image')->label('Ảnh')->image()->disk('public')->directory('homepage'),
                                            Forms\Components\TextInput::make('link')->label('Liên kết')->maxLength(2048)->rules(['nullable', 'regex:~^(?:/(?!/)|https?://)[^\s]*$~i']),
                                        ])->defaultItems(0)->collapsible()->itemLabel(fn (array $state): ?string => $state['title'] ?? null)->visible(fn (Forms\Get $get) => in_array($get('layout'), ['cards', 'faq', 'setup'])),
                                    ])->defaultItems(0)->collapsible()->itemLabel(fn (array $state): ?string => $state['title'] ?? null)->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Thông Tin')
    ->icon('heroicon-o-information-circle')
    ->schema([
        // Section: Thông Tin Liên Hệ
        Forms\Components\Section::make('Thông Tin Liên Hệ')
            ->schema([
                Forms\Components\TextInput::make('address')
                    ->maxLength(255)
                    ->default(null)
                    ->label('Địa chỉ'),
                Forms\Components\TextInput::make('map')
                    ->default(null)
                    ->label('Link google map cửa hàng'),

                Forms\Components\TextInput::make('hotline')
                    ->maxLength(255)
                    ->default(null)
                    ->label('Hotline'),

                Forms\Components\TextInput::make('support_hours')
                    ->maxLength(255)
                    ->label('Giờ hỗ trợ hiển thị ở nút liên hệ')
                    ->placeholder('Ví dụ: 8h30 - 20h00'),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->default(null)
                    ->label('Email'),

                Forms\Components\TextInput::make('website')
                    ->maxLength(255)
                    ->default(null)
                    ->label('Website'),

                Forms\Components\TextInput::make('copyright')
                    ->maxLength(255)
                    ->default(null)
                    ->label('Copyright'),
            ]),

        // Section: Logo
        Forms\Components\Section::make('Logo')
            ->schema([
                Forms\Components\TextInput::make('web_name')
                    ->default(null)
                    ->label('Tên Website'),
                Forms\Components\FileUpload::make('logo')
                    ->disk('public')
                    ->directory('branding')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                    ->imagePreviewHeight('100')
                    ->maxSize(4096)
                    ->default(null)
                    ->label('Logo header & footer')
                    ->helperText('Logo hiển thị trên thanh menu và chân trang. Nên dùng ảnh ngang, nền trong suốt; hỗ trợ SVG, PNG, WebP, JPG.'),

                Forms\Components\FileUpload::make('logo_light')
                    ->default(null)
                    ->label('Logo Ánh Sáng'),
            ]),

        // Section: Thông Tin Ngân Hàng
        Forms\Components\Section::make('Thông Tin Ngân Hàng')
            ->schema([
                Forms\Components\TextInput::make('so_tai_khoan')
                    ->maxLength(255)
                    ->default(null)
                    ->label('Số Tài Khoản'),

                Forms\Components\TextInput::make('ngan_hang')
                    ->maxLength(255)
                    ->default(null)
                    ->label('Ngân Hàng'),
            ]),

        // Section: Mạng Xã Hội
        Forms\Components\Section::make('Mạng Xã Hội')
            ->schema([
                Forms\Components\TextInput::make('facebook')
                    ->label('Facebook')
                    ->placeholder('Nhập link Facebook'),

                Forms\Components\TextInput::make('messenger')
                    ->label('Messenger')
                    ->placeholder('Nhập link Messenger'),

                Forms\Components\TextInput::make('youtube')
                    ->label('YouTube')
                    ->placeholder('Nhập link YouTube'),

                Forms\Components\TextInput::make('shopee')
                    ->label('Shopee')
                    ->placeholder('Nhập link Shopee'),

                Forms\Components\TextInput::make('zalo')
                    ->label('Zalo')
                    ->placeholder('Nhập link Zalo'),
                Forms\Components\TextInput::make('ministry_of_industry_and_trade_link')
                    ->label('Bộ Công Thương')
                    ->placeholder('Nhập link Bộ Công Thương'),

                Forms\Components\FileUpload::make('web_icon')
                    ->label('Icon Web')
                    ->placeholder('Upload  icon'),
                Forms\Components\FileUpload::make('banner_web')
                    ->label('Banner Web')
                    ->placeholder('Upload Banner Web'),
                Forms\Components\Textarea::make('description_web')
                    ->label('Mô tả Web')
                    ->placeholder('Nhập mô tả Web'),
            ]),

        // Section: Mô Tả Công Ty
        Forms\Components\Section::make('Mô Tả Công Ty')
            ->schema([
                Forms\Components\Textarea::make('company_description')
                    ->label('Mô tả công ty')
                    ->placeholder('Nhập mô tả về công ty')
                    ->rows(5), // Số hàng hiển thị
            ]),
    ]),

                        Tabs\Tab::make('Hướng dẫn')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\RichEditor::make('purchase_guide')
                                    ->columnSpanFull()
                                    ->label('Hướng dẫn mua hàng'),
                            ]),

                        Tabs\Tab::make('Thanh toán')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Forms\Components\RichEditor::make('payment_guide')
                                    ->columnSpanFull()
                                    ->label('Hướng dẫn thanh toán'),
                            ]),

                        Tabs\Tab::make('Vận chuyển')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Forms\Components\RichEditor::make('shipping_policy')
                                    ->columnSpanFull()
                                    ->label('Chính sách vận chuyển và giao nhận'),
                            ]),

                        Tabs\Tab::make('Bảo hành')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\RichEditor::make('warranty_policy')
                                    ->columnSpanFull()
                                    ->label('Chính sách bảo hành')
                                    ->helperText('Mỗi tiêu đề lớn (H2) sẽ tự động hiển thị thành 1 mục đóng/mở (accordion) trong tab Chính sách bảo hành của trang sản phẩm.'),
                            ]),

                        Tabs\Tab::make('Bảo mật')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Forms\Components\RichEditor::make('privacy_policy')
                                    ->columnSpanFull()
                                    ->label('Chính sách bảo mật'),
                            ]),

                        Tabs\Tab::make('Đổi trả')
                            ->icon('heroicon-o-check-circle')
                            ->schema([
                                Forms\Components\RichEditor::make('return_policy')
                                    ->columnSpanFull()
                                    ->label('Chính sách đổi trả hàng và hoàn tiền'),
                            ]),

                        Tabs\Tab::make('Giới Thiệu')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\RichEditor::make('introduction')
                                    ->columnSpanFull()
                                    ->label('Giới thiệu'),
                            ]),

                        Tabs\Tab::make('Giao diện & Cửa hàng')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Forms\Components\Section::make('Cấu Hình Mã Đơn Hàng')
                                    ->description('Điều chỉnh tiền tố và quy tắc sinh mã đơn hàng cho khách đặt hàng trên website')
                                    ->schema([
                                        Forms\Components\TextInput::make('order_code_prefix')
                                            ->label('Tiền tố mã đơn hàng')
                                            ->placeholder('Ví dụ: BB, WEB, DH')
                                            ->default('BB')
                                            ->required()
                                            ->maxLength(10),
                                        Forms\Components\TextInput::make('order_code_digits')
                                            ->label('Số chữ số ngẫu nhiên')
                                            ->numeric()
                                            ->default(6)
                                            ->minValue(4)
                                            ->maxValue(12)
                                            ->required()
                                            ->helperText('Ví dụ: Tiền tố BB + 6 chữ số tạo ra mã định dạng BB123456'),
                                    ])->columns(2),

                                Forms\Components\Section::make('Thanh Thông Báo Trên Cùng (Announcement Bar)')
                                    ->description('Hiển thị thông báo khuyến mãi và bộ đếm ngược chạy 1s tick chuẩn ATK Store')
                                    ->schema([
                                        Forms\Components\Toggle::make('announcement_enabled')
                                            ->label('Bật thanh thông báo')
                                            ->default(true),
                                        Forms\Components\TextInput::make('announcement_text')
                                            ->label('Nội dung thông báo')
                                            ->default('Ưu đãi thành viên BABU: Miễn phí vận chuyển cho đơn từ 800k • Đăng ký nhận quà')
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('announcement_button_text')
                                            ->label('Chữ trên nút (Pill button)')
                                            ->default('Chi tiết'),
                                        Forms\Components\TextInput::make('announcement_link')
                                            ->label('Link điều hướng')
                                            ->default('/dang-ky'),
                                        Forms\Components\DateTimePicker::make('announcement_countdown_end')
                                            ->label('Thời gian kết thúc đếm ngược (nếu có)')
                                            ->helperText('Bộ đếm ngược ngày/giờ/phút/giây sẽ tự động đếm lùi về thời điểm này'),
                                    ])->columns(2),

                                Forms\Components\Section::make('Mức Phân Tầng Ưu Đãi Giỏ Hàng (Cart Drawer Tiers)')
                                    ->description('Thanh tiến trình trong giỏ hàng trượt 400px (Cart Drawer) thúc đẩy giá trị đơn hàng')
                                    ->schema([
                                        Forms\Components\TextInput::make('cart_tier_1_amount')
                                            ->label('Mức 1 - Số tiền tối thiểu (VNĐ)')
                                            ->numeric()
                                            ->default(300000),
                                        Forms\Components\TextInput::make('cart_tier_1_reward')
                                            ->label('Mức 1 - Phần thưởng / Quà tặng')
                                            ->default('Tặng dụng cụ vệ sinh phím'),

                                        Forms\Components\TextInput::make('cart_tier_2_amount')
                                            ->label('Mức 2 - Số tiền tối thiểu (VNĐ)')
                                            ->numeric()
                                            ->default(800000),
                                        Forms\Components\TextInput::make('cart_tier_2_reward')
                                            ->label('Mức 2 - Phần thưởng / Quà tặng')
                                            ->default('Freeship toàn quốc'),

                                        Forms\Components\TextInput::make('cart_tier_3_amount')
                                            ->label('Mức 3 - Số tiền tối thiểu (VNĐ)')
                                            ->numeric()
                                            ->default(1500000),
                                        Forms\Components\TextInput::make('cart_tier_3_reward')
                                            ->label('Mức 3 - Phần thưởng / Quà tặng')
                                            ->default('Tặng lót chuột Gaming BABU'),
                                    ])->columns(2),

                                Forms\Components\Section::make('Tiện Ích Chuỗi Đăng Nhập / Thành Viên BABU')
                                    ->description('Tiện ích ghim cố định góc trái dưới cùng (Streak / Loyalty Widget)')
                                    ->schema([
                                        Forms\Components\Toggle::make('streak_widget_enabled')
                                            ->label('Bật hiển thị tiện ích thành viên')
                                            ->default(true),
                                        Forms\Components\TextInput::make('streak_widget_title')
                                            ->label('Tiêu đề tiện ích')
                                            ->default('Thành viên BABU'),
                                        Forms\Components\TextInput::make('streak_widget_subtitle')
                                            ->label('Mô tả ngắn / Lời kêu gọi')
                                            ->default('Đăng nhập nhận ưu đãi & tích lũy đổi quà'),
                                    ])->columns(2),
                            ]),


                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Ngày cập nhật'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
