<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class PromotionCampaign extends Model {
    protected $fillable=['name','slug','headline','intro','terms','banner_image','mobile_banner_image','product_banner_image','is_active','show_product_banner','priority','starts_at','ends_at'];
    protected $casts=['is_active'=>'boolean','show_product_banner'=>'boolean','priority'=>'integer','starts_at'=>'datetime','ends_at'=>'datetime'];
    public function items() { return $this->hasMany(PromotionCampaignItem::class)->orderBy('sort_order')->orderBy('id'); }
    public function scopeLive(Builder $query): Builder {
        return $query->where('is_active',true)->where(fn($q)=>$q->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>',now()));
    }
    public function publicUrl(): string { return $this->slug==='black-friday' ? url('/black-friday') : url('/khuyen-mai/'.$this->slug); }
    public static function productBanner(): ?self { return static::live()->where('show_product_banner',true)->whereNotNull('product_banner_image')->where('product_banner_image','!=','')->orderByDesc('priority')->orderByDesc('id')->first(); }
}
