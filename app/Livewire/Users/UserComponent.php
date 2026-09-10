<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use TallStackUi\Traits\Interactions;

class UserComponent extends Component
{
    use Interactions, WithPagination;

    public $search;

    public $id;

    public $quantity;

    public $name;

    public $last_name;

    public $ci;

    public $birthdate;

    public $gender;

    public $phone;

    public $email;

    public $rols = [];

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'nombre'],
                ['index' => 'last_name', 'label' => 'apellido'],
                ['index' => 'birthdate', 'label' => 'fecha'],
                ['index' => 'gender', 'label' => 'genero'],
                ['index' => 'phone', 'label' => 'telefono'],
                ['index' => 'email', 'label' => 'email'],
                ['index' => 'roles', 'label' => 'roles'],
                ['index' => 'action', 'label' => 'Operaciones'],
            ],
            'rows' => User::query()
                ->with('roles')
                ->when($this->search, function (Builder $query) {
                    return $query->where('name', 'like', "%{$this->search}%");
                })
                ->paginate($this->quantity)
                ->withQueryString(),
        ];
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'ci' => ['required', 'string', 'max:15', Rule::unique('users', 'ci')->ignore($this->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->id)],
            'phone' => 'required|string|max:10',
            'gender' => 'required|in:M,F',
            'birthdate' => 'required|date',
        ];
    }

    public function render()
    {
        $roles = Role::select('name', 'id')->whereNot('name', 'Administrador')->orderBy('name')->get();
        $options = $roles->map(function ($account) {
            return [
                'label' => $account->name,
                'value' => $account->id,
            ];
        })->toArray();

        return view('livewire.users.user-component', compact('options'));
    }

    public function store()
    {
        $this->validate();
        $user = User::create([
            'name' => $this->name,
            'last_name' => $this->last_name,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'ci' => $this->ci,
            'phone' => $this->phone,
            'email' => $this->email,
            'password' => Hash::make($this->ci),
        ]);
        $user->syncRoles($this->rols);
        $this->toast()
            ->expandable(false)
            ->success('Registro agregado', 'El usuario fue registrado correctamente')
            ->send();
        $this->clear();
    }

    public function update()
    {
        $this->validate();
        $user = User::find($this->id, 'id');
        $user->update([
            'name' => $this->name,
            'last_name' => $this->last_name,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'ci' => $this->ci,
            'phone' => $this->phone,
            'email' => $this->email,
        ]);
        $user->syncRoles($this->rols);
        $this->toast()
            ->expandable(false)
            ->success('Registro agregado', 'El usuario fue registrado correctamente')
            ->send();
        $this->clear();
    }

    #[On('load::user')]
    public function edit(User $user)
    {
        $this->name = $user->name;
        $this->last_name = $user->last_name;
        $this->birthdate = $user->birthdate;
        $this->gender = $user->gender;
        $this->ci = $user->ci;
        $this->phone = $user->phone;
        $this->email = $user->email;
        $this->id = $user->id;
        $this->rols = $user->roles->pluck('id')->toArray();
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    public function delete(User $user)
    {
        $user->delete();
        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El usuario fue eliminado correctamente')
            ->send();
        $this->clear();
    }

    public function clear()
    {
        $this->dispatch('close-modal');
        $this->resetValidation();
        $this->reset(['name', 'last_name', 'birthdate', 'phone', 'email', 'gender', 'id', 'ci', 'rols']);
    }
}
