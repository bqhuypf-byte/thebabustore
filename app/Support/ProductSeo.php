<?php
namespace App\Support;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Str;

class ProductSeo
{
    public static function text(?string $html): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags(preg_replace('/<\/?(?:p|div|br|li|h[1-6]|tr|td)\b[^>]*>/i', ' ', $html ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    public static function metadata(Product $product, ?Setting $settings): array
    {
        $sections = collect($product->content_sections ?? [])->filter(fn($s) => $s['visible'] ?? true);
        $sectionText = $sections->map(fn($s) => ($s['body'] ?? '').' '.($s['html'] ?? ''))->implode(' ');
        $legacy = $product->content_standardized_at ? '' : $product->description;
        $description = self::text($product->seo_description ?: $product->short_description ?: $sectionText ?: $legacy);
        return [
            'title' => $product->seo_title ?: $product->name.' | '.($settings?->web_name ?: 'The BABU Store'),
            'description' => Str::limit($description ?: $product->name.' tại The BABU Store.', 160, '…'),
            'canonical' => url('/product/'.$product->slug),
        ];
    }

    public static function schema(Product $product, ?Setting $settings): array
    {
        $product->loadMissing(['variants','brand','category','approvedReviews']);
        $meta = self::metadata($product, $settings);
        $images = collect($product->images ?: [])->merge($product->variants->pluck('image'))->filter()->unique()->map(fn($path) => asset('storage/'.$path))->values()->all();
        $base = ['name'=>$product->name, 'description'=>$meta['description'], 'url'=>$meta['canonical']];
        if ($images) $base['image'] = $images;
        if ($product->brand) $base['brand'] = ['@type'=>'Brand','name'=>$product->brand->name];
        if ($product->category) $base['category'] = $product->category->name;
        if ($product->approvedReviews->isNotEmpty()) {
            $base['aggregateRating'] = ['@type'=>'AggregateRating','ratingValue'=>round($product->approvedReviews->avg('rating'),1),'reviewCount'=>$product->approvedReviews->count()];
        }
        $variants = $product->variants->map(function($variant) use($product,$base,$meta,$settings) {
            $url = $meta['canonical'].'?variant='.$variant->id;
            $data = array_merge($base, ['@type'=>'Product','@id'=>$url.'#product','name'=>$product->name.' — '.$variant->name,'sku'=>$variant->sku,'url'=>$url]);
            unset($data['aggregateRating']);
            if ($variant->image) $data['image'] = [asset('storage/'.$variant->image)];
            if ((float)$variant->price > 0) $data['offers'] = ['@type'=>'Offer','url'=>$url,'priceCurrency'=>'VND','price'=>(string)$variant->price,'availability'=>'https://schema.org/'.($variant->stock === 'in_stock' ? 'InStock' : 'OutOfStock'),'seller'=>['@type'=>'Organization','name'=>$settings?->web_name ?: 'The BABU Store']];
            foreach ($variant->classification ?? [] as $key=>$value) {
                $attribute = Str::lower(Str::ascii($key));
                if (in_array($attribute,['mau','mau sac','color'])) $data['color']=$value;
                else $data['additionalProperty'][]=['@type'=>'PropertyValue','name'=>$key,'value'=>$value];
            }
            return $data;
        })->all();
        if (count($variants)>1) {
            $dimensions = $product->variants->flatMap(fn($v)=>array_keys($v->classification ?: ['Phiên bản'=>$v->name]))->map(fn($key)=>in_array(Str::lower(Str::ascii($key)),['mau','mau sac','color']) ? 'https://schema.org/color' : $key)->unique()->values()->all();
            $data = array_merge($base,['@type'=>'ProductGroup','@id'=>$meta['canonical'].'#product-group','productGroupID'=>(string)$product->id,'variesBy'=>$dimensions,'hasVariant'=>$variants]);
        } else {
            $data = array_merge($base, $variants[0] ?? [], ['@type'=>'Product','@id'=>$meta['canonical'].'#product']);
            if (isset($base['aggregateRating'])) $data['aggregateRating']=$base['aggregateRating'];
        }
        $crumbs=[['@type'=>'ListItem','position'=>1,'name'=>'Trang chủ','item'=>url('/')],['@type'=>'ListItem','position'=>2,'name'=>'Sản phẩm','item'=>url('/shop')],['@type'=>'ListItem','position'=>3,'name'=>$product->name,'item'=>$meta['canonical']]];
        return ['@context'=>'https://schema.org','@graph'=>[$data,['@type'=>'BreadcrumbList','itemListElement'=>$crumbs]]];
    }
}
