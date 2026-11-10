<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Danh sách sản phẩm';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Quản lý bán hàng';
    protected static ?string $recordTitleAttribute = 'name';

    public static function getPluralModelLabel(): string
    {
        return 'Danh sách sản phẩm';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        // CỘT TRÁI (9/12): 5 TAB QUẢN TRỊ SẢN PHẨM CHUẨN NEXTJS HG DIGITAL
                        Group::make()
                            ->schema([
                                Tabs::make('Quản lý sản phẩm')
                                    ->persistTabInQueryString()
                                    ->tabs([
                                        // TAB 1: THÔNG TIN CHUNG
                                        Tab::make('Thông tin chung')
                                            ->icon('heroicon-o-cube')
                                            ->schema([
                                                Section::make('Thông tin cơ bản & Giá bán')
                                                    ->description('Nhập tên đầy đủ của sản phẩm, giá bán hiển thị mặc định và hình ảnh đại diện.')
                                                    ->schema([
                                                        TextInput::make('name')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(
                                                                fn (string $operation, $state, Forms\Set $set) =>
                                                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                                            )
                                                            ->placeholder('Nhập tên đầy đủ của sản phẩm...')
                                                            ->label('Tên sản phẩm')
                                                            ->columnSpanFull(),

                                                        Grid::make(3)
                                                            ->schema([
                                                                TextInput::make('price')
                                                                    ->numeric()
                                                                    ->required()
                                                                    ->default(0)
                                                                    ->suffix('₫')
                                                                    ->label('Giá bán cơ bản (VNĐ)')
                                                                    ->helperText('Giá mặc định khi chưa chọn biến thể.'),

                                                                TextInput::make('discount_price')
                                                                    ->numeric()
                                                                    ->required()
                                                                    ->default(0)
                                                                    ->suffix('₫')
                                                                    ->label('Giá gốc / Giá giảm (VNĐ)')
                                                                    ->helperText('Dùng để đối chiếu hoặc hiển thị giá khuyến mãi.'),

                                                                Toggle::make('on_sale')
                                                                    ->label('Hiển thị trong Flash Sale')
                                                                    ->helperText('Bật để đưa sản phẩm vào khu vực khuyến mãi.')
                                                                    ->inline(false)
                                                                    ->default(false),
                                                            ]),
                                                    ]),

                                                Section::make('Ảnh chính & Thư viện ảnh phụ')
                                                    ->description('Kéo thả để sắp xếp thứ tự hiển thị. Ảnh đầu tiên sẽ được dùng làm ảnh đại diện chính của sản phẩm.')
                                                    ->collapsible()
                                                    ->schema([
                                                        FileUpload::make('images')
                                                            ->directory('products')
                                                            ->required()
                                                            ->enableReordering()
                                                            ->multiple()
                                                            ->image()
                                                            ->panelLayout('grid')
                                                            ->label('Thư viện hình ảnh sản phẩm'),
                                                    ]),
                                            ]),

                                        // TAB 2: BIẾN THỂ SẢN PHẨM
                                        Tab::make('Biến thể sản phẩm')
                                            ->icon('heroicon-o-squares-plus')
                                            ->badge(fn (Get $get): int => is_array($get('variants')) ? count($get('variants')) : 0)
                                            ->schema([
                                                Section::make('Cấu hình phân loại & Danh sách biến thể')
                                                    ->description('Quản lý thứ tự nhóm thuộc tính (Màu sắc, Switch, Layout...) và cấu hình giá, SKU, tồn kho riêng cho từng biến thể.')
                                                    ->schema([
                                                        \App\Filament\Forms\ProductContentFields::optionOrder(),

                                                        Repeater::make('variants')
                                                            ->label('Danh sách các biến thể')
                                                            ->relationship()
                                                            ->collapsible()
                                                            ->reorderableWithButtons()
                                                            ->itemLabel(function (array $state, Get $get): HtmlString {
                                                                $name = e($state['name'] ?? 'Biến thể mới');
                                                                $sku = e($state['sku'] ?? 'Chưa có SKU');
                                                                $price = isset($state['price']) && is_numeric($state['price'])
                                                                    ? number_format((float) $state['price'], 0, ',', '.') . '₫'
                                                                    : '0₫';
                                                                $isInStock = ($state['stock'] ?? 'in_stock') === 'in_stock';
                                                                $stockBadge = $isInStock
                                                                    ? "<span style='padding:2px 8px;border-radius:99px;font-size:11px;font-weight:700;background:rgba(16,185,129,0.18);color:#10b981;'>Còn hàng</span>"
                                                                    : "<span style='padding:2px 8px;border-radius:99px;font-size:11px;font-weight:700;background:rgba(239,68,68,0.18);color:#ef4444;'>Hết hàng</span>";

                                                                $rawImg = $state['image'] ?? null;
                                                                if (is_array($rawImg)) {
                                                                    $rawImg = reset($rawImg) ?: null;
                                                                }
                                                                if (!$rawImg || !is_string($rawImg)) {
                                                                    $productImages = $get('images');
                                                                    if (is_array($productImages) && !empty($productImages)) {
                                                                        $firstProdImg = reset($productImages);
                                                                        if (is_string($firstProdImg)) {
                                                                            $rawImg = $firstProdImg;
                                                                        }
                                                                    }
                                                                }
                                                                $imgHtml = (is_string($rawImg) && $rawImg !== '' && !str_starts_with($rawImg, 'livewire-file:'))
                                                                    ? "<img src='" . e(asset('storage/' . ltrim($rawImg, '/'))) . "' style='width:38px;height:38px;object-fit:cover;border-radius:8px;border:1px solid rgba(148,163,184,0.3);background:#fff;flex-shrink:0;' alt=''>"
                                                                    : "<span style='display:inline-grid;place-items:center;width:38px;height:38px;border-radius:8px;background:rgba(59,130,246,0.15);color:#60a5fa;font-weight:800;font-size:11px;flex-shrink:0;'>IMG</span>";

                                                                $classText = '';
                                                                if (!empty($state['classification']) && is_array($state['classification'])) {
                                                                    $pairs = [];
                                                                    foreach ($state['classification'] as $k => $v) {
                                                                        if ($k !== '' && $v !== '') {
                                                                            $pairs[] = e("{$k}: {$v}");
                                                                        }
                                                                    }
                                                                    if ($pairs) {
                                                                        $classText = "<span style='font-size:12px;color:#94a3b8;'>(" . implode(' • ', $pairs) . ")</span>";
                                                                    }
                                                                }

                                                                return new HtmlString("
                                                                    <div style='display:inline-flex;align-items:center;gap:10px;flex-wrap:wrap;'>
                                                                        {$imgHtml}
                                                                        <span style='padding:3px 8px;border-radius:6px;background:rgba(59,130,246,0.2);color:#60a5fa;font-family:monospace;font-size:12.5px;font-weight:800;'>SKU: {$sku}</span>
                                                                        <strong style='font-size:13.5px;'>{$name}</strong>
                                                                        {$classText}
                                                                        <span style='color:#f43f5e;font-weight:800;font-size:13px;'>{$price}</span>
                                                                        {$stockBadge}
                                                                    </div>
                                                                ");
                                                            })
                                                            ->schema([
                                                                TextInput::make('name')
                                                                    ->required()
                                                                    ->placeholder('VD: Đen - Red Switch - Bản Pro')
                                                                    ->label('Tên biến thể')
                                                                    ->columnSpan(2),

                                                                TextInput::make('sku')
                                                                    ->required()
                                                                    ->label('Mã SKU')
                                                                    ->default(fn () => strtoupper(Str::random(3)) . random_int(1000, 9999))
                                                                    ->unique(ProductVariant::class, 'sku', ignoreRecord: true)
                                                                    ->columnSpan(1),

                                                                Select::make('stock')
                                                                    ->options([
                                                                        'in_stock' => 'Còn hàng (In Stock)',
                                                                        'out_of_stock' => 'Tạm hết hàng (Out of Stock)',
                                                                    ])
                                                                    ->default('in_stock')
                                                                    ->required()
                                                                    ->label('Trạng thái kho')
                                                                    ->columnSpan(1),

                                                                TextInput::make('price')
                                                                    ->numeric()
                                                                    ->required()
                                                                    ->suffix('₫')
                                                                    ->label('Giá bán (VNĐ)')
                                                                    ->columnSpan(1),

                                                                TextInput::make('discount_price')
                                                                    ->numeric()
                                                                    ->required()
                                                                    ->nullable()
                                                                    ->suffix('₫')
                                                                    ->label('Giá nhập / Giá gốc')
                                                                    ->columnSpan(1),

                                                                TextInput::make('warranty_days')
                                                                    ->numeric()
                                                                    ->label('Thời hạn bảo hành (Ngày)')
                                                                    ->default(0)
                                                                    ->required()
                                                                    ->nullable()
                                                                    ->suffix('ngày')
                                                                    ->columnSpan(1),

                                                                DateTimePicker::make('updated_at')
                                                                    ->disabled()
                                                                    ->label('Cập nhật gần nhất')
                                                                    ->nullable()
                                                                    ->columnSpan(1),

                                                                Forms\Components\KeyValue::make('classification')
                                                                    ->label('Nhóm phân loại (Màu sắc, Switch, Phiên bản...)')
                                                                    ->keyLabel('Tên nhóm')
                                                                    ->valueLabel('Giá trị')
                                                                    ->helperText('Ví dụ: Dòng máy → Pro X; Màu sắc → Black. Các biến thể cần dùng cùng tên nhóm.')
                                                                    ->columnSpan(3),

                                                                FileUpload::make('image')
                                                                    ->label('Ảnh biến thể')
                                                                    ->imageEditor()
                                                                    ->directory('product_variants')
                                                                    ->nullable()
                                                                    ->columnSpan(1),

                                                                \App\Filament\Forms\ProductContentFields::variantSpecifications(),
                                                            ])
                                                            ->columns(4)
                                                            ->minItems(1),
                                                    ]),
                                            ]),

                                        // TAB 3: MÔ TẢ & BÀI VIẾT
                                        Tab::make('Mô tả & Bài viết')
                                            ->icon('heroicon-o-document-text')
                                            ->schema([
                                                Section::make('Mô tả tóm tắt & Nội dung bài viết')
                                                    ->description('Quản lý đoạn mô tả ngắn, bài viết chi tiết gốc và các khối mô tả có cấu trúc chuẩn hiển thị.')
                                                    ->schema([
                                                        Textarea::make('short_description')
                                                            ->rows(3)
                                                            ->placeholder('Nhập đoạn mô tả ngắn gọn (2-3 câu) tóm tắt đặc điểm nổi bật nhất của sản phẩm...')
                                                            ->columnSpanFull()
                                                            ->label('Mô tả tóm tắt sản phẩm'),

                                                        TinyEditor::make('description')
                                                            ->columnSpanFull()
                                                            ->fileAttachmentsDirectory(directory: 'products')
                                                            ->label('Bài viết chi tiết & Đánh giá (Bản gốc)')
                                                            ->helperText('Bản mô tả gốc được giữ lại. Website tự chuẩn hóa font, cỡ chữ và chuyển các thông số nhận diện được sang tab riêng.'),

                                                        \App\Filament\Forms\ProductContentFields::sections(),
                                                    ]),
                                            ]),

                                        // TAB 4: THÔNG SỐ KỸ THUẬT
                                        Tab::make('Thông số kỹ thuật')
                                            ->icon('heroicon-o-cpu-chip')
                                            ->schema([
                                                Section::make('Bảng thông số kỹ thuật chi tiết')
                                                    ->description('Các nhóm thông số đã xác minh sẽ hiển thị trực tiếp ở tab Thông số kỹ thuật trên trang sản phẩm.')
                                                    ->schema([
                                                        \App\Filament\Forms\ProductContentFields::specifications(),
                                                    ]),
                                            ]),

                                        // TAB 5: TỐI ƯU SEO
                                        Tab::make('Tối ưu SEO')
                                            ->icon('heroicon-o-magnifying-glass-circle')
                                            ->schema([
                                                Section::make('Tối ưu công cụ tìm kiếm (SEO) & Xem trước Google')
                                                    ->description('Tối ưu tiêu đề, mô tả meta và theo dõi trực tiếp kết quả hiển thị trên Google Search.')
                                                    ->schema([
                                                        Placeholder::make('seo_serp_preview')
                                                            ->label('Tổng quan chỉ số SEO & Xem trước kết quả tìm kiếm (Google SERP)')
                                                            ->content(function (Get $get): HtmlString {
                                                                $name = trim((string) ($get('name') ?? 'Tên sản phẩm'));
                                                                $slug = trim((string) ($get('slug') ?? Str::slug($name)));
                                                                $seoTitle = trim((string) ($get('seo_title') ?? ''));
                                                                $seoDesc = trim((string) ($get('seo_description') ?? ''));
                                                                $shortDesc = trim(strip_tags((string) ($get('short_description') ?? '')));

                                                                $displayTitle = $seoTitle !== '' ? $seoTitle : ($name !== '' ? "{$name} | The BABU Store" : 'Tên sản phẩm | The BABU Store');
                                                                $displayDesc = $seoDesc !== ''
                                                                    ? $seoDesc
                                                                    : ($shortDesc !== '' ? Str::limit($shortDesc, 160) : 'Mua sản phẩm chính hãng tại The BABU Store — Giao hàng toàn quốc, bảo hành uy tín, hỗ trợ kỹ thuật tận tâm.');

                                                                $titleLen = mb_strlen($displayTitle);
                                                                $descLen = mb_strlen($displayDesc);

                                                                $titleColor = ($titleLen >= 40 && $titleLen <= 65) ? '#10b981' : ($titleLen > 65 ? '#ef4444' : '#f59e0b');
                                                                $descColor = ($descLen >= 120 && $descLen <= 165) ? '#10b981' : ($descLen > 165 ? '#ef4444' : '#f59e0b');

                                                                $escapedUrl = e(url('/product/' . ($slug !== '' ? $slug : 'duong-dan-san-pham')));
                                                                $escapedTitle = e(Str::limit($displayTitle, 70));
                                                                $escapedDesc = e(Str::limit($displayDesc, 170));

                                                                return new HtmlString("
                                                                    <div style='display:grid;gap:12px;padding:16px;border-radius:12px;background:rgba(15,23,42,0.55);border:1px solid rgba(148,163,184,0.2);'>
                                                                        <div style='display:flex;flex-wrap:wrap;gap:16px;font-size:13px;'>
                                                                            <span style='display:inline-flex;align-items:center;gap:6px;'>
                                                                                <span style='width:8px;height:8px;border-radius:999px;background:{$titleColor};display:inline-block;'></span>
                                                                                <strong>Độ dài Tiêu đề SEO:</strong> {$titleLen} ký tự (Khuyến nghị: 50–60)
                                                                            </span>
                                                                            <span style='display:inline-flex;align-items:center;gap:6px;'>
                                                                                <span style='width:8px;height:8px;border-radius:999px;background:{$descColor};display:inline-block;'></span>
                                                                                <strong>Độ dài Mô tả SEO:</strong> {$descLen} ký tự (Khuyến nghị: 140–160)
                                                                            </span>
                                                                        </div>
                                                                        <div style='padding:14px 16px;border-radius:10px;background:#ffffff;color:#202124;box-shadow:0 1px 3px rgba(0,0,0,0.12);font-family:Arial,sans-serif;'>
                                                                            <div style='font-size:12px;color:#4d5156;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;'>
                                                                                The BABU Store — {$escapedUrl}
                                                                            </div>
                                                                            <div style='font-size:18px;line-height:1.35;color:#1a0dab;font-weight:500;margin-bottom:4px;'>
                                                                                {$escapedTitle}
                                                                            </div>
                                                                            <div style='font-size:13px;line-height:1.5;color:#4d5156;'>
                                                                                {$escapedDesc}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                ");
                                                            }),

                                                        TextInput::make('seo_title')
                                                            ->label('Tiêu đề SEO')
                                                            ->maxLength(255)
                                                            ->live(onBlur: true)
                                                            ->helperText('Tiêu đề riêng, rõ tên/model sản phẩm. Để trống để dùng tên sản phẩm và tên cửa hàng.'),

                                                        Textarea::make('seo_description')
                                                            ->label('Mô tả tìm kiếm')
                                                            ->rows(3)
                                                            ->maxLength(320)
                                                            ->live(onBlur: true)
                                                            ->helperText('Tóm tắt nội dung thật, ưu tiên khoảng 140–160 ký tự; không nhồi từ khóa.'),

                                                        TinyEditor::make('warranty_content')
                                                            ->label('Điều kiện bảo hành riêng của sản phẩm')
                                                            ->fileAttachmentsDirectory(directory: 'products')
                                                            ->helperText('Hiển thị cùng chính sách chung; không sửa thời hạn bảo hành của biến thể ở đây.'),
                                                    ]),

                                                \App\Filament\Forms\ProductContentFields::research(),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['default' => 12, 'lg' => 9]),

                        // CỘT PHẢI (3/12): SIDEBAR ĐƯỜNG DẪN TĨNH, DANH MỤC & TRẠNG THÁI KINH DOANH
                        Group::make()
                            ->schema([
                                Section::make('Thiết lập & Phân loại')
                                    ->schema([
                                        TextInput::make('slug')
                                            ->required()
                                            ->dehydrated()
                                            ->unique(Product::class, 'slug', ignoreRecord: true)
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->label('Đường dẫn tĩnh (Slug)')
                                            ->helperText('Tự động tạo từ tên sản phẩm.'),

                                        Select::make('category_id')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->label('Danh mục sản phẩm')
                                            ->options(fn () => Category::query()->pluck('name', 'id'))
                                            ->placeholder('Chọn danh mục sản phẩm'),

                                        Select::make('subcategory_id')
                                            ->searchable()
                                            ->preload()
                                            ->relationship('subcategory', 'name')
                                            ->label('Danh mục con')
                                            ->placeholder('Chọn danh mục phụ'),

                                        Select::make('brand_id')
                                            ->searchable()
                                            ->preload()
                                            ->relationship('brand', 'name')
                                            ->label('Thương hiệu')
                                            ->placeholder('Chọn thương hiệu'),

                                        Toggle::make('is_active')
                                            ->label('Hiển thị trên cửa hàng')
                                            ->default(true)
                                            ->helperText('Tắt để tạm ẩn sản phẩm khỏi danh sách công khai.'),

                                        Toggle::make('is_featured')
                                            ->label('Sản phẩm nổi bật')
                                            ->default(false),

                                        Placeholder::make('variant_stock_summary')
                                            ->label('Trạng thái kinh doanh (Theo biến thể)')
                                            ->content(function (Get $get, ?Product $record): HtmlString {
                                                $variants = $get('variants');
                                                if (is_array($variants) && count($variants) > 0) {
                                                    $total = count($variants);
                                                    $inStock = collect($variants)->filter(fn ($v) => ($v['stock'] ?? 'in_stock') !== 'out_of_stock')->count();
                                                } elseif ($record) {
                                                    $total = $record->variants()->count();
                                                    $inStock = $record->variants()->where('stock', '!=', 'out_of_stock')->count();
                                                } else {
                                                    $total = 0;
                                                    $inStock = 0;
                                                }

                                                $badge = $inStock > 0
                                                    ? "<span style='display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;background:rgba(16,185,129,0.15);color:#10b981;'>● Còn hàng ({$inStock}/{$total} biến thể)</span>"
                                                    : "<span style='display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;background:rgba(239,68,68,0.15);color:#ef4444;'>● Tạm hết hàng (0/{$total} biến thể)</span>";

                                                $slug = $get('slug') ?? $record?->slug;
                                                $link = $slug
                                                    ? "<div style='margin-top:10px;'><a href='" . e(url('/product/' . $slug)) . "' target='_blank' style='font-size:12px;color:#3b82f6;text-decoration:underline;'>↗ Xem trang sản phẩm ngoài cửa hàng</a></div>"
                                                    : '';

                                                return new HtmlString($badge . $link);
                                            }),
                                    ]),
                            ])
                            ->columnSpan(['default' => 12, 'lg' => 3]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('images')
                    ->label('Ảnh')
                    ->circular()
                    ->stacked()
                    ->limit(1),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên Sản Phẩm')
                    ->description(fn (Product $record): string => $record->slug ?? '')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh Mục')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Giá Bán')
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_status')
                    ->label('Trạng Thái Kho')
                    ->state(function (Product $record): string {
                        $inStock = $record->variants->where('stock', '!=', 'out_of_stock')->count();
                        $total = $record->variants->count();
                        return $inStock > 0 ? "Còn hàng ({$inStock}/{$total})" : "Tạm hết ({$total})";
                    })
                    ->badge()
                    ->color(fn (string $state): string => str_starts_with($state, 'Còn hàng') ? 'success' : 'danger'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Hiển Thị'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('variants.sku')
                    ->label('Mã SKU Biến Thể')
                    ->badge()
                    ->color('info')
                    ->separator(',')
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày Tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày Cập Nhật')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Danh Mục')
                    ->relationship('category', 'name')
                    ->preload(),

                SelectFilter::make('brand')
                    ->label('Thương Hiệu')
                    ->relationship('brand', 'name')
                    ->preload(),

                SelectFilter::make('stock_filter')
                    ->label('Trạng Thái Kho')
                    ->options([
                        'in_stock' => 'Còn hàng (Có biến thể còn hàng)',
                        'out_of_stock' => 'Tạm hết hàng (Tất cả biến thể hết hàng)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'in_stock' => $query->whereHas('variants', fn (Builder $q) => $q->where('stock', '!=', 'out_of_stock')),
                            'out_of_stock' => $query->whereDoesntHave('variants', fn (Builder $q) => $q->where('stock', '!=', 'out_of_stock')),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Xem'),
                    Tables\Actions\EditAction::make()
                        ->label('Chỉnh Sửa'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa Nhiều'),
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
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
