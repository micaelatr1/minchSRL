<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class ProductComponent extends Component
{
    use Interactions, WithPagination;

    public ?string $search = null;

    public ?string $categoryFilter = null;

    public function render()
    {
        $products = Product::with('user')
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->categoryFilter, fn (Builder $query) => $query->where('category', $this->categoryFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('livewire.inventory.product-component', compact('products'));
    }

    public function delete(Product $product)
    {
        $this->authorize('Eliminar productos');

        $product->delete();

        $this->toast()
            ->expandable(false)
            ->success('Producto eliminado', 'El producto fue eliminado correctamente')
            ->send();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }
}
