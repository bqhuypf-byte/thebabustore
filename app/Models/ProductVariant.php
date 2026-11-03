<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'status',
        'name',
        'price',
        'discount_price',
        'image',
        'stock',
        'product_id',
        'warranty_days',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            // Xóa thumbnail nếu thay đổi
            if ($model->isDirty('image') && $model->getOriginal('image')) {
                Storage::disk('public')->delete($model->getOriginal('image'));
            }


        });

        static::deleting(function ($model) {
            // Xóa thumbnail
            if ($model->image) {
                Storage::disk('public')->delete($model->image);
            }
        });
    }


}
