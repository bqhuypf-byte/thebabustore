<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchPalette extends Component
{
    public bool $isOpen = false;
    public string $query = '';
    public int $selectedIndex = 0;

    #[On('open-search-palette')]
    public function open(): void
    {
        $this->isOpen = true;
        $this->query = '';
        $this->selectedIndex = 0;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->query = '';
    }

    public function selectNext(int $total): void
    {
        if ($total <= 0) return;
        $this->selectedIndex = ($this->selectedIndex + 1) % $total;
    }

    public function selectPrev(int $total): void
    {
        if ($total <= 0) return;
        $this->selectedIndex = ($this->selectedIndex - 1 + $total) % $total;
    }

    public function render()
    {
        $products = collect();
        $categories = collect();

        $trimmed = trim($this->query);
        if (mb_strlen($trimmed) >= 2) {
            $products = Product::search($trimmed)
                ->with(['category', 'variants'])
                ->take(8)
                ->get();

            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $trimmed);
            $categories = Category::query()
                ->where('is_active', 1)
                ->where('name', 'like', "%{$escaped}%")
                ->take(4)
                ->get();
        }

        return view('livewire.search-palette', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}

