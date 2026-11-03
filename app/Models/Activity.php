<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    // Nếu bạn muốn chỉ định các trường có thể gán giá trị hàng loạt
    protected $fillable = [
        'view_count',
        'date',
    ];
}