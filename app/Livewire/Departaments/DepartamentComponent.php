<?php

namespace App\Livewire\Departaments;

use App\Models\Departament;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class DepartamentComponent extends Component
{
    use Interactions, WithPagination;

    public $departamentId;

    public $area;

    public $description;

    public $account_id;

    public $user_id;

    public ?int $quantity = 10;

    public ?string $search = null;

    protected function rules()
    {
        return [
            'area' => 'required|string|max:150',
            'description' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id',
            'user_id' => 'required|exists:users,id|unique:departaments,user_id,'.$this->departamentId,
        ];
    }

    public function with(): array
    {
        $departaments = Departament::query()
            ->with('account', 'user')
            ->when($this->search, function ($query) {
                return $query->where('area', 'like', "%{$this->search}%");
            })
            ->paginate($this->quantity)
            ->withQueryString();

        $rows = $departaments->through(function ($departament) {
            return (object) [
                'id' => $departament->id,
                'area' => $departament->area,
                'description' => $departament->description,
                'account' => $departament->account?->name ?? '',
                'user' => $departament->user?->name ?? '',
                'model' => $departament,
            ];
        });

        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'area', 'label' => 'Área'],
                ['index' => 'description', 'label' => 'Descripción'],
                ['index' => 'account', 'label' => 'Cuenta'],
                ['index' => 'user', 'label' => 'Usuario'],
                ['index' => 'action', 'label' => 'Acciones'],
            ],
            'rows' => $rows,
        ];
    }

    public function render()
    {
        return view('livewire.departaments.departament-component');
    }

    public function store()
    {
        $this->authorize('Crear departamentos');

        $this->validate();

        Departament::create([
            'area' => $this->area,
            'description' => $this->description,
            'account_id' => $this->account_id,
            'user_id' => $this->user_id,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Registro almacenado', 'El departamento se registró correctamente')
            ->send();

        $this->clear();
    }

    #[On('load::departament')]
    public function edit(Departament $departament)
    {
        $this->departamentId = $departament->id;
        $this->area = $departament->area;
        $this->description = $departament->description;
        $this->account_id = $departament->account_id;
        $this->user_id = $departament->user_id;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    public function update()
    {
        $this->authorize('Editar departamentos');

        $this->validate();

        $departament = Departament::find($this->departamentId);

        $departament->update([
            'area' => $this->area,
            'description' => $this->description,
            'account_id' => $this->account_id,
            'user_id' => $this->user_id,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Registro actualizado', 'Datos del departamento actualizados correctamente')
            ->send();

        $this->clear();
    }

    public function delete($id)
    {
        $this->authorize('Eliminar departamentos');

        $id = is_array($id) ? $id[0] : $id;
        $departament = Departament::findOrFail($id);
        $departament->delete();
        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El departamento fue eliminado correctamente')
            ->send();
    }

    public function clear()
    {
        $this->resetValidation();
        $this->dispatch('close-modal');
        $this->reset(['departamentId', 'area', 'description', 'account_id', 'user_id']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
