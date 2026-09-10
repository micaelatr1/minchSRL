<?php

namespace App\Livewire\Contracts;

use App\Models\Contract;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class ContractComponent extends Component
{
    use Interactions, WithPagination;

    public ?string $search = null;

    public ?string $statusFilter = null;

    public ?int $quantity = 9;

    public function render()
    {
        $contracts = Contract::with('person')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                        ->orWhereHas('person', function ($q) {
                            $q->where('full_name', 'like', "%{$this->search}%")
                                ->orWhere('ci', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('created_at', 'desc')
            ->paginate($this->quantity);

        return view('livewire.contracts.contract-component', compact('contracts'));
    }

    public function delete(Contract $contract)
    {
        $this->authorize('Eliminar contratos');

        $contract->delete();

        $this->toast()
            ->success('Contrato eliminado', 'El contrato fue eliminado correctamente')
            ->send();
    }
}
