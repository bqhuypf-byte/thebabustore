<?php
namespace App\Livewire;
use App\Models\PromotionCampaign;
use App\Models\PromotionCampaignItem;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
class PromotionCampaignPage extends Component {
    use WithPagination, \App\Livewire\Concerns\PurchasesProductCards;
    #[Locked] public int $campaignId;
    #[Url(as:'category',except:'',history:true)] public string $category='';
    public function mount(string $slug='black-friday'): void { $this->campaignId=PromotionCampaign::live()->where('slug',$slug)->firstOrFail()->id; }
    public function updatedCategory(): void { $this->resetPage(); }
    public function render() {
        $campaign=PromotionCampaign::live()->findOrFail($this->campaignId);
        $base=PromotionCampaignItem::query()->where('promotion_campaign_id',$campaign->id)->visible();
        $categories=(clone $base)->with('product.category')->get()->pluck('product.category')->filter()->unique('id')->values();
        $lowestPrice=\App\Models\ProductVariant::query()->whereIn('product_id',(clone $base)->select('product_id'))->where('stock','in_stock')->where(fn($q)=>$q->whereNull('stock_quantity')->orWhere('stock_quantity','>',0))->min('price');
        $items=$base->when($this->category,fn($q)=>$q->whereHas('product.category',fn($c)=>$c->where('slug',$this->category)))->with(['product.variants'=>fn($q)=>$q->orderBy('id'),'product.category'])->orderBy('sort_order')->orderBy('id')->paginate(12);
        return view('livewire.promotion-campaign',['campaign'=>$campaign,'items'=>$items,'categories'=>$categories,'lowestPrice'=>$lowestPrice]);
    }
}
