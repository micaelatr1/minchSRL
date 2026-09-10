<?php

namespace App\Livewire\Liquidations;

use App\Models\Customer;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class SupplierCooperativeSearch extends Component
{
    #[Modelable]
    public $ci;

    public $searchTerm = '';

    public $filteredCustomers = [];

    public $showList = false;

    public function mount($ci = null)
    {
        $this->ci = $ci;
        $this->searchTerm = $ci;
    }

    public function render()
    {
        return view('livewire.liquidations.supplier-cooperative-search');
    }

    public function updatedSearchTerm()
    {
        $search = trim($this->searchTerm);
        if (strlen($search) < 2) {
            $this->filteredCustomers = [];

            return;
        }

        $this->filteredCustomers = Customer::query()
            ->with(['person', 'cooperative'])
            ->whereHas('person', function ($query) use ($search) {
                $query->where('ci', 'LIKE', "%{$search}%")
                    ->orWhere('full_name', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('cooperative', function ($query) use ($search) {
                $query->where('NIT', 'LIKE', "%{$search}%")
                    ->orWhere('NIM', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();
    }

    public function selectCustomer($id)
    {
        $customer = Customer::with(['person', 'cooperative'])->find($id);
        if (! $customer) {
            return;
        }

        $this->ci = $customer->ci;
        $this->searchTerm = $customer->ci;
        $this->showList = false;

        $this->dispatch('supplier-selected', [
            'id' => $customer->id,
            'full_name' => $customer->full_name,
            'phone' => $customer->phone,
            'ci' => $customer->ci,
            'NIM' => $customer->cooperative?->NIM,
            'NIT' => $customer->cooperative?->NIT,
            'concession' => $customer->cooperative?->concession,
            'mine' => $customer->cooperative?->mine,
            'municipality' => $customer->cooperative?->municipality,
            'name' => $customer->cooperative?->name,
            'contribution' => $customer->cooperative?->contribution,
        ]);
    }

    public function showDropdown()
    {
        $this->showList = true;
    }

    public function hideDropdown()
    {
        usleep(200000);
        $this->showList = false;
        $this->ci = $this->searchTerm;
        $this->dispatch('supplier-ci-manual', ['ci' => $this->ci]);
    }
}
