<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;


class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Biểu tượng điều hướng
    protected static ?string $navigationLabel = 'Bảng nội dung';
    protected static ?string $navigationGroup = 'Quản lý nội dung';
    protected static ?int $navigationSort = 1;

    public static function getPluralModelLabel(): string
    {
        return 'Bảng nội dung';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông Tin Bài Viết') // Tiêu đề section
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Tiêu Đề') // Đổi nhãn sang tiếng Việt
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                        )
                        ->helperText('Nhập tiêu đề cho bài viết của bạn.'), // Mô tả dễ hiểu

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug') // Đổi nhãn sang tiếng Việt
                        ->required()
                        ->dehydrated()
                        ->unique(Blog::class, 'slug', ignoreRecord: true)
                        ->maxLength(255)
                        ->helperText('Slug sẽ tự động được tạo từ tiêu đề. Không cần nhập.'), // Mô tả dễ hiểu

                    Forms\Components\TextInput::make('short_description')
                        ->label('Mô Tả Ngắn') // Đổi nhãn sang tiếng Việt
                        ->required()
                        ->helperText('Nhập một mô tả ngắn gọn cho bài viết của bạn.'), // Mô tả dễ hiểu

                    Forms\Components\Select::make('content_type_id')
                        ->label('Loại nội dung')
                        ->relationship('contentType', 'name')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('content_category_id')
                        ->label('Danh mục nội dung')
                        ->relationship('contentCategory', 'name')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('status')
                        ->label('Trạng thái xuất bản')
                        ->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'archived' => 'Lưu trữ'])
                        ->default('draft')
                        ->required(),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Thời gian xuất bản'),

                        TinyEditor::make('description')
                        ->fileAttachmentsDirectory(directory: 'blogs')
                        ->label('Mô Tả Chi Tiết') // Đổi nhãn sang tiếng Việt
                        ->required()
                        ->helperText('Nhập mô tả chi tiết cho bài viết của bạn.'), // Mô tả dễ hiểu

                    Forms\Components\FileUpload::make('banner')
                        ->label('Ảnh Bìa') // Đổi nhãn sang tiếng Việt
                        ->required()
                        ->helperText('Tải lên ảnh bìa cho bài viết.'), // Mô tả dễ hiểu

                    Forms\Components\Section::make('Tối ưu SEO')
                        ->schema([
                            Forms\Components\TextInput::make('seo_title')->label('Tiêu đề SEO')->maxLength(60),
                            Forms\Components\Textarea::make('seo_description')->label('Mô tả SEO')->maxLength(160),
                        ])
                        ->columnSpanFull(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID') // Đổi nhãn sang tiếng Việt
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu Đề') // Đổi nhãn sang tiếng Việt
                    ->searchable(),

                Tables\Columns\ImageColumn::make('banner')
                    ->label('Ảnh Bìa') // Đổi nhãn sang tiếng Việt
                    ->searchable(),

                Tables\Columns\TextColumn::make('contentType.name')
                    ->label('Loại nội dung'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày Tạo') // Đổi nhãn sang tiếng Việt
                    ->dateTime()
                    ->sortable(),


                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày Cập Nhật') // Đổi nhãn sang tiếng Việt
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Có thể thêm bộ lọc nếu cần
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Chỉnh sửa'),
                    Tables\Actions\ViewAction::make()
                        ->label('Xem'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa'),
                ]),
            ])->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa'),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
