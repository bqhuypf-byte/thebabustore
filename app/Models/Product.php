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
        'images' => 'array',
        'content_sections' => 'array',
        'specification_groups' => 'array',
        'option_group_order' => 'array',
        'content_standardized_at' => 'datetime',
        'seo_research' => 'array',
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

    public function scopeSearch($query, $term)
    {
        $term = trim($term);
        if ($term === '') {
            return $query;
        }

        // Escape % and _ for LIKE query to prevent SQL injection/pattern matching surprises
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term);
        $ascii = Str::ascii($term);
        $escapedAscii = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $ascii);

        return $query->where('is_active', 1)
            ->where(function ($q) use ($escaped, $escapedAscii) {
                $q->where('name', 'like', "%{$escaped}%")
                  ->orWhere('slug', 'like', "%{$escaped}%")
                  ->orWhereHas('brand', function ($b) use ($escaped) {
                      $b->where('name', 'like', "%{$escaped}%");
                  })
                  ->orWhereHas('category', function ($c) use ($escaped) {
                      $c->where('name', 'like', "%{$escaped}%");
                  });

                if ($escapedAscii !== $escaped && $escapedAscii !== '') {
                    $q->orWhere('name', 'like', "%{$escapedAscii}%")
                      ->orWhere('slug', 'like', "%{$escapedAscii}%");
                }
            });
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

                $structuredImages = self::extractImagePaths(json_encode($model->content_sections ?? [], JSON_UNESCAPED_SLASHES).' '.($model->warranty_content ?? ''));
                $deletedImages = array_diff($oldImages, $newImages, $structuredImages);

                foreach ($deletedImages as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
        });

        static::deleting(function ($model) {

            if (\App\Models\OrderItem::whereIn('product_variant_id', $model->variants()->select('id'))->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['product' => 'Sản phẩm đã có đơn hàng. Hãy ngừng bán thay vì xóa để giữ lịch sử.']);
            }

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
