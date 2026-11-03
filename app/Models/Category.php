<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
    public function product_cat()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
    public function products()
    {
        return $this->hasManyThrough(Product::class, Subcategory::class);
    }
    public function getRandomProducts($limit = 8)
    {
        // Lấy tất cả sản phẩm từ các subcategories
        $products = $this->subcategories->flatMap(function ($subcategory) {
            return $subcategory->products; // Lấy tất cả sản phẩm từ subcategory
        });

        // Trả về 8 sản phẩm ngẫu nhiên
        return $products->shuffle()->take($limit);
    }
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            // Xóa thumbnail nếu thay đổi
            if ($model->isDirty('banner') && $model->getOriginal('banner')) {
                Storage::disk('public')->delete($model->getOriginal('banner'));
            }


        });

        static::deleting(function ($model) {
            // Xóa thumbnail
            if ($model->banner) {
                Storage::disk('public')->delete($model->banner);
            }


        });
    }


}
