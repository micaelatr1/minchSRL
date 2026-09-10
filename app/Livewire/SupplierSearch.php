<?php

namespace App\Livewire;

use App\Models\Person;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class SupplierSearch extends Component
{
    #[Modelable]
    public $ci = '';

    public $searchTerm = '';

    public $filteredSuppliers = [];

    public $showList = false;

    public function mount($ci = null)
    {
        $this->ci = $ci;
        $this->searchTerm = $ci;
    }

    public function updatedSearchTerm()
    {
        $search = trim($this->searchTerm);
        if (strlen($search) < 2) {
            $this->filteredSuppliers = [];

            return;
        }

        // Buscar en la tabla people (no en suppliers)
        $this->filteredSuppliers = Person::where('ci', 'LIKE', "%{$search}%")
            ->orWhere('full_name', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get(['id', 'ci', 'full_name', 'phone']);
    }

    public function selectSupplier($id)
    {
        $person = Person::find($id);
        if (! $person) {
            return;
        }

        $this->ci = $person->ci;
        $this->searchTerm = $person->ci;
        $this->showList = false;

        // Disparar evento con datos de la persona
        $this->dispatch('supplier-selected', [
            'person_id' => $person->id, // Enviamos el ID de la persona
            'full_name' => $person->full_name,
            'phone' => $person->phone,
            'ci' => $person->ci,
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
        // Si el usuario escribió manualmente y no seleccionó, asignamos el CI
        if (empty($this->ci) && ! empty($this->searchTerm)) {
            $this->ci = $this->searchTerm;
            $this->dispatch('supplier-ci-manual', ['ci' => $this->ci]);
        }
    }

    public function render()
    {
        return view('livewire.supplier-search');
    }
}
