<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductVariant;

class VariantSelection
{
    public static function map(Product $product): array
    {
        $variants = $product->variants;
        if ($variants->count() < 2) return [];
        $map = [];
        foreach ($variants as $variant) {
            $attributes = collect($variant->classification ?: [])->filter(fn ($value, $key) => is_string($key) && trim($key) !== '' && is_string($value) && trim($value) !== '')->mapWithKeys(fn ($value, $key) => [trim($key) => trim($value)])->all();
            if (!$attributes) {
                // Only infer explicit known colour words; require a unique complete map below.
                $attributes = self::inferAttributes($variant->name, $product);
                if (!$attributes) return [];
            }
            $map[$variant->id] = $attributes;
        }
        $keys = array_keys(reset($map));
        // MySQL may reorder JSON object keys. Prefer familiar selection order unless admin overrides it.
        $priority = ['so phim' => 0, 'kich thuoc' => 0, 'dung luong' => 0, 'dong may' => 0, 'model' => 0, 'phien ban' => 0, 'loai' => 0, 'mau sac' => 1, 'mau' => 1, 'color' => 1, 'switch' => 2, 'cong tac' => 2, 'mic' => 3, 'cap' => 4];
        usort($keys, fn ($a, $b) => ($priority[\Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($a))] ?? 5) <=> ($priority[\Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($b))] ?? 5));
        $configured = array_column($product->option_group_order ?? [], 'name');
        $keys = array_values(array_unique(array_merge(array_intersect($configured, $keys), $keys)));
        if (count($keys) < 1) return [];
        $combinations = [];
        foreach ($map as $attrs) {
            if (count($attrs) !== count($keys) || array_diff($keys, array_keys($attrs))) return [];
            $combination = json_encode(array_map(fn ($key) => $attrs[$key], $keys), JSON_UNESCAPED_UNICODE);
            if (isset($combinations[$combination])) return [];
            $combinations[$combination] = true;
        }
        foreach ($map as $id => $attrs) $map[$id] = array_replace(array_fill_keys($keys, ''), $attrs);
        return $map;
    }

    private static function inferAttributes(string $name, Product $product): array
    {
        // Match colour phrases longest-first, so Rose Red / Black Gold remain one colour.
        $colours = [
            'xanh da trời','xanh dương','xanh lá','xanh đen','trắng xanh','trắng đỏ','cam trắng','xanh trắng','đen polar',
            'đen hồng gradient','đen gradient','đen graphic','trắng graphic','đen vàng','đen đỏ','hồng nhạt','sương mù trắng',
            'rose red','white blue','black red','polar black','polar blue','sky blue','black gold','blue gradient','black gradient',
            'red metalic','đỏ metalic','berry red','steel black','ice white','white gold','light pink','pastel pink','lake green',
            'black pink','white graphic','mint','cheese','crystal','cyan','rose','snow','retro',
            'trắng','đen','đỏ','cam','hồng','tím','vàng','bạc','xám','xanh','nâu',
            'gray','grey','purple','blue','black','golden','gold','silver','white','green','pink','orange','red',
        ];
        usort($colours, fn($a,$b) => mb_strlen($b) <=> mb_strlen($a));
        $pattern = implode('|', array_map(fn($value) => preg_quote($value, '/'), $colours));
        $name = preg_replace('/\s+/u', ' ', trim($name));
        $name = preg_replace('/^màu\s+/iu', '', $name);
        // A colour-only variant inherits its actual product's model name, never an invented version.
        if (preg_match('/^('.$pattern.')$/iu', $name, $match)) {
            return ['Phiên bản' => $product->name, 'Màu sắc' => self::colourLabel($match[1])];
        }
        if (preg_match('/^('.$pattern.')(?:\s*-\s*|\s+)(.+)$/iu', $name, $match)) {
            $model = self::versionLabel(trim($match[2]));
            return ['Phiên bản' => $model, 'Màu sắc' => self::colourLabel($match[1])];
        }
        if (preg_match('/^(.+?)(?:\s*-\s*|\s+)('.$pattern.')$/iu', $name, $match)) {
            return ['Phiên bản' => self::versionLabel(trim($match[1])), 'Màu sắc' => self::colourLabel($match[2])];
        }
        return [];
    }

    private static function versionLabel(string $value): string
    {
        $key = mb_strtolower(preg_replace('/\s*-\s*/u', '-', trim($value)));
        $switchLabels = ['linear'=>'Linear', 'tactile'=>'Tactile', 'clicky'=>'Clicky', 'clikcy'=>'Clicky', 'silent-linear'=>'Silent Linear', 'silent-tactile'=>'Silent Tactile'];
        return $switchLabels[$key] ?? preg_replace('/\bpro\b/iu', 'Pro', trim($value));
    }

    private static function colourLabel(string $value): string
    {
        return mb_strtoupper(mb_substr($value, 0, 1)).mb_strtolower(mb_substr($value, 1));
    }

    public static function select(Product $product, $selectedVariant, string $group, string $value): ?ProductVariant
    {
        $map = self::map($product);
        if (!$map || !in_array($group, array_keys(reset($map)), true)) return null;
        $current = $map[$selectedVariant] ?? [];
        $keys = array_keys(reset($map));
        $prior = array_slice($keys, 0, array_search($group, $keys, true));
        return $product->variants->filter(function ($variant) use ($map, $group, $value, $prior, $current) {
            if ($variant->stock !== 'in_stock' || ($map[$variant->id][$group] ?? null) !== $value) return false;
            foreach ($prior as $key) if (($map[$variant->id][$key] ?? null) !== ($current[$key] ?? null)) return false;
            return true;
        })->sortByDesc(function ($variant) use ($current, $map, $group) {
            $score = 0;
            foreach ($current as $key => $selected) if ($key !== $group && ($map[$variant->id][$key] ?? null) === $selected) $score++;
            return $score;
        })->first();
    }

    public static function groups(Product $product, $selectedVariant): array
    {
        $map = self::map($product);
        if (!$map) return [];
        $fallbackImage = collect($product->images ?: [])->filter()->first();
        $current = $map[$selectedVariant] ?? reset($map);
        $groups = [];
        $prior = [];
        foreach (array_keys(reset($map)) as $key) {
            $eligible = $product->variants->filter(function ($variant) use ($map, $prior, $current) {
                foreach ($prior as $previous) if (($map[$variant->id][$previous] ?? null) !== ($current[$previous] ?? null)) return false;
                return true;
            });
            $values = [];
            foreach ($product->variants as $variant) {
                $value = $map[$variant->id][$key];
                if (!isset($values[$value])) {
                    $values[$value] = [
                        'label' => $value, 'image' => $variant->image ?: $fallbackImage,
                        'price' => $variant->price, 'available' => false, 'compatible' => false,
                    ];
                }
                if (!$eligible->contains('id', $variant->id)) continue;
                $values[$value]['compatible'] = true;
                if ($variant->stock === 'in_stock') {
                    if (!$values[$value]['available'] || $variant->price < $values[$value]['price']) $values[$value]['price'] = $variant->price;
                    $values[$value]['available'] = true;
                    $values[$value]['image'] = $variant->image ?: $values[$value]['image'] ?: $fallbackImage;
                }
            }
            $groups[] = ['name' => $key, 'selected' => $current[$key] ?? '', 'options' => array_values($values)];
            $prior[] = $key;
        }
        return $groups;
    }

}
