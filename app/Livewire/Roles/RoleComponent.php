<?php

namespace App\Livewire\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use TallStackUi\Traits\Interactions;

class RoleComponent extends Component
{
    use Interactions;

    public $search;

    public $quantity = 10;

    public $id;

    public $name;

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Nombre'],
                ['index' => 'action', 'label' => 'Operaciones'],
            ],
            'rows' => Role::query()
                ->when($this->search, function (Builder $query) {
                    return $query->where('name', 'like', "%{$this->search}%");
                })
                ->paginate($this->quantity)
                ->withQueryString(),
        ];
    }

    public function rules()
    {
        return [
            'name' => ['required', 'min:4', 'max:150', 'string', Rule::unique('roles', 'name')->ignore($this->id)],
        ];
    }

    public function render()
    {

        return view('livewire.roles.role-component');
    }

    public function store()
    {
        $this->validate();
        Role::create([
            'name' => $this->name,
        ]);
        $this->toast()
            ->expandable(false)
            ->success('Registro agregado', 'El rol fue registrado correctamente')
            ->send();
        $this->clear();
    }

    #[On('load::role')]
    public function edit(Role $role)
    {
        $this->id = $role->id;
        $this->name = $role->name;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    public function delete(Role $rol)
    {
        $rol->delete();
        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El rol fue eliminado correctamente')
            ->send();
        $this->clear();
    }

    public function update()
    {
        $this->validate();
        $rol = Role::find($this->id);
        $rol->update([
            'name' => $this->name,
        ]);
        $this->toast()
            ->expandable(false)
            ->success('Registro actualizado', 'Los datos del rol fueron actualizadas')
            ->send();
        $this->clear();
    }

    public function clear()
    {
        $this->resetValidation();
        $this->reset(['name', 'id']);
        $this->dispatch('close-modal');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
