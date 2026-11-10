<?php
namespace App\Console\Commands;

use App\Models\Product;
use App\Support\CatalogNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StandardizeCatalog extends Command
{
    protected $signature='catalog:standardize {--apply : Save the prepared content} {--backup= : Absolute JSON backup file, required when applying}';
    protected $description='Normalize product descriptions and SEO without changing original descriptions, prices, stock or reviews';
    public function handle(): int
    {
        $products=Product::with(['category','variants'])->get();
        $prepared=[]; $backup=[]; $imagesBefore=0; $imagesAfter=0;
        foreach($products as $product) {
            if($product->content_standardized_at) continue;
            $data=CatalogNormalizer::prepare($product);
            $before=preg_match_all('/<img\b/i',$product->description ?? '');
            $after=preg_match_all('/<img\b/i',implode(' ',array_column($data['content_sections'],'html')).' '.($data['warranty_content'] ?? ''));
            if(!$product->content_sections && $before!==$after) {
                $this->error('Image count mismatch for product '.$product->id);
                return self::FAILURE;
            }
            $imagesBefore+=$before; $imagesAfter+=$after;
            $prepared[$product->id]=$data;
            $backup[]=['id'=>$product->id,'name'=>$product->name,'attributes'=>$product->getAttributes()];
        }
        $this->info(json_encode(['products'=>count($prepared),'imagesBefore'=>$imagesBefore,'imagesAfter'=>$imagesAfter],JSON_UNESCAPED_UNICODE));
        if(!$this->option('apply')) return self::SUCCESS;
        $path=$this->option('backup');
        if(!$path || File::exists($path)) { $this->error('Choose a new absolute backup file before applying.'); return self::FAILURE; }
        File::ensureDirectoryExists(dirname($path));
        File::put($path,json_encode(['created_at'=>now()->toIso8601String(),'products'=>$backup],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));
        DB::transaction(function() use($prepared) {
            foreach($prepared as $id=>$data) {
                $product=Product::findOrFail($id);
                $product->fill($data);
                $product->content_standardized_at=now();
                $product->save();
            }
        });
        $this->info('Saved '.count($prepared).' products. Original description fields are preserved; backup: '.$path);
        return self::SUCCESS;
    }
}
