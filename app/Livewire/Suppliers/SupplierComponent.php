<?php

// app/Livewire/Suppliers/SupplierComponent.php

namespace App\Livewire\Suppliers;

use App\Models\Person;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class SupplierComponent extends Component
{
    use Interactions, WithPagination;

    public $supplierId;          // ID del proveedor en edición

    public $full_name;

    public $ci;

    public $description;         // Nuevo campo

    public ?int $quantity = 10;

    public ?string $search = null;

    // Reglas de validación
    protected function rules()
    {
        return [
            'full_name' => 'required|min:5|max:150',
            'ci' => [
                'required',
                'min:4',
                'max:15',
                Rule::unique('people', 'ci')->ignore($this->supplierId, 'id'), // Ignora el CI de la persona actual
            ],
            'description' => 'required|max:255',
        ];
    }

    // Datos para la tabla
    public function with(): array
    {
        $suppliers = Supplier::query()
            ->with('person') // Eager loading
            ->when($this->search, function (Builder $query) {
                return $query->whereHas('person', function ($q) {
                    $q->where('full_name', 'like', "%{$this->search}%")
                        ->orWhere('ci', 'like', "%{$this->search}%");
                });
            })
            ->paginate($this->quantity)
            ->withQueryString();

        // Transformamos cada Supplier en un objeto con las propiedades necesarias
        $rows = $suppliers->through(function ($supplier) {
            return (object) [
                'id' => $supplier->id,
                'ci' => $supplier->person->ci ?? '',
                'full_name' => $supplier->person->full_name ?? '',
                'description' => $supplier->description,
                // Guardamos el modelo original para usarlo en acciones (eliminar, editar)
                'model' => $supplier,
            ];
        });

        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'ci', 'label' => 'CI'],
                ['index' => 'full_name', 'label' => 'Nombre Completo'],
                ['index' => 'description', 'label' => 'Descripción'],
                ['index' => 'action', 'label' => 'Acciones'],
            ],
            'rows' => $rows,
        ];
    }

    public function render()
    {
        return view('livewire.suppliers.supplier-component');
    }

    // Crear nuevo proveedor
    public function store()
    {
        $this->authorize('Crear proveedores');

        $this->validate();

        // Crear la persona
        $person = Person::create([
            'full_name' => $this->full_name,
            'ci' => $this->ci,
        ]);

        // Crear el proveedor asociado
        Supplier::create([
            'description' => $this->description,
            'person_id' => $person->id,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Registro almacenado', 'El proveedor se registró correctamente')
            ->send();

        $this->clear();
    }

    // Cargar datos para editar
    #[On('load::supplier')]
    public function edit(Supplier $supplier)
    {
        $this->supplierId = $supplier->id;
        $this->description = $supplier->description;
        $this->full_name = $supplier->person->full_name;
        $this->ci = $supplier->person->ci;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    // Actualizar proveedor
    public function update()
    {
        $this->authorize('Editar proveedores');

        $this->validate();

        $supplier = Supplier::with('person')->find($this->supplierId);

        // Actualizar persona
        $supplier->person->update([
            'full_name' => $this->full_name,
            'ci' => $this->ci,
        ]);

        // Actualizar proveedor
        $supplier->update([
            'description' => $this->description,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Registro actualizado', 'Datos del proveedor actualizados correctamente')
            ->send();

        $this->clear();
    }

    // Eliminar proveedor (incluye la persona en cascada por la FK)
    public function delete(Supplier $supplier)
    {
        $this->authorize('Eliminar proveedores');

        $supplier->delete(); // Como la FK tiene onDelete('cascade'), elimina la persona también
        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El proveedor fue eliminado correctamente')
            ->send();
    }

    // Limpiar formulario y cerrar modal
    public function clear()
    {
        $this->resetValidation();
        $this->dispatch('close-modal');
        $this->reset(['supplierId', 'full_name', 'ci', 'description']);
    }

    // Reiniciar paginación al buscar
    public function updatedSearch()
    {
        $this->resetPage();
    }
}
