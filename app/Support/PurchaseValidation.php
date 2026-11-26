<?php
namespace App\Support;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Helpers\CartManagement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseValidation
{
    public static function available(ProductVariant $variant): bool
    {
        return $variant->stock === 'in_stock' && ($variant->stock_quantity === null || (int) $variant->stock_quantity > 0);
    }
    public static function limit(?ProductVariant $variant): int
    {
        return $variant && self::available($variant) ? max(0, (int) ($variant->stock_quantity ?? 99)) : 0;
    }
    public static function clamp($quantity, ?ProductVariant $variant): int
    {
        return max(1, min(max(1, self::limit($variant)), (int) $quantity));
    }
    public static function map(Product $product): array
    {
        $map = VariantSelection::map($product);
        if ($map) return $map;
        return $product->variants->mapWithKeys(fn ($v) => [$v->id => ['Phiên bản' => (string)$v->id]])->all();
    }
    public static function resolve(Product $product, array $selected): ?ProductVariant
    {
        foreach (self::map($product) as $id => $attrs) {
            if (count($selected) === count($attrs) && !array_diff_assoc($attrs, $selected)) return $product->variants->firstWhere('id', $id);
        }
        return null;
    }
    public static function groups(Product $product, array $selected): array
    {
        $map = self::map($product);
        if (!$map) return [];
        $groups=[]; $prior=[];
        foreach (array_keys(reset($map)) as $name) {
            $values=[];
            foreach ($product->variants as $v) {
                $label=$map[$v->id][$name];
                $values[$label] ??= ['label'=>$label,'image'=>$v->image ?: (($product->images ?: [])[0] ?? null),'price'=>$v->price,'available'=>false,'compatible'=>false];
                $compatible=true;
                foreach ($prior as $key) if (isset($selected[$key]) && $map[$v->id][$key] !== $selected[$key]) $compatible=false;
                if (!$compatible) continue;
                $values[$label]['compatible']=true;
                if (self::available($v)) { $values[$label]['available']=true; $values[$label]['image']=$v->image ?: $values[$label]['image']; }
            }
            $groups[]=['name'=>$name,'selected'=>$selected[$name] ?? '', 'options'=>array_values($values)];
            $prior[]=$name;
        }
        return $groups;
    }
    public static function choose(Product $product, array $selected, string $group, string $value): array
    {
        $groups=self::groups($product,$selected);
        $option=collect(collect($groups)->firstWhere('name',$group)['options'] ?? [])->firstWhere('label',$value);
        if (!$option || !$option['available']) return $selected;
        $selected[$group]=$value;
        // Retain explicit compatible choices; never fill another group automatically.
        $kept=[];
        foreach ($groups as $g) {
            $name=$g['name'];
            if (!isset($selected[$name])) continue;
            $candidate=$kept + [$name=>$selected[$name]];
            $exists=$product->variants->contains(function ($v) use ($product,$candidate) {
                return self::available($v) && !array_diff_assoc($candidate,self::map($product)[$v->id]);
            });
            if ($exists) $kept=$candidate;
        }
        return $kept;
    }
    public static function message(Product $product, array $selected): ?string
    {
        $map=self::map($product);
        if (!$map || !$product->variants->contains(fn($v)=>self::available($v))) return 'Hết hàng';
        foreach (array_keys(reset($map)) as $name) if (!isset($selected[$name]) || $selected[$name] === '') return 'Vui lòng chọn '.$name;
        $variant=self::resolve($product,$selected);
        return $variant && self::available($variant) ? null : 'Hết hàng';
    }
    public static function add(int $productId, $variantId, array $selected, $quantity, string $token): int
    {
        $session=session()->getId();
        return Cache::lock('purchase-session:'.hash('sha256',$session),10)->block(5,function () use ($productId,$variantId,$selected,$quantity,$token,$session) {
            $receipt='purchase-receipt:'.hash('sha256',$session.':'.$token);
            if (Cache::has($receipt)) return count(CartManagement::getRawCartItemsFromSession());
            return DB::transaction(function () use ($productId,$variantId,$selected,$quantity,$receipt) {
                $product=Product::where('is_active',true)->lockForUpdate()->find($productId);
                if (!$product) throw ValidationException::withMessages(['purchase'=>'Sản phẩm không còn khả dụng.']);
                $product->setRelation('variants',ProductVariant::where('product_id',$productId)->lockForUpdate()->get());
                if ($message=self::message($product,$selected)) throw ValidationException::withMessages(['purchase'=>$message]);
                $variant=self::resolve($product,$selected);
                if (!$variant || (int)$variantId !== (int)$variant->id) throw ValidationException::withMessages(['purchase'=>'Vui lòng chọn phiên bản.']);
                $qty=filter_var($quantity,FILTER_VALIDATE_INT);
                if ($qty === false || $qty < 1) throw ValidationException::withMessages(['purchase'=>'Số lượng phải ít nhất là 1.']);
                $existing=collect(CartManagement::getRawCartItemsFromSession())->firstWhere('product_variant_id',$variant->id)['quantity'] ?? 0;
                if ($qty + $existing > self::limit($variant)) throw ValidationException::withMessages(['purchase'=>'Số lượng vượt quá số lượng có thể đặt.']);
                $count=CartManagement::addItemToCart($variant->id,$qty);
                Cache::put($receipt,true,300);
                return $count;
            });
        });
    }
}
