<?php

namespace App\Livewire\Permissions;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use TallStackUi\Traits\Interactions;

class PermissionComponent extends Component
{
    use Interactions,WithPagination;

    public $role;

    public $search;

    public $selected;

    public function mount()
    {

        $this->role = 0;
        $this->search = '';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $permissions = Permission::where('name', 'like', '%'.$this->search.'%')
            ->select('name', 'description', 'id', DB::raw('0 as checked'))
            ->orderBy('id', 'asc')
            ->paginate(12);

        $roles = Role::select('name', 'id')->whereNot('name', 'Administrador')->orderBy('name')->get();

        if ($this->role) {
            $role = Role::with('permissions:id')->find($this->role);

            $rolePermissionIds = $role->permissions->pluck('id')->toArray();

            foreach ($permissions as $permission) {
                $permission->checked = in_array($permission->id, $rolePermissionIds) ? 1 : 0;
            }
        }
        $options = $roles->map(function ($account) {
            return [
                'label' => $account->name,
                'value' => $account->id,
            ];
        })->toArray();

        return view('livewire.permissions.permission-component', compact('options', 'permissions'));
    }

    public function syncPermission($stated, $id)
    {

        if ($this->role) {
            $roleName = Role::find($this->role);
            if ($stated) {
                $roleName->givePermissionTo($id);
                $this->toast()
                    ->expandable(false)
                    ->success('Permiso  asignado', 'El permiso fue asignado')
                    ->send();
            } else {
                $roleName->revokePermissionTo($id);
                $this->toast()
                    ->expandable(false)
                    ->success('Permiso  revocado', 'El permiso fue revocado')
                    ->send();
            }
        } else {
            return $this->toast()
                ->expandable(false)
                ->error('Rol invalido', 'Seleccione un rol valido')
                ->send();
        }
    }

    public function syncAll()
    {
        /* if($this->role==2){
            return  $this->dispatch('notify', title: 'Error de acceso', icon: 'error', text: 'Acción no autorizada');
        } */
        if (! $this->role) {
            return $this->toast()
                ->expandable(false)
                ->error('Rol invalido', 'Seleccione un rol valido')
                ->send();
        }
        $roleN = Role::find($this->role);
        $permissionsAll = Permission::pluck('id')->toArray();
        $roleN->syncPermissions($permissionsAll);
        $this->toast()
            ->expandable(false)
            ->success('Permisos asignados', 'Los permisos fueron asignados')
            ->send();
    }

    public function revokeAll()
    {
        //        dd($this->role);
        if (! $this->role) {
            return $this->toast()
                ->expandable(false)
                ->error('Rol invalido', 'Seleccione un rol valido')
                ->send();
        }
        $roleN = Role::find($this->role);
        $roleN->syncPermissions([]);
        $this->toast()
            ->expandable(false)
            ->error('Permisos revocados', 'Los permisos fueron revocados')
            ->send();
    }
}
