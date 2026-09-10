<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use App\Models\Supplier;
use App\Services\KardexService;
use Carbon\Carbon;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class KardexComponent extends Component
{
    use Interactions;

    public Product $product;

    public string $dateFrom;

    public string $dateTo;

    public bool $showEntryModal = false;

    public bool $showAdjustmentModal = false;

    // Entry form
    public string $entry_quantity = '';

    public string $entry_unit_cost = '';

    public string $entry_document_number = '';

    public string $entry_supplier_id = '';

    public ?string $entry_description = '';

    // Adjustment form
    public string $adj_quantity = '';

    public string $adj_unit_cost = '';

    public ?string $adj_description = '';

    // Entry consumption modal
    public ?int $ec_movement_id = null;

    public string $ec_quantity = '';

    public string $ec_date = '';

    public ?string $ec_description = '';

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function render(KardexService $kardexService)
    {
        $movements = $kardexService->getKardex(
            $this->product,
            Carbon::parse($this->dateFrom),
            Carbon::parse($this->dateTo)
        );

        return view('livewire.inventory.kardex-component', compact('movements'));
    }

    public function saveEntry(KardexService $kardexService)
    {
        $this->validate([
            'entry_quantity' => 'required|numeric|min:0.01',
            'entry_unit_cost' => 'required|numeric|min:0',
            'entry_document_number' => 'required|string|max:50',
            'entry_supplier_id' => 'required|exists:suppliers,id',
            'entry_description' => 'nullable|string',
        ]);

        $supplier = Supplier::findOrFail($this->entry_supplier_id);
        $account = auth()->user()->departaments()->first()?->account;

        $kardexService->processEntry(
            $this->product,
            (float) $this->entry_quantity,
            (float) $this->entry_unit_cost,
            $this->entry_document_number,
            $supplier,
            $this->entry_description ?? 'Compra '.$this->product->name,
            Carbon::now(),
            auth()->user(),
            $account?->id
        );

        $this->reset(['entry_quantity', 'entry_unit_cost', 'entry_document_number', 'entry_supplier_id', 'entry_description']);
        $this->showEntryModal = false;

        $this->toast()->expandable(false)->success('Ingreso registrado', 'El ingreso fue registrado correctamente')->send();
        $this->dispatch('$refresh');
    }

    public function saveAdjustment(KardexService $kardexService)
    {
        $this->validate([
            'adj_quantity' => 'required|numeric',
            'adj_unit_cost' => 'nullable|numeric|min:0',
            'adj_description' => 'nullable|string',
        ]);

        $qty = (float) $this->adj_quantity;

        if ($qty === 0.0) {
            $this->toast()->expandable(false)->error('Error', 'La cantidad no puede ser cero')->send();

            return;
        }

        try {
            $kardexService->processAdjustment(
                $this->product,
                $qty,
                $qty > 0 ? (float) ($this->adj_unit_cost ?: 0) : null,
                $this->adj_description ?? 'Ajuste '.$this->product->name,
                Carbon::now(),
                auth()->user()
            );

            $this->reset(['adj_quantity', 'adj_unit_cost', 'adj_description']);
            $this->showAdjustmentModal = false;

            $this->toast()->expandable(false)->success('Ajuste registrado', 'El ajuste fue registrado correctamente')->send();
            $this->dispatch('$refresh');
        } catch (\RuntimeException $e) {
            $this->toast()->expandable(false)->error('Error', $e->getMessage())->send();
        }
    }

    public function clear()
    {
        $this->resetValidation();
        $this->dispatch('close-modal');
        $this->reset(['ec_movement_id', 'ec_quantity', 'ec_date', 'ec_description']);
    }

    public function saveEntryConsumption(KardexService $kardexService)
    {
        $this->validate([
            'ec_quantity' => 'required|numeric|min:0.01',
            'ec_date' => 'required|date',
            'ec_description' => 'nullable|string',
        ]);

        $qty = (float) $this->ec_quantity;

        $productKardex = \App\Models\KardexMovement::find($this->ec_movement_id);

        if (! $productKardex) {
            $this->toast()->expandable(false)->error('Error', 'El movimiento seleccionado no existe')->send();

            return;
        }

        try {
            $kardexService->processConsumption(
                $this->product,
                $qty,
                $this->ec_description ?? 'Salida desde entrada #'.$this->ec_movement_id,
                Carbon::parse($this->ec_date),
                auth()->user(),
                (float) $productKardex->unit_cost
            );

            $this->reset(['ec_movement_id', 'ec_quantity', 'ec_date', 'ec_description']);
            $this->dispatch('close-modal');

            $this->toast()->expandable(false)->success('Salida registrada', 'La salida fue registrada correctamente')->send();
            $this->dispatch('$refresh');
        } catch (\RuntimeException $e) {
            $this->toast()->expandable(false)->error('Error', $e->getMessage())->send();
        }
    }
}
