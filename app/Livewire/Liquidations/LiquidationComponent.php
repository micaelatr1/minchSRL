<?php

namespace App\Livewire\Liquidations;

use App\Models\Liquidation;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class LiquidationComponent extends Component
{
    use Interactions, WithPagination;

    public ?string $search = null;

    public ?string $metalFilter = null;

    public ?int $quantity = 10;

    public function render()
    {
        $liquidations = Liquidation::with('customer.person')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('lote', 'like', "%{$this->search}%")
                        ->orWhere('full_name', 'like', "%{$this->search}%")
                        ->orWhereHas('customer.person', function ($q) {
                            $q->where('full_name', 'like', "%{$this->search}%")
                                ->orWhere('ci', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->metalFilter, fn ($q) => $q->where('metal', $this->metalFilter))
            ->orderBy('created_at', 'desc')
            ->paginate($this->quantity);

        return view('livewire.liquidations.liquidation-component', compact('liquidations'));
    }

    public function delete(Liquidation $liquidation)
    {
        $this->authorize('Ver liquidaciones');

        $liquidation->delete();

        $this->toast()
            ->success('Liquidación eliminada', 'La liquidación fue eliminada correctamente')
            ->send();
    }
}
