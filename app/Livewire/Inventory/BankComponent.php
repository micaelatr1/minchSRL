<?php

namespace App\Livewire\Inventory;

use App\Models\Account;
use App\Models\KardexMovement;
use App\Models\Movement;
use App\Models\PurchaseItem;
use App\Services\MovementBalanceService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class BankComponent extends Component
{
    use Interactions, WithPagination;

    public ?int $selectedAccountId = null;

    public string $selectedDate;

    public array $expandedMovements = [];

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m');
        $account = Account::where('department', 'Inventory')->first();
        $this->selectedAccountId = $account?->id;
    }

    public function render()
    {
        $start = Carbon::createFromFormat('Y-m', $this->selectedDate)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $account = $this->selectedAccountId ? Account::find($this->selectedAccountId) : null;

        $transactions = collect();
        if ($account) {
            $transactions = Movement::whereHas('transaction', function ($query) {
                $query->where('account_id', $this->selectedAccountId);
            })
                ->with([
                    'transaction' => fn ($q) => $q->where('account_id', $this->selectedAccountId),
                    'person',
                ])
                ->whereBetween('date', [$start, $end])
                ->orderBy('date')
                ->orderBy('id')
                ->select('*')
                ->selectRaw('SUM(CASE WHEN type IN ("D", "B") THEN amount ELSE -amount END) OVER (ORDER BY date, id) as balance')
                ->paginate(10);
        }

        $inventoryAccounts = Account::where('department', 'Inventory')->get();

        return view('livewire.inventory.bank-component', compact('account', 'transactions', 'inventoryAccounts', 'start', 'end'));
    }

    public function toggleExpand(int $movementId)
    {
        if (in_array($movementId, $this->expandedMovements)) {
            $this->expandedMovements = array_filter($this->expandedMovements, fn ($id) => $id !== $movementId);
        } else {
            $this->expandedMovements[] = $movementId;
        }
    }

    public function getProductsForMovement(int $movementId)
    {
        return KardexMovement::with('product')
            ->whereHasMorph('reference', [PurchaseItem::class], function ($q) use ($movementId) {
                $q->whereHas('purchase', fn ($q) => $q->where('movement_id', $movementId));
            })
            ->get();
    }

    public function delete(Movement $movement)
    {
        $this->authorize('Eliminar entradas inventario');

        $date = $movement->date;
        $accountId = $movement->transaction?->account_id;

        $movement->delete();

        if ($accountId) {
            app(MovementBalanceService::class)->recalculateFromDate($date, $accountId);
        }

        $this->toast()
            ->expandable(false)
            ->success('Movimiento eliminado', 'El movimiento fue eliminado correctamente')
            ->send();
    }

    public function updatingSelectedDate()
    {
        $this->resetPage();
    }

    public function updatingSelectedAccountId()
    {
        $this->resetPage();
    }
}
