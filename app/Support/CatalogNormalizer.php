<?php
namespace App\Support;

use App\Models\Product;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;

class CatalogNormalizer
{
    public static function prepare(Product $product): array
    {
        $content = ProductContent::forProduct($product);
        $doc = new DOMDocument('1.0','UTF-8');
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="normalize-root">'.$content['description'].'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $doc->getElementById('normalize-root');
        $sections = [];
        $title = 'Giới thiệu '.$product->name;
        $html = '';
        $warranty = '';
        $flush = function() use (&$sections,&$title,&$html,&$warranty,$product) {
            if (trim(strip_tags($html)) === '' && !str_contains($html,'<img')) { $html=''; return; }
            $key = Str::lower(Str::ascii($title));
            if (preg_match('/^(?:[ivx.0-9 ]+)?(?:chinh sach bao hanh|bao hanh|dieu kien bao hanh|thu tuc bao hanh|chinh sach 1 doi 1)/', $key)) {
                $warranty .= '<h3>'.e(self::title($title)).'</h3>'.$html;
            } else {
                $sections[]=['title'=>Str::limit(self::title($title),200,''),'body'=>'','html'=>$html,'visible'=>true];
            }
            $html='';
        };
        $nodes = [];
        $flatten = function($parent) use (&$flatten,&$nodes) {
            foreach ($parent->childNodes as $node) {
                if ($node instanceof DOMElement && in_array($node->tagName,['div','article','section'])) $flatten($node);
                else $nodes[]=$node;
            }
        };
        if ($root) $flatten($root);
        $imageIndex = 0;
        foreach ($nodes as $nodeIndex => $node) {
            if ($node instanceof DOMElement && preg_match('/^h[1-6]$/',$node->tagName)) {
                $next = null;
                foreach (array_slice($nodes, $nodeIndex + 1) as $candidate) {
                    if (trim($candidate->textContent) !== '' || ($candidate instanceof DOMElement && ($candidate->tagName === 'img' || $candidate->getElementsByTagName('img')->length))) { $next=$candidate; break; }
                }
                if (!$next || ($next instanceof DOMElement && preg_match('/^h[1-6]$/',$next->tagName))) {
                    $html .= '<p>'.e(trim($node->textContent)).'</p>';
                    continue;
                }
                $flush();
                $title=trim($node->textContent);
                continue;
            }
            if ($node instanceof DOMElement) {
                $imgs = $node->tagName === 'img' ? [$node] : iterator_to_array($node->getElementsByTagName('img'));
                foreach ($imgs as $image) {
                    $imageIndex++;
                    if (trim($image->getAttribute('alt'))==='') $image->setAttribute('alt',$product->name.' — hình minh họa '.$imageIndex);
                    $image->setAttribute('loading','lazy');
                }
            }
            $html .= $doc->saveHTML($node);
        }
        $flush();
        if (!$sections) {
            $sections[]=['title'=>'Giới thiệu '.$product->name,'body'=>$product->name.' thuộc danh mục '.($product->category?->name ?: 'sản phẩm').' tại The BABU Store. Xem các phiên bản hiện có và liên hệ cửa hàng để được tư vấn.','html'=>'','visible'=>true];
        }
        $sections = $product->content_sections ?: $sections;
        $summary = self::summary($product,$sections);
        $groups = $product->specification_groups ?: $content['groups'];
        return [
            'content_sections'=>$sections,
            'specification_groups'=>$groups,
            'warranty_content'=>$product->warranty_content ?: $warranty ?: null,
            'seo_title'=>$product->seo_title ?: Str::limit($product->name,70,'…').' | The BABU Store',
            'seo_description'=>$product->seo_description ?: $summary,
            'short_description'=>mb_strlen(ProductSeo::text($product->short_description)) >= 30 ? ProductSeo::text($product->short_description) : $summary,
        ];
    }

    private static function title(string $title): string
    {
        if (mb_strtoupper($title)!==$title) return $title;
        $result = mb_strtolower($title);
        $result = mb_strtoupper(mb_substr($result,0,1)).mb_substr($result,1);
        foreach (['USB','RGB','CNC','PCB','PBT','ABS','LED','LCD','OLED','TFT','IEM','HiFi','KZ','ZSN','EDX','DAC','HMX','NTC','VIA','QMK','FPS','DPI','PAW','WASD','Bluetooth','Pro','Ultra','Max','AKKO','AULA','MCHOSE'] as $term) {
            $result=preg_replace('/\b'.preg_quote(mb_strtolower($term),'/').'\b/iu',$term,$result);
        }
        $result=preg_replace_callback('/\b[a-z]+[0-9]+[a-z0-9]*\b/i',fn($m)=>strtoupper($m[0]),$result);
        return $result;
    }

    private static function summary(Product $product, array $sections): string
    {
        $short = ProductSeo::text($product->short_description);
        if (mb_strlen($short) >= 30) return Str::limit($short,160,'…');
        foreach ($sections as $section) {
            if (!($section['visible'] ?? true)) continue;
            $source = ($section['body'] ?? '').' '.($section['html'] ?? '');
            $paragraphs = preg_split('/<\/?p\b[^>]*>/i',$source);
            foreach ($paragraphs as $paragraph) {
                $text=ProductSeo::text($paragraph);
                if (mb_strlen($text)<60 || str_starts_with($text,'#')) continue;
                return Str::limit($text,160,'…');
            }
        }
        return Str::limit($product->name.'. Xem thông tin, các phiên bản và giá bán tại The BABU Store.',160,'…');
    }
}
