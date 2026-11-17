<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['activation_token_hash'];

    protected $casts = ['purchase_date' => 'date', 'activation_token_expires_at' => 'datetime'];

    public function warrantyActivations()
    {
        return $this->hasMany(WarrantyActivation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
