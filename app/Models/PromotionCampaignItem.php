<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class PromotionCampaignItem extends Model {
    protected $fillable=['product_id','is_active','sort_order','badge'];
    protected $casts=['is_active'=>'boolean','sort_order'=>'integer'];
    public function campaign() { return $this->belongsTo(PromotionCampaign::class,'promotion_campaign_id'); }
    public function product() { return $this->belongsTo(Product::class); }
    public function scopeVisible(Builder $query): Builder {
        return $query->where('is_active',true)->whereHas('product',fn($q)=>$q->where('is_active',true)->whereHas('variants',fn($v)=>$v->where('stock','in_stock')->where(fn($s)=>$s->whereNull('stock_quantity')->orWhere('stock_quantity','>',0))));
    }
}
