<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class CustomerComponent extends Component
{
    use Interactions, WithFileUploads, WithPagination;

    public $customerId;

    public $full_name;

    public $ci;

    public $file;

    public $existingFile;

    public $cooperative_id;

    public ?int $quantity = 10;

    public ?string $search = null;

    protected function rules()
    {
        return [
            'full_name' => 'required|min:5|max:150',
            'ci' => [
                'required',
                'min:4',
                'max:15',
                Rule::unique('people', 'ci')->ignore($this->customerId, 'id'),
            ],
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'cooperative_id' => 'nullable|exists:cooperatives,id',
        ];
    }

    public function with(): array
    {
        $customers = Customer::query()
            ->with('person', 'cooperative')
            ->when($this->search, function (Builder $query) {
                return $query->whereHas('person', function ($q) {
                    $q->where('full_name', 'like', "%{$this->search}%")
                        ->orWhere('ci', 'like', "%{$this->search}%");
                });
            })
            ->paginate($this->quantity)
            ->withQueryString();

        $rows = $customers->through(function ($customer) {
            return (object) [
                'id' => $customer->id,
                'ci' => $customer->person->ci ?? '',
                'full_name' => $customer->person->full_name ?? '',
                'file' => $customer->file ? basename($customer->file) : '',
                'file_path' => $customer->file ? \Storage::url($customer->file) : null,
                'cooperative' => $customer->cooperative?->name ?? '',
                'model' => $customer,
            ];
        });

        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'ci', 'label' => 'CI'],
                ['index' => 'full_name', 'label' => 'Nombre Completo'],
                ['index' => 'file', 'label' => 'Archivo'],
                ['index' => 'cooperative', 'label' => 'Cooperativa'],
                ['index' => 'action', 'label' => 'Acciones'],
            ],
            'rows' => $rows,
        ];
    }

    public function render()
    {
        return view('livewire.customers.customer-component');
    }

    public function store()
    {
        $this->authorize('Crear clientes');

        $this->validate();

        $person = Person::create([
            'full_name' => $this->full_name,
            'ci' => $this->ci,
        ]);

        $path = $this->file ? $this->file->store('customer/file', 'public') : null;

        Customer::create([
            'file' => $path,
            'cooperative_id' => $this->cooperative_id,
            'person_id' => $person->id,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Registro almacenado', 'El cliente se registró correctamente')
            ->send();

        $this->clear();
    }

    #[On('load::customer')]
    public function edit(Customer $customer)
    {
        $this->customerId = $customer->id;
        $this->existingFile = $customer->file;
        $this->file = null;
        $this->cooperative_id = $customer->cooperative_id;
        $this->full_name = $customer->person->full_name;
        $this->ci = $customer->person->ci;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    public function update()
    {
        $this->authorize('Editar clientes');

        $this->validate();

        $customer = Customer::with('person')->find($this->customerId);

        $customer->person->update([
            'full_name' => $this->full_name,
            'ci' => $this->ci,
        ]);

        $path = $customer->file;
        if ($this->file) {
            if ($customer->file) {
                \Storage::disk('public')->delete($customer->file);
            }
            $path = $this->file->store('customer/file', 'public');
        }

        $customer->update([
            'file' => $path,
            'cooperative_id' => $this->cooperative_id,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Registro actualizado', 'Datos del cliente actualizados correctamente')
            ->send();

        $this->clear();
    }

    public function delete($id)
    {
        $this->authorize('Eliminar clientes');

        $id = is_array($id) ? $id[0] : $id;
        $customer = Customer::findOrFail($id);
        if ($customer->file) {
            \Storage::disk('public')->delete($customer->file);
        }
        $customer->delete();
        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El cliente fue eliminado correctamente')
            ->send();
    }

    public function clear()
    {
        $this->resetValidation();
        $this->dispatch('close-modal');
        $this->reset(['customerId', 'full_name', 'ci', 'file', 'existingFile', 'cooperative_id']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
