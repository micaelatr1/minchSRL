<?php

namespace App\Livewire\Contracts;

use App\Models\Account;
use App\Models\Contract;
use App\Models\Movement;
use App\Services\CashBalanceService;
use App\Services\MovementBalanceService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class ContractShowComponent extends Component
{
    use Interactions;

    public Contract $contract;

    public $payment_type = 'direct';

    public $payment_amount;

    public $payment_date;

    public $payment_description;

    public $account_id;

    public $payment_method = 'CH';

    public $number_check;

    public $editingMovementId = null;

    public function mount(Contract $contract)
    {
        $this->contract = $contract->load(['person', 'movements.box', 'movements.transaction', 'movements.user']);
        $this->payment_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $accounts = Account::all();
        return view('livewire.contracts.contract-show-component', compact('accounts'));
    }

    public function rules()
    {
        $rules = [
            'payment_type' => ['required', 'in:direct,cash_box,bank'],
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_description' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->payment_type === 'bank') {
            $rules['account_id'] = ['required', 'exists:accounts,id'];
            $rules['payment_method'] = ['required', 'in:CH,T'];
            $rules['number_check'] = ['nullable', 'string', 'max:20'];
        }

        return $rules;
    }

    public function store()
    {
        $this->authorize('Crear contratos');
        $this->validate();

        $amount = (float) $this->payment_amount;
        $remaining = $this->contract->remaining_amount;

        if (! $this->editingMovementId && $amount > $remaining) {
            $this->addError('payment_amount', "El monto excede el saldo pendiente (Bs {$remaining})");

            return;
        }

        DB::transaction(function () use ($amount) {
            if ($this->editingMovementId) {
                $movement = Movement::with(['box', 'transaction'])->findOrFail($this->editingMovementId);
                $oldType = $movement->box ? 'cash_box' : ($movement->transaction ? 'bank' : 'direct');
                $oldDate = $movement->getOriginal('date');
                $oldAccountId = $movement->transaction?->account_id;

                $movement->update([
                    'date' => $this->payment_date,
                    'description' => $this->payment_description ?: "Pago contrato {$this->contract->code}",
                    'amount' => $amount,
                ]);

                if ($oldType !== $this->payment_type) {
                    if ($movement->box) {
                        $movement->box()->delete();
                    }
                    if ($movement->transaction) {
                        $movement->transaction()->delete();
                    }
                    $movement->unsetRelation('box')->unsetRelation('transaction');
                }

                if ($this->payment_type === 'cash_box' && $oldType !== 'cash_box') {
                    $movement->box()->create([]);
                } elseif ($this->payment_type === 'bank') {
                    if ($oldType === 'bank') {
                        $movement->transaction->update([
                            'account_id' => $this->account_id,
                            'payment_type' => $this->payment_method,
                            'number_check' => $this->number_check ?: null,
                        ]);
                    } else {
                        $movement->transaction()->create([
                            'account_id' => $this->account_id,
                            'payment_type' => $this->payment_method,
                            'number_check' => $this->number_check ?: null,
                        ]);
                    }
                }

                $this->recalculateBalances($oldType, $oldDate, $oldAccountId);
            } else {
                if ($this->payment_type === 'direct') {
                    Movement::create([
                        'date' => $this->payment_date,
                        'description' => $this->payment_description ?: "Pago contrato {$this->contract->code}",
                        'type' => 'D',
                        'amount' => $amount,
                        'person_id' => $this->contract->person_id,
                        'contract_id' => $this->contract->id,
                        'user_id' => auth()->id(),
                    ]);
                } elseif ($this->payment_type === 'cash_box') {
                    $movement = Movement::create([
                        'date' => $this->payment_date,
                        'description' => $this->payment_description ?: "Pago contrato {$this->contract->code}",
                        'type' => 'D',
                        'amount' => $amount,
                        'person_id' => $this->contract->person_id,
                        'contract_id' => $this->contract->id,
                        'user_id' => auth()->id(),
                    ]);
                    $movement->box()->create([]);
                    app(CashBalanceService::class)->recalculateFromDate($this->payment_date);
                } elseif ($this->payment_type === 'bank') {
                    $movement = Movement::create([
                        'date' => $this->payment_date,
                        'description' => $this->payment_description ?: "Pago contrato {$this->contract->code}",
                        'type' => 'D',
                        'amount' => $amount,
                        'person_id' => $this->contract->person_id,
                        'contract_id' => $this->contract->id,
                        'user_id' => auth()->id(),
                    ]);
                    $movement->transaction()->create([
                        'account_id' => $this->account_id,
                        'payment_type' => $this->payment_method,
                        'number_check' => $this->number_check ?: null,
                    ]);
                    app(MovementBalanceService::class)->recalculateFromDate($this->payment_date, $this->account_id);
                }
            }
        });

        $this->updateStatus();
        $this->clear();
        $this->contract->load(['movements.box', 'movements.transaction', 'movements.user']);

        $this->toast()
            ->success($this->editingMovementId ? 'Pago actualizado' : 'Pago registrado', 'El pago fue procesado correctamente')
            ->send();
    }

    public function delete(Movement $movement): void
    {
        $this->authorize('Eliminar contratos');

        $movement->load(['box', 'transaction']);
        $movementType = $movement->box ? 'cash_box' : ($movement->transaction ? 'bank' : 'direct');
        $movementDate = $movement->date;
        $oldAccountId = $movement->transaction?->account_id;

        $movement->delete();

        $this->recalculateBalances($movementType, $movementDate, $oldAccountId);
        $this->updateStatus();
        $this->contract->load(['movements.box', 'movements.transaction', 'movements.user']);

        $this->toast()
            ->success('Pago eliminado', 'El pago fue eliminado correctamente')
            ->send();
    }

    public function getPaymentTypeLabel($type): string
    {
        return match ($type) {
            'cash_box' => 'Caja chica',
            'bank' => 'Banco',
            'direct' => 'Directo',
            default => $type,
        };
    }

    public function getMovementRef(Movement $movement): string
    {
        if ($movement->box) {
            return $movement->box->number_label;
        }

        if ($movement->transaction) {
            return $movement->transaction->number_label;
        }

        return '—';
    }

    private function recalculateBalances(?string $movementType, $movementDate, ?int $oldAccountId): void
    {
        if ($movementType === 'cash_box' || $this->payment_type === 'cash_box') {
            app(CashBalanceService::class)->recalculateFromDate($movementDate);
        }
        if ($movementType === 'bank' || $this->payment_type === 'bank') {
            $accountId = $this->payment_type === 'bank' ? $this->account_id : $oldAccountId;
            if ($accountId) {
                app(MovementBalanceService::class)->recalculateFromDate($movementDate, $accountId);
            }
        }
    }

    private function updateStatus(): void
    {
        $totalPaid = $this->contract->paid_amount;
        $newStatus = match (true) {
            $totalPaid >= (float) $this->contract->total_amount => 'completed',
            default => 'in_progress',
        };
        $this->contract->update(['status' => $newStatus]);
        $this->contract->refresh();
    }

    public function clear(): void
    {
        $this->resetValidation();
        $this->reset(['payment_amount', 'payment_description', 'account_id', 'number_check', 'editingMovementId']);
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_type = 'direct';
        $this->payment_method = 'CH';
        $this->dispatch('close-modal');
    }
}
