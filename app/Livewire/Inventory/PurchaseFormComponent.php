<?php

namespace App\Livewire\Inventory;

use App\Models\Account;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PersonSupplierService;
use App\Services\PurchaseService;
use Carbon\Carbon;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class PurchaseFormComponent extends Component
{
    use Interactions;

    public ?int $id = null;

    public string $date;

    public ?string $invoice_number = '';

    public string $ci = '';

    public ?int $supplier_id = null;

    public ?string $supplier_name = '';

    public ?string $supplier_phone = '';

    public array $items = [];

    public string $payment_type = 'T';

    public ?string $number_check = '';

    public ?string $description = '';

    public function mount(int $id = 0)
    {
        $this->date = now()->format('Y-m-d');

        if ($id) {
            $purchase = Purchase::with(['items.product', 'supplier.person', 'movement.transaction'])->findOrFail($id);
            $this->id = $purchase->id;
            $this->date = $purchase->date->format('Y-m-d');
            $this->invoice_number = $purchase->invoice_number;
            $this->supplier_id = $purchase->supplier_id;
            $this->supplier_name = $purchase->supplier?->full_name;
            $this->supplier_phone = $purchase->supplier?->phone;
            $this->ci = $purchase->supplier?->ci;

            if ($purchase->movement) {
                $this->payment_type = $purchase->movement->transaction?->payment_type ?? 'T';
                $this->number_check = $purchase->movement->transaction?->number_check;
                $this->description = $purchase->movement->description;
            }

            foreach ($purchase->items as $item) {
                $this->items[] = [
                    '_key' => 'item_'.$item->id,
                    'product_id' => (string) $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => (string) $item->quantity,
                    'unit_cost' => (string) $item->unit_cost,
                ];
            }
        }
    }

    public function render()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('livewire.inventory.purchase-form-component', compact('products'));
    }

    public function getTotalAmountProperty(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $cost = (float) ($item['unit_cost'] ?? 0);
            $total += $qty * $cost;
        }

        return $total;
    }

    public function save(PurchaseService $purchaseService)
    {
        if (! $this->supplier_id && $this->ci && $this->supplier_name) {
            $supplier = app(PersonSupplierService::class)->resolve(
                $this->ci,
                $this->supplier_name,
                $this->supplier_phone ?: null,
            );
            $this->supplier_id = $supplier->id;
        }

        $this->validate();

        $date = Carbon::parse($this->date);
        $supplier = Supplier::with('person')->findOrFail($this->supplier_id);

        $items = [];
        foreach ($this->items as $item) {
            $items[] = [
                'product_id' => (int) $item['product_id'],
                'quantity' => (float) $item['quantity'],
                'unit_cost' => (float) $item['unit_cost'],
            ];
        }

        $account = Account::where('department', 'Inventory')->first();

        if (! $account) {
            $this->toast()
                ->expandable(false)
                ->error('Cuenta no configurada', 'No existe una cuenta bancaria para el área de Inventario.')
                ->send();

            return;
        }

        try {
            if ($this->id) {
                $purchaseService->updatePurchase(
                    $this->id,
                    $date,
                    $this->invoice_number ?: null,
                    $supplier,
                    auth()->user(),
                    $items,
                    $account->id,
                    $this->payment_type,
                    $this->number_check ?: null,
                    $this->description ?: null,
                );

                $this->toast()
                    ->expandable(false)
                    ->success('Compra actualizada', 'La compra fue actualizada correctamente')
                    ->send();
            } else {
                $purchaseService->createPurchase(
                    $date,
                    $this->invoice_number ?: null,
                    $supplier,
                    auth()->user(),
                    $items,
                    $account->id,
                    $this->payment_type,
                    $this->number_check ?: null,
                    $this->description ?: null,
                );

                $this->toast()
                    ->expandable(false)
                    ->success('Compra registrada', 'La compra fue registrada correctamente')
                    ->send();
            }

            return redirect()->route('inventory.purchases');
        } catch (\RuntimeException $e) {
            $this->toast()
                ->expandable(false)
                ->error('Saldo insuficiente', $e->getMessage())
                ->send();
        }
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_type' => 'required|in:T,CH',
            'number_check' => 'required|string|max:20',
            'description' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $productIds = array_map(fn ($item) => (int) ($item['product_id'] ?? 0), $this->items);
            if (count($productIds) !== count(array_unique($productIds))) {
                $validator->errors()->add('items', 'No se puede seleccionar el mismo producto más de una vez.');
            }

            $account = Account::where('department', 'Inventory')->first();
            if ($account) {
                $total = 0;
                foreach ($this->items as $item) {
                    $total += (float) ($item['quantity'] ?? 0) * (float) ($item['unit_cost'] ?? 0);
                }

                $date = Carbon::parse($this->date);
                $monthStart = $date->copy()->startOfMonth();

                $hasBalanceInRange = Movement::query()
                    ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
                    ->where('transactions.account_id', $account->id)
                    ->where('movements.type', 'B')
                    ->whereBetween('movements.date', [$monthStart, $date])
                    ->exists();

                $totalDebit = Movement::query()
                    ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
                    ->where('transactions.account_id', $account->id)
                    ->whereIn('movements.type', ['D', 'B'])
                    ->whereBetween('movements.date', [$monthStart, $date])
                    ->sum('movements.amount');

                $totalCredit = Movement::query()
                    ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
                    ->where('transactions.account_id', $account->id)
                    ->where('movements.type', 'C')
                    ->whereBetween('movements.date', [$monthStart, $date])
                    ->sum('movements.amount');

                $balance = (float) $totalDebit - (float) $totalCredit;

                if (! $hasBalanceInRange) {
                    $previousBalance = Movement::query()
                        ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
                        ->where('transactions.account_id', $account->id)
                        ->where('movements.type', 'B')
                        ->where('movements.date', '<', $monthStart)
                        ->orderBy('movements.date', 'desc')
                        ->value('movements.amount');

                    if ($previousBalance) {
                        $balance += (float) $previousBalance;
                    }
                }

                if ($balance < $total) {
                    $validator->errors()->add('items', 'El banco asignado ('.$account->name.') no tiene fondos suficientes. Saldo disponible: Bs. '.number_format($balance, 2));
                }
            } else {
                $validator->errors()->add('bank_account', 'No existe una cuenta bancaria configurada para el área de Inventario. Asigne una cuenta bancaria con destino "Inventario".');
            }
        });
    }

    protected function getListeners()
    {
        return [
            'supplier-selected' => 'onSupplierSelected',
            'supplier-ci-manual' => 'onSupplierCiManual',
        ];
    }

    public function onSupplierCiManual($payload)
    {
        $this->ci = $payload['ci'];
    }

    public function onSupplierSelected($payload)
    {
        $this->ci = $payload['ci'];
        $this->supplier_name = $payload['full_name'];
        $this->supplier_phone = $payload['phone'] ?? null;
        $personId = $payload['person_id'];
        $supplier = Supplier::where('person_id', $personId)->first();
        $this->supplier_id = $supplier?->id;
    }
}
