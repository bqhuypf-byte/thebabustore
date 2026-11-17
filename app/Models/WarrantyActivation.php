<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyActivation extends Model
{
    protected $guarded = [];

    protected $casts = ['purchase_date' => 'date', 'expires_at' => 'date', 'activated_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
