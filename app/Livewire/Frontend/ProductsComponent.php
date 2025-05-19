<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsComponent extends Component
{
    use WithPagination;

    public $sortBy = 'newest';
    public $perPage = 12;
    public $category;

    public function mount($category = null)
    {
        $this->category = $category;
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    #[Layout('layouts.frontend')]
    public function render()
    {
        $query = Product::with(['category', 'images', 'prices'])
            ->where('is_active', true);

        if ($this->category) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->category);
            });
        }

        $query->when($this->sortBy === 'newest', function ($q) {
            $q->latest();
        })->when($this->sortBy === 'oldest', function ($q) {
            $q->oldest();
        });

        $products = $query->paginate($this->perPage);

        return view('livewire.frontend.products-component', [
            'products' => $products
        ]);
    }
}
