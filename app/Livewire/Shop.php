<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Subcategory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Shop extends Component
{
    use WithPagination;

    // Khai báo các thuộc tính
    public $selectedPriceRanges = [];
    public $selectedBrands = []; // Thêm thuộc tính để lưu trữ thương hiệu đã chọn
    public $selectedStatuses = []; // Thêm thuộc tính để lưu trữ trạng thái đã chọn
    public $categories = [];
    public $brands = []; // Thêm thuộc tính để lưu trữ danh sách thương hiệu
    public $categories_all = [];
    public $categoryInfo ;

    #[Url(except: '', history: true)]
    public $search = '';

    public $sortField = 'name';
    public $sortDirection = 'asc';

    #[Url(as: 'subcategory', except: '', history: true)]
    public $subcategorySlug = '';

    #[Url(as: 'category', except: '', history: true)]
    public $categorySlug = '';

    public $saleOnly = false;

    #[Url(as: 'price', except: '', history: true)]
    public $priceRange = '';

    #[Url(except: 'featured', history: true)]
    public $sort = 'featured';

    public $subcategoryInfo;

    public function mount()
    {
        $this->categories_all = Category::with('subcategories')->get();
        $this->categories = Category::with('subcategories')->where('show_in_header', true)->get();
        $this->brands = Brand::all();

        $this->saleOnly = $this->sort === 'discount';
        if ($this->subcategorySlug) {
            $this->subcategoryInfo = Subcategory::where('slug', $this->subcategorySlug)->first();
        }
        if ($this->categorySlug) {
            $this->categoryInfo = Category::where('slug', $this->categorySlug)->first();
        }
    }


    public function render()
    {
        $productsQuery = Product::with('variants')
        ->inStock() // chỉ lấy sản phẩm còn hàng
        ->where('name', 'like', "%{$this->search}%")
        ;

        if ($this->sort === 'discount') {
            $productsQuery->where('on_sale', true);
        }

        match ($this->priceRange) {
            'under_500' => $productsQuery->where('price', '<', 500000),
            '500_1000' => $productsQuery->whereBetween('price', [500000, 999999]),
            '1000_2000' => $productsQuery->whereBetween('price', [1000000, 1999999]),
            'over_2000' => $productsQuery->where('price', '>=', 2000000),
            default => null,
        };

        // Lọc theo khoảng giá
        if (!empty($this->selectedPriceRanges)) {
            $productsQuery->where(function($query) {
                foreach ($this->selectedPriceRanges as $range) {
                    $query->orWhereRaw("price $range");
                }
            });
        }

        // Lọc theo thương hiệu
        if (!empty($this->selectedBrands)) {
            $productsQuery->whereIn('brand_id', $this->selectedBrands);
        }

        // Lọc theo trạng thái
        if (!empty($this->selectedStatuses)) {
            $productsQuery->whereIn('status', $this->selectedStatuses); // Thêm điều kiện lọc theo status
        }

        // Các điều kiện lọc khác
        if ($this->subcategorySlug) {
            $productsQuery->whereHas('subcategories', function ($query) {
                $query->where('slug', $this->subcategorySlug);
            });
        }

        if ($this->categorySlug) {
            $productsQuery->whereHas('categories', function ($query) {
                $query->where('slug', $this->categorySlug);
            });
        }

        // Sắp xếp và phân trang
        match ($this->sort) {
            'price-asc' => $productsQuery->orderBy('price'),
            'price-desc' => $productsQuery->orderByDesc('price'),
            default => $productsQuery->orderByDesc('created_at'),
        };
        $products = $productsQuery->paginate(12);


        return view('livewire.store-shop', [
            'products' => $products,
            'categories' => $this->categories,
            'brands' => $this->brands,
            'categories_all' => $this->categories_all,
            'categoryInfo' => $this->categoryInfo,
            'subcategoryInfo' => $this->subcategoryInfo,
        ]);
    }

    public function selectCategory(string $slug = ''): void
    {
        $this->categorySlug = $slug;
        $this->subcategorySlug = '';
        $this->categoryInfo = $slug ? Category::where('slug', $slug)->first() : null;
        $this->subcategoryInfo = null;
        $this->resetPage();
    }

    public function selectCategoryById(int $id): void
    {
        $this->selectCategory(Category::query()->whereKey($id)->value('slug') ?? '');
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedPriceRange(): void { $this->resetPage(); }
    public function updatedSort(): void { $this->resetPage(); }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
}
