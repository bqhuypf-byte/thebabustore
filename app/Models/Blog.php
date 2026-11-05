<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'short_description',
        'banner',
        'slug',
        'content_type_id',
        'content_category_id',
        'status',
        'published_at',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    public function contentCategory()
    {
        return $this->belongsTo(ContentCategory::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            // Xóa thumbnail nếu thay đổi
            if ($model->isDirty('banner') && $model->getOriginal('banner')) {
                Storage::disk('public')->delete($model->getOriginal('banner'));
            }



            // Xóa ảnh bị loại khỏi content
            if ($model->isDirty('description') && $model->getOriginal('description')) {
                $oldImages = self::extractImagePaths($model->getOriginal('description'));
                $newImages = self::extractImagePaths($model->description);

                $deletedImages = array_diff($oldImages, $newImages);

                foreach ($deletedImages as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
        });

        static::deleting(function ($model) {
            // Xóa thumbnail
            if ($model->banner) {
                Storage::disk('public')->delete($model->banner);
            }



            // Xóa toàn bộ ảnh trong content
            $contentImages = self::extractImagePaths($model->description ?? '');
            foreach ($contentImages as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        });
    }

    protected static function extractImagePaths($content): array
    {
        // Tìm tất cả URL chứa /storage/products/
        preg_match_all('/https?:\/\/[^"\']+\/storage\/blogs\/[^\s"\'<>]+/', $content, $matches);

        return collect($matches[0])
            ->map(function ($fullUrl) {
                // Lấy phần sau /storage/ để phù hợp với đường dẫn lưu trên disk
                return Str::after($fullUrl, '/storage/');
            })
            ->unique()
            ->toArray();
    }

}
