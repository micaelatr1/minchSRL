<?php

namespace App\Livewire\Inventory;

use App\Models\Purchase;
use App\Services\PurchaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class PurchaseComponent extends Component
{
    use Interactions, WithPagination;

    public ?string $search = null;

    public string $dateFrom;

    public string $dateTo;

    public int $quantity = 10;

    public function mount()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $purchases = Purchase::with(['supplier.person', 'items', 'movement', 'user'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('invoice_number', 'like', "%{$this->search}%")
                        ->orWhereHas('supplier.person', fn (Builder $q) => $q->where('full_name', 'like', "%{$this->search}%"));
                });
            })
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->quantity);

        return view('livewire.inventory.purchase-component', compact('purchases'));
    }

    public function delete(Purchase $purchase, PurchaseService $purchaseService)
    {
        $this->authorize('Eliminar entradas inventario');

        $purchaseService->deletePurchase($purchase);

        $this->toast()
            ->expandable(false)
            ->success('Compra eliminada', 'La compra fue eliminada correctamente')
            ->send();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }
}
