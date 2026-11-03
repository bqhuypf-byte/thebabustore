<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Slide;
use App\Models\Blog;
use App\Models\Product;
use App\Models\ProductVariant;

class Optimize extends Component
{
    public $slides;
    public $blogs;
    public $products;
    public $productVariants;
    public function mount()
    {
        $this->slides = Slide::all();
        $this->blogs = Blog::all();
        $this->products = Product::all();
        $this->productVariants = ProductVariant::all();
    }

    public function render()
    {
        return view('livewire.optimize');
    }
}
