<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'hotline',
        'support_hours',
        'email',
        'website',
        'copyright',
        'logo',
        'logo_light',
        'purchase_terms',
        'purchase_guide',
        'payment_guide',
        'shipping_policy',
        'warranty_policy',
        'privacy_policy',
        'return_policy',
        'introduction',
        'contact',
        'recruitment',
        'so_tai_khoan',
        'ngan_hang',
        'facebook',
        'messenger',
        'youtube',
        'shopee',
        'zalo',
        'web_icon',
        'company_description',
        'ministry_of_industry_and_trade_link',
        'map',
        'web_name',
        'description_web',
        'banner_web',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            // Xóa thumbnail nếu thay đổi
            if ($model->isDirty('logo') && $model->getOriginal('logo')) {
                Storage::disk('public')->delete($model->getOriginal('logo'));
            }
            if ($model->isDirty('logo_light') && $model->getOriginal('logo_light')) {
                Storage::disk('public')->delete($model->getOriginal('logo_light'));
            }
            if ($model->isDirty('web_icon') && $model->getOriginal('web_icon')) {
                Storage::disk('public')->delete($model->getOriginal('web_icon'));
            }
            if ($model->isDirty('banner_web') && $model->getOriginal('banner_web')) {
                Storage::disk('public')->delete($model->getOriginal('banner_web'));
            }

        });

        static::deleting(function ($model) {
            // Xóa thumbnail
            if ($model->logo) {
                Storage::disk('public')->delete($model->logo);
            }
            if ($model->logo_light) {
                Storage::disk('public')->delete($model->logo_light);
            }
            if ($model->web_icon) {
                Storage::disk('public')->delete($model->web_icon);
            }
            if ($model->banner_web) {
                Storage::disk('public')->delete($model->banner_web);
            }
        });
    }


}
