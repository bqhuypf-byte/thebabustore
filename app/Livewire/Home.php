<?php

namespace App\Livewire;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Slide;
use App\Models\Product;
use Livewire\Component;
use App\Jobs\CheckPayment;
class Home extends Component
{
    public $slides;
    public $categories;
    public $latestPosts;
    public $latestProducts; // Biến để lưu sản phẩm mới nhất

    public function mount()
    {
        $this->slides = Slide::get();
        $this->categories = Category::with('subcategories')->get();

        $this->categories->transform(function ($category) {
            $category->random_products = Product::where('category_id', $category->id)
                ->whereHas('variants', function ($query) {
                    $query->where('stock', '!=', 'out_of_stock');
                })
                ->inRandomOrder()
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            return $category;
        });
        $this->latestPosts = Blog::latest()->take(8)->get();
        $this->latestProducts = Product::whereHas('variants', function ($query) {
            $query->where('stock', '!=', 'out_of_stock');
        })
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get(); // Lấy 4 sản phẩm mới nhất còn hàng
    }

    public function render()
    {
        return view('livewire.home', [
            'categories' => $this->categories,
            'latestPosts' => $this->latestPosts,
            'latestProducts' => $this->latestProducts, // Truyền sản phẩm mới nhất vào view
        ]);
    }
}