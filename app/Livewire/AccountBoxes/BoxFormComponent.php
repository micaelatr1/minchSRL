<?php

namespace App\Livewire\AccountBoxes;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Movement;
use App\Models\Person;
use App\Services\CashBalanceService;
use App\Services\PersonSupplierService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BoxFormComponent extends Component
{
    public $id;

    public $amount;

    public $type;

    public $date;

    public $description;

    public $number_check;

    public $personType = 'supplier';

    public $ci;

    public $full_name;

    public $person_id;

    public $phone;

    public $contracts = [];

    public $contract_id = null;

    public $date_account;

    public $account_id;

    public function mount($date_account = null, $account_id = null, $id = 0)
    {
        $this->date_account = $date_account;
        $this->account_id = $account_id;
        $this->date = $date_account ? Carbon::parse($date_account)->format('Y-m-d') : now()->format('Y-m-d');

        if ($id) {
            $movement = Movement::with('person', 'box')->find($id);
            if ($movement) {
                $this->id = $movement->id;
                $this->amount = $movement->amount;
                $this->type = $movement->type;
                $this->date = $movement->date;
                $this->description = $movement->description;

                $this->number_check = null;

                if ($movement->person) {
                    $this->ci = $movement->person->ci;
                    $this->full_name = $movement->person->full_name;
                    $this->phone = $movement->person->phone;
                    $this->person_id = $movement->person->id;
                    $this->contract_id = $movement->contract_id;

                    $movement->person->load(['supplier', 'customer']);
                    $this->personType = $movement->person->customer ? 'customer' : ($movement->person->supplier ? 'supplier' : $this->personType);

                    $this->loadContracts();
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.account-boxes.box-form-component');
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
        $this->person_id = null;
        $this->full_name = '';
        $this->phone = '';
        $this->contracts = [];
        $this->contract_id = null;
    }

    public function onSupplierSelected($payload)
    {
        $this->ci = $payload['ci'];
        $this->full_name = $payload['full_name'];
        $this->phone = $payload['phone'];
        $this->person_id = $payload['person_id'];

        $person = Person::with(['supplier', 'customer'])->find($payload['person_id']);
        if ($person) {
            $this->personType = $person->customer ? 'customer' : ($person->supplier ? 'supplier' : $this->personType);
        }

        $this->loadContracts();
    }

    public function store()
    {
        $this->authorize('Crear caja chica');

        $this->validate([
            'personType' => 'required|in:supplier,customer',
            'ci' => 'required|string|max:15',
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:15',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:D,C',
            'date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|max:255',
            'number_check' => 'nullable|string|max:20',
            'contract_id' => 'nullable|exists:contracts,id',
        ]);

        DB::transaction(function () {
            app(CashBalanceService::class)->recalculateFromDate($this->date);

            $person = $this->resolvePerson();

            $movement = Movement::create([
                'date' => $this->date,
                'description' => $this->description,
                'type' => $this->type,
                'amount' => $this->amount,
                'person_id' => $person->id,
                'contract_id' => $this->contract_id ?: null,
                'user_id' => auth()->id(),
            ]);

            $movement->box()->create([]);

            $this->id = $movement->id;

            app(CashBalanceService::class)->recalculateFromDate($movement->date);

            $this->updateContractStatus($this->contract_id);
        });

        return redirect()->route('receipt.box.pdf', $this->id);
    }

    public function update()
    {
        $this->authorize('Editar caja chica');

        $this->validate([
            'personType' => 'required|in:supplier,customer',
            'ci' => 'required|string|max:15',
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:15',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:D,C',
            'date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|max:255',
            'number_check' => 'nullable|string|max:20',
            'contract_id' => 'nullable|exists:contracts,id',
        ]);

        DB::transaction(function () {
            $movement = Movement::with('person', 'box')->findOrFail($this->id);
            $oldContractId = $movement->contract_id;

            app(CashBalanceService::class)->recalculateFromDate($this->date);

            $person = $this->resolvePerson();

            $movement->update([
                'date' => $this->date,
                'description' => $this->description,
                'type' => $this->type,
                'amount' => $this->amount,
                'person_id' => $person->id,
                'contract_id' => $this->contract_id ?: null,
            ]);

            app(CashBalanceService::class)->recalculateFromDate($this->date);

            $this->updateContractStatus($oldContractId);
            if ($this->contract_id && $this->contract_id !== $oldContractId) {
                $this->updateContractStatus($this->contract_id);
            }
        });

        return redirect()->route('receipt.box.pdf', $this->id);
    }

    private function loadContracts(): void
    {
        if (! $this->person_id) {
            $this->contracts = [];
            $this->contract_id = null;

            return;
        }

        $this->contracts = Contract::where('person_id', $this->person_id)
            ->where('status', 'in_progress')
            ->get();
    }

    private function updateContractStatus(?int $contractId): void
    {
        if (! $contractId) {
            return;
        }

        $contract = Contract::find($contractId);
        if (! $contract) {
            return;
        }

        $totalPaid = (float) $contract->movements()->where('type', 'C')->sum('amount');
        $newStatus = $totalPaid >= (float) $contract->total_amount ? 'completed' : 'in_progress';
        $contract->update(['status' => $newStatus]);
    }

    public function clear()
    {
        $this->reset(['id', 'ci', 'full_name', 'phone', 'amount', 'type', 'date', 'description', 'number_check', 'person_id', 'personType', 'contracts', 'contract_id']);
        $this->resetValidation();
        $this->dispatch('close-modal');
    }

    private function resolvePerson(): Person
    {
        if ($this->personType === 'supplier') {
            $supplier = app(PersonSupplierService::class)->resolve(
                $this->ci,
                $this->full_name,
                $this->phone
            );

            return $supplier->person;
        }

        $person = Person::firstOrCreate(
            ['ci' => $this->ci],
            ['full_name' => $this->full_name, 'phone' => $this->phone]
        );

        if ($person->wasRecentlyCreated === false) {
            $person->update(array_filter([
                'full_name' => $this->full_name,
                'phone' => $this->phone,
            ]));
        }

        Customer::firstOrCreate(
            ['person_id' => $person->id]
        );

        return $person;
    }
}
