<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'images' => 'array'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    public function categories()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function subcategory() {
        return $this->belongsTo(Subcategory::class , 'subcategory_id' );
    }
    public function subcategories() {
        return $this->belongsTo(Subcategory::class , 'subcategory_id' );
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function getApprovedReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    public function getApprovedReviewsAvgAttribute(): float
    {
        return (float) round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function productCount()
    {
        return $this->count();
    }
    public function isOutOfStock()
    {
        // Kiểm tra tất cả các biến thể của sản phẩm
        foreach ($this->variants as $variant) {
            // Kiểm tra nếu có bất kỳ biến thể nào có stock khác 'out_of_stock'
            if ($variant->stock != 'out_of_stock') {
                // Nếu có ít nhất một biến thể còn hàng, trả về false
                return false;
            }
        }

        // Nếu tất cả các biến thể đều hết hàng (stock == 'out_of_stock'), trả về true
        return true;
    }
    public function scopeInStock($query)
{
    return $query->whereHas('variants', function ($q) {
        $q->where('stock', '!=', 'out_of_stock');
    });
}

    // protected static function boot()
    // {
    //     parent::boot();

    //     /** @var Model $model */
    //     static::updating(function ($model) {
    //         if ($model->isDirty('images') && ($model->getOriginal('images') !== null)) {
    //             Storage::disk('public')->delete($model->getOriginal('images'));
    //         }
    //     });
    //     static::deleting(function ($model) {
    //         if ($model->images) {
    //             Storage::disk('public')->delete($model->images);
    //         }
    //     });
    // }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            // Xóa ảnh trong images nếu có thay đổi
            if ($model->isDirty('images')) {
                $oldImages = $model->getOriginal('images') ?? [];
                $newImages = $model->images ?? [];
                $removedImages = array_diff($oldImages, $newImages);
                foreach ($removedImages as $image) {
                    Storage::disk('public')->delete($image);
                }
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

            // Xóa toàn bộ images
            if (is_array($model->images)) {
                foreach ($model->images as $image) {
                    Storage::disk('public')->delete($image);
                }
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
        preg_match_all('/https?:\/\/[^"\']+\/storage\/products\/[^\s"\'<>]+/', $content, $matches);

        return collect($matches[0])
            ->map(function ($fullUrl) {
                // Lấy phần sau /storage/ để phù hợp với đường dẫn lưu trên disk
                return Str::after($fullUrl, '/storage/');
            })
            ->unique()
            ->toArray();
    }

}
