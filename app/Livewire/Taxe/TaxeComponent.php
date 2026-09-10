<?php

namespace App\Livewire\Taxe;

use App\Models\Taxe;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class TaxeComponent extends Component
{
    use Interactions;
    use WithPagination;

    public $id;

    #[Validate('required|min:3|max:150')]
    public $name;

    #[Validate('required|min:3|max:6')]
    public $number;

    #[Validate('required|min:3|max:6')]
    public $initials;

    #[Validate('required|numeric|gte:1.00|lte:99.99')]
    public $applied_discount;

    #[Validate('required|not_in:0')]
    public $type;

    public ?int $quantity = 10;

    public ?string $search = null;

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'name'],
                ['index' => 'initials', 'label' => 'Iniciales'],
                ['index' => 'number', 'label' => 'Numero'],
                ['index' => 'label', 'label' => 'Tipo'],
                ['index' => 'applied_discount', 'label' => 'Porcentaje'],
                ['index' => 'action'],
            ],
            'rows' => Taxe::query()
                ->when($this->search, function (Builder $query) {
                    return $query->where('name', 'like', "%{$this->search}%");
                })
                ->paginate($this->quantity)
                ->withQueryString(),
        ];
    }

    public function render()
    {
        /* $taxes = Taxe::where('name', 'like', '%' . $this->search . '%')->paginate(10); */

        return view('livewire.taxe.taxe-component');
    }

    public function store()
    {
        $this->validate();
        Taxe::create([
            'name' => $this->name,
            'type' => $this->type,
            'number' => $this->number,
            'applied_discount' => $this->applied_discount,
            'initials' => $this->initials,
        ]);
        $this->toast()
            ->expandable(false)
            ->success('Registro almacenado', 'Impuesto registrado')
            ->send();

        $this->clear();
    }

    #[On('load::taxe')]
    public function edit(Taxe $taxe)
    {
        $this->id = $taxe->id;
        $this->name = $taxe->name;
        $this->number = $taxe->number;
        $this->type = $taxe->type;
        $this->applied_discount = $taxe->applied_discount;
        $this->initials = $taxe->initials;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    public function update()
    {
        $this->validate();
        $taxe = Taxe::find($this->id);
        $taxe->update([
            'name' => $this->name,
            'number' => $this->number,
            'type' => $this->type,
            'applied_discount' => $this->applied_discount,
            'initials' => $this->initials,
        ]);
        $this->toast()
            ->expandable(false)
            ->success('Impuesto actualizado', 'Registro modificado')
            ->send();
        $this->clear();
    }

    public function delete(Taxe $taxe)
    {
        $taxe->delete();
        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El impuesto fue eliminado correctamente')
            ->send();
    }

    public function clear()
    {
        $this->resetValidation();
        $this->reset(['id', 'name', 'applied_discount', 'type', 'initials']);
        $this->dispatch('close-modal');

    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
