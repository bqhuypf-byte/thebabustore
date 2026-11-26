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
        'stock_quantity',
        'product_id',
        'warranty_days',
        'classification',
        'specifications',
    ];

    protected $casts = ['stock_quantity' => 'integer', 'classification' => 'array', 'specifications' => 'array'];

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
            if (\App\Models\OrderItem::where('product_variant_id', $model->id)->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['variants' => 'Biến thể đã có đơn hàng. Hãy chuyển sang hết hàng thay vì xóa để giữ lịch sử.']);
            }
            // Xóa thumbnail
            if ($model->image) {
                Storage::disk('public')->delete($model->image);
            }
        });
    }


}
