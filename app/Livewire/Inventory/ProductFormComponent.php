<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class ProductFormComponent extends Component
{
    use Interactions;

    public ?int $id = null;

    public string $name = '';

    public ?string $description = '';

    public string $category = 'materia_prima';

    public string $unit_of_measure = 'kg';

    public bool $is_active = true;

    public function mount(int $id = 0)
    {
        if ($id) {
            $product = Product::findOrFail($id);
            $this->id = $product->id;
            $this->name = $product->name;
            $this->description = $product->description;
            $this->category = $product->category;
            $this->unit_of_measure = $product->unit_of_measure;
            $this->is_active = $product->is_active;
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:materia_prima,insumo,repuesto,combustible,otro'],
            'unit_of_measure' => ['required', 'in:kg,ton,l,u,m'],
            'is_active' => ['boolean'],
        ];
    }

    public function save()
    {
        $this->authorize($this->id ? 'Editar productos' : 'Crear productos');

        $this->validate();

        if ($this->id) {
            $product = Product::findOrFail($this->id);
            $product->update([
                'name' => $this->name,
                'description' => $this->description,
                'category' => $this->category,
                'unit_of_measure' => $this->unit_of_measure,
                'is_active' => $this->is_active,
            ]);

            $this->toast()
                ->expandable(false)
                ->success('Producto actualizado', 'El producto fue actualizado correctamente')
                ->send();
        } else {
            Product::create([
                'name' => $this->name,
                'description' => $this->description,
                'category' => $this->category,
                'unit_of_measure' => $this->unit_of_measure,
                'is_active' => $this->is_active,
                'user_id' => auth()->id(),
            ]);

            $this->toast()
                ->expandable(false)
                ->success('Producto creado', 'El producto fue registrado correctamente')
                ->send();
        }

        return redirect()->route('inventory.products');
    }

    public function render()
    {
        return view('livewire.inventory.product-form-component');
    }
}
