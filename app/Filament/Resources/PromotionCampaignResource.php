<?php
namespace App\Filament\Resources;
use App\Filament\Resources\PromotionCampaignResource\Pages;
use App\Models\PromotionCampaign;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
class PromotionCampaignResource extends Resource {
    protected static ?string $model=PromotionCampaign::class;
    protected static ?string $slug='promotion-campaigns';
    protected static ?string $navigationIcon='heroicon-o-megaphone';
    protected static ?string $navigationLabel='Chiến dịch khuyến mãi';
    protected static ?string $navigationGroup='Quản lý bán hàng';
    protected static ?int $navigationSort=7;
    public static function getModelLabel(): string { return 'chiến dịch khuyến mãi'; }
    public static function getPluralModelLabel(): string { return 'Chiến dịch khuyến mãi'; }
    public static function form(Form $form): Form {
        return $form->schema([
            Forms\Components\Section::make('Nội dung trang chiến dịch')->schema([
                Forms\Components\TextInput::make('name')->label('Tên quản lý')->required()->maxLength(255)->live(onBlur:true)->afterStateUpdated(fn($state,Forms\Set $set,string $operation)=>$operation==='create'?$set('slug',Str::slug($state)):null),
                Forms\Components\TextInput::make('slug')->label('Đường dẫn chiến dịch')->required()->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord:true)->maxLength(180)->helperText('black-friday mở tại /black-friday. Các chiến dịch khác mở tại /khuyen-mai/đường-dẫn.'),
                Forms\Components\TextInput::make('headline')->label('Tiêu đề trang')->required()->maxLength(255),
                Forms\Components\Textarea::make('intro')->label('Giới thiệu ngắn')->rows(3)->maxLength(1500),
                Forms\Components\Textarea::make('terms')->label('Điều kiện áp dụng')->rows(4)->maxLength(5000)->columnSpanFull()->helperText('Chỉ nhập điều kiện ưu đãi thực tế của cửa hàng.'),
            ])->columns(2),
            Forms\Components\Section::make('Banner riêng của chiến dịch')->schema([
                Forms\Components\FileUpload::make('product_banner_image')->label('Banner nhỏ trên trang sản phẩm')->image()->disk('public')->directory('promotions')->maxSize(5120)->helperText('Tỉ lệ 3:1, khuyến nghị 1440 × 480 px. Bấm banner sẽ mở đúng trang chiến dịch.'),
                Forms\Components\FileUpload::make('banner_image')->label('Banner đầu trang chiến dịch')->image()->disk('public')->directory('promotions')->maxSize(5120)->helperText('Tỉ lệ 3:1, khuyến nghị 2160 × 720 px.'),
                Forms\Components\FileUpload::make('mobile_banner_image')->label('Banner trang chiến dịch trên điện thoại')->image()->disk('public')->directory('promotions')->maxSize(5120)->helperText('Tỉ lệ 3:2, khuyến nghị 1200 × 800 px.'),
            ])->columns(3),
            Forms\Components\Section::make('Hiển thị và thời gian')->schema([
                Forms\Components\Toggle::make('is_active')->label('Bật trang chiến dịch')->default(false),
                Forms\Components\Toggle::make('show_product_banner')->label('Hiện banner nhỏ trên các trang sản phẩm')->default(false),
                Forms\Components\DateTimePicker::make('starts_at')->label('Bắt đầu')->timezone('Asia/Bangkok'),
                Forms\Components\DateTimePicker::make('ends_at')->label('Kết thúc')->timezone('Asia/Bangkok')->after(fn(Forms\Get $get) => $get('starts_at') ?: ''),
                Forms\Components\TextInput::make('priority')->label('Độ ưu tiên banner')->integer()->default(0)->minValue(0)->helperText('Khi nhiều chiến dịch cùng chạy, banner có số cao hơn được ưu tiên.'),
            ])->columns(2),
            Forms\Components\Section::make('Sản phẩm sale của riêng chiến dịch')->description('Danh sách này độc lập với mục Slide và cờ sale chung. Giá hiển thị và thanh toán lấy từ giá biến thể trong mục Sản phẩm; không tự tạo mức giảm. Sản phẩm hết hàng tự ẩn khỏi trang nhưng vẫn giữ trong danh sách quản lý.')->schema([
                Forms\Components\Repeater::make('items')->label('Danh sách sản phẩm')->relationship()->orderColumn('sort_order')->defaultItems(0)->reorderable()->collapsible()->addActionLabel('Thêm sản phẩm vào chiến dịch')->schema([
                    Forms\Components\Select::make('product_id')->label('Sản phẩm')->required()->searchable()->distinct()->getSearchResultsUsing(fn(string $search)=>Product::query()->where('is_active',true)->whereHas('variants',fn($q)=>$q->where('stock','in_stock'))->where('name','like','%'.$search.'%')->orderBy('name')->limit(40)->pluck('name','id')->all())->getOptionLabelUsing(fn($value)=>Product::find($value)?->name),
                    Forms\Components\TextInput::make('badge')->label('Nhãn nhỏ (không bắt buộc)')->maxLength(60)->placeholder('Ưu đãi Black Friday'),
                    Forms\Components\Toggle::make('is_active')->label('Hiển thị sản phẩm')->default(true),
                ])->columns(3),
            ]),
        ]);
    }
    public static function table(Table $table): Table {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Chiến dịch')->searchable(),
            Tables\Columns\TextColumn::make('slug')->label('Đường dẫn')->searchable(),
            Tables\Columns\IconColumn::make('is_active')->label('Bật trang')->boolean(),
            Tables\Columns\IconColumn::make('show_product_banner')->label('Banner sản phẩm')->boolean(),
            Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Sản phẩm'),
            Tables\Columns\TextColumn::make('starts_at')->label('Bắt đầu')->dateTime('d/m/Y H:i')->timezone('Asia/Bangkok'),
            Tables\Columns\TextColumn::make('ends_at')->label('Kết thúc')->dateTime('d/m/Y H:i')->timezone('Asia/Bangkok'),
        ])->actions([
            Tables\Actions\Action::make('open')->label('Mở trang')->icon('heroicon-o-arrow-top-right-on-square')->url(fn(PromotionCampaign $record)=>$record->publicUrl())->openUrlInNewTab(),
            Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(),
        ]);
    }
    public static function getPages(): array { return ['index'=>Pages\ListPromotionCampaigns::route('/'),'create'=>Pages\CreatePromotionCampaign::route('/create'),'edit'=>Pages\EditPromotionCampaign::route('/{record}/edit')]; }
}
