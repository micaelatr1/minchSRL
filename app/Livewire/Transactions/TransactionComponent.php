<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\Movement;
use App\Models\Supplier;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class TransactionComponent extends Component
{
    use Interactions, WithPagination;

    public $selectedDate = null;

    public $selectedSupplierId = null; // Filtro por proveedor

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m');
    }

    public function render()
    {
        $start = Carbon::createFromFormat('Y-m', $this->selectedDate)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Obtener cuentas con saldos del mes
        $accounts = Account::select('accounts.*')
            ->when($this->selectedSupplierId, function ($query) {
                $personId = Supplier::whereKey($this->selectedSupplierId)->value('person_id');

                if ($personId) {
                    $query->whereHas('movements', fn ($movementQuery) => $movementQuery->where('person_id', $personId));
                }
            })
            ->addSelect([
                'amountD' => Movement::selectRaw('COALESCE(SUM(amount), 0)')
                    ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
                    ->whereColumn('transactions.account_id', 'accounts.id')
                    ->where('movements.type', 'D')
                    ->whereBetween('movements.date', [$start, $end]),
                'amountC' => Movement::selectRaw('COALESCE(SUM(amount), 0)')
                    ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
                    ->whereColumn('transactions.account_id', 'accounts.id')
                    ->where('movements.type', 'C')
                    ->whereBetween('movements.date', [$start, $end]),
                'amountB' => Movement::selectRaw('COALESCE(SUM(amount), 0)')
                    ->join('transactions', 'movements.id', '=', 'transactions.movement_id')
                    ->whereColumn('transactions.account_id', 'accounts.id')
                    ->where('movements.type', 'B')
                    ->whereBetween('movements.date', [$start, $end]),
            ])
            ->paginate(10);

        // Transformar para agregar saldo (incluye saldo del mes anterior)
        $accounts->getCollection()->transform(function ($account) {
            $account->amountS = ($account->amountB ?? 0) + ($account->amountD ?? 0) - ($account->amountC ?? 0);

            return $account;
        });

        // Obtener proveedores que tienen movimientos en el mes (para el filtro)
        $suppliers = Supplier::with('person')
            ->whereHas('person.movements', function ($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            })
            ->get();

        return view('livewire.transactions.transaction-component', compact('accounts', 'suppliers'));
    }

    // Método para limpiar el formulario (si existe algún modal)
    public function clear()
    {
        $this->dispatch('close-modal');
        $this->reset([
            'amount',
            'type',
            'date',
            'account_id',
            'description',
            'doc',
        ]);
    }

    // Opcional: resetear paginación al cambiar filtros
    public function updatingSelectedSupplierId()
    {
        $this->resetPage();
    }

    public function updatingSelectedDate()
    {
        $this->resetPage();
    }
}
