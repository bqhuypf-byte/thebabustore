<?php
namespace App\Http\Controllers;
use App\Models\Product;
class ProductSitemapController extends Controller
{
    public function __invoke()
    {
        $products = Product::where('is_active',1)->select('slug','updated_at')->orderBy('id')->get();
        return response()->view('seo.product-sitemap', compact('products'))->header('Content-Type','application/xml; charset=UTF-8');
    }
}
