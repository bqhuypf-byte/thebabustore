<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductSeoRevision
{
    public const FIELDS = ['name', 'content_sections', 'specification_groups', 'seo_title', 'seo_description', 'seo_research'];
    public const BASELINE = ['name', 'slug', 'images', 'description', 'content_sections', 'specification_groups', 'seo_title', 'seo_description', 'seo_research'];

    /** Recheck stock and concurrent editorial changes while holding row locks. */
    public static function run(array $entry, bool $apply = false, ?callable $backup = null): array
    {
        $id = (int) ($entry['id'] ?? 0);
        if (($entry['hold'] ?? false) === true) return ['id' => $id, 'status' => 'needs_model_confirmation'];
        $changes = $entry['changes'] ?? [];
        if (array_diff(array_keys($changes), self::FIELDS) || array_diff(self::FIELDS, array_keys($changes))) {
            throw new InvalidArgumentException('Revision fields must match the editorial allow-list.');
        }
        foreach (['seo_title' => [50, 60], 'seo_description' => [140, 160]] as $field => [$min, $max]) {
            $length = mb_strlen($changes[$field] ?? '');
            if ($length < $min || $length > $max) throw new InvalidArgumentException("Invalid length for {$field}: {$id}");
        }
        if (array_diff(self::BASELINE, array_keys($entry['expected'] ?? []))) throw new InvalidArgumentException('Complete baseline is required.');
        foreach ($changes['specification_groups'] as $group) {
            foreach ($group['rows'] ?? [] as $row) {
                if (empty($row['source_label']) || ($row['verification_status'] ?? '') !== 'verified') {
                    throw new InvalidArgumentException('Published specifications require a verified source.');
                }
            }
        }
        return DB::transaction(function () use ($id, $entry, $changes, $apply, $backup) {
            $product = Product::whereKey($id)->lockForUpdate()->first();
            if (!$product) return ['id' => $id, 'status' => 'missing'];
            $variants = $product->variants()->lockForUpdate()->get();
            if (!$variants->contains(fn ($v) => $v->stock === 'in_stock')) return ['id' => $id, 'status' => 'out_of_stock'];
            foreach (self::BASELINE as $field) {
                if ($product->getAttribute($field) !== $entry['expected'][$field]) {
                    return ['id' => $id, 'status' => 'changed_since_review', 'field' => $field];
                }
            }
            if (!$apply) return ['id' => $id, 'status' => 'ready'];
            if (!$backup) throw new InvalidArgumentException('A backup writer is required for applying revisions.');
            $backup(['id' => $id, 'product' => $product->getAttributes(), 'variants' => $variants->map->getAttributes()->all()]);
            $product->fill($changes);
            $product->content_standardized_at = now();
            $product->save();
            return ['id' => $id, 'status' => 'applied'];
        });
    }
}
