<?php

namespace App\Livewire\Cooperatives;

use App\Models\Cooperative;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class CooperativeComponent extends Component
{
    use Interactions, WithPagination;

    public $id = 0;

    public $search = '';

    public $quantity = 10;

    public $name = '';

    public $concession = '';

    public $mine = '';

    public $municipality = '';

    public $NIM = '';

    public $NIT = '';

    public $contribution = 0.0;

    public $comibol = 0.0;

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'NIT', 'label' => 'NIT'],
                ['index' => 'NIM', 'label' => 'NIM'],
                ['index' => 'name', 'label' => 'Nombre Completo'],
                ['index' => 'concession', 'label' => 'concession'],
                ['index' => 'mine', 'label' => 'vocamina'],
                ['index' => 'municipality', 'label' => 'municipio'],
                ['index' => 'action'],
            ],
            'rows' => Cooperative::query()
                ->when($this->search, function (Builder $query) {
                    return $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('NIT', 'like', "%{$this->search}%")
                        ->orWhere('NIM', 'like', "%{$this->search}%");
                })
                ->paginate($this->quantity)
                ->withQueryString(),
        ];
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:200',
            'concession' => 'required|string|max:250',
            'mine' => 'required|string|max:250',
            'municipality' => 'required|string|max:150',
            'NIM' => 'required|string|max:200',
            'NIT' => 'required|string|max:200',
            'contribution' => 'required|numeric|gte:0|lte:99.99',
            'comibol' => 'required|numeric|gte:0|lte:99.99',
        ];
    }

    public function render()
    {
        return view('livewire.cooperatives.cooperative-component');
    }

    public function store()
    {
        $this->authorize('Crear cooperativas');

        $this->validate();
        Cooperative::create([
            'name' => $this->name,
            'concession' => $this->concession,
            'mine' => $this->mine,
            'municipality' => $this->municipality,
            'NIM' => $this->NIM,
            'NIT' => $this->NIT,
            'contribution' => $this->contribution,
            'comibol' => $this->comibol,
        ]);
        $this->toast()
            ->expandable(false)
            ->success('Registro almacenado', 'La Cooperativa se registro correctamente')
            ->send();
        $this->clear();
    }

    #[On('load::cooperative')]
    public function edit(Cooperative $cooperative)
    {
        $this->name = $cooperative->name;
        $this->concession = $cooperative->concession;
        $this->mine = $cooperative->mine;
        $this->municipality = $cooperative->municipality;
        $this->NIM = $cooperative->NIM;
        $this->NIT = $cooperative->NIT;
        $this->contribution = $cooperative->contribution;
        $this->comibol = $cooperative->comibol;
        $this->id = $cooperative->id;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    public function update()
    {
        $this->authorize('Editar cooperativas');

        $this->validate();
        $taxe = Cooperative::find($this->id);
        $taxe->update([
            'name' => $this->name,
            'concession' => $this->concession,
            'mine' => $this->mine,
            'municipality' => $this->municipality,
            'NIM' => $this->NIM,
            'NIT' => $this->NIT,
            'contribution' => $this->contribution,
            'comibol' => $this->comibol,
        ]);
        $this->toast()
            ->expandable(false)
            ->success('Registro actualizado', 'Datos de la Cooperativa actualizado correctamente')
            ->send();
        $this->clear();
    }

    public function delete(Cooperative $supplier)
    {
        $this->authorize('Eliminar cooperativas');

        $supplier->delete();
        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'La Cooperativa fue eliminado correctamente')
            ->send();
    }

    public function clear()
    {
        $this->resetValidation();
        $this->dispatch('close-modal');
        $this->reset([
            'name',
            'concession',
            'mine',
            'municipality',
            'NIM',
            'NIT',
            'contribution',
            'comibol',
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
