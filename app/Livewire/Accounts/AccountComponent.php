<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class AccountComponent extends Component
{
    use Interactions, WithPagination;

    public $id;

    public $name;

    public $account_number;

    public $currency_type;

    public $balance;

    public $color;

    public $initials;

    public string $department = 'none';

    public ?int $quantity = 10;

    public ?string $search = null;

    public function rules()
    {
        return [
            'name' => ['required', 'min:3', 'max:120', 'string'],
            'account_number' => ['required', 'min:5', 'max:20', Rule::unique('accounts', 'account_number')->ignore($this->id)],
            'initials' => ['nullable', 'min:1', 'max:7'],
            'color' => ['nullable', 'min:3', 'max:15', Rule::unique('accounts', 'color')->ignore($this->id)],
            'currency_type' => ['required', 'in:USD,EUR,BOB'],
            'department' => [
                'nullable',
                Rule::in(['none', 'Human Resources', 'Inventory']),
                Rule::unique('accounts', 'department')->ignore($this->id)->where(fn ($q) => $q->where('department', '!=', 'none')),
            ],
        ];
    }

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Nombre'],
                ['index' => 'account_number', 'label' => 'Número'],
                ['index' => 'currency_type', 'label' => 'Moneda'],
                ['index' => 'department', 'label' => 'Departamento'],
                ['index' => 'action1', 'label' => 'Sigla'],
                ['index' => 'action'],
            ],
            'rows' => Account::query()
                ->when($this->search, function (Builder $query) {
                    return $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('account_number', 'like', "%{$this->search}%");
                })
                ->paginate($this->quantity)
                ->withQueryString(),
        ];
    }

    public function render()
    {
        return view('livewire.accounts.account-component');
    }

    public function delete(Account $account)
    {
        $account->delete();
        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'La cuenta fue eliminada')
            ->send();
    }

    public function store()
    {
        $this->validate();
        $account = Account::create([
            'name' => $this->name,
            'account_number' => $this->account_number,
            'color' => $this->color,
            'initials' => $this->initials,
            'currency_type' => $this->currency_type,
            'department' => $this->department,
        ]);
        $this->toast()
            ->expandable(false)
            ->success('Registro almacenado', 'La cuenta fue registrada correctamente')
            ->send();
        $this->clear();
    }

    public function update()
    {
        $this->validate();
        $taxe = Account::find($this->id);
        $taxe->update([
            'name' => $this->name,
            'account_number' => $this->account_number,
            'color' => $this->color,
            'initials' => $this->initials,
            'currency_type' => $this->currency_type,
            'department' => $this->department,
        ]);
        $this->toast()
            ->expandable(false)
            ->success('Registro actualizado', 'La cuenta fue actualizada')
            ->send();
        $this->clear();
    }

    #[On('load::account')]
    public function edit(Account $account)
    {
        $this->id = $account->id;
        $this->color = $account->color;
        $this->initials = $account->initials;
        $this->name = $account->name;
        $this->account_number = $account->account_number;
        $this->currency_type = $account->currency_type;
        $this->department = $account->department;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    public function clear()
    {
        $this->dispatch('close-modal');
        $this->resetValidation();
        $this->reset(['id', 'name', 'account_number', 'currency_type', 'balance', 'initials', 'department']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
