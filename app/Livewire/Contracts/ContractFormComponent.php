<?php

namespace App\Livewire\Contracts;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Person;
use App\Services\PersonSupplierService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class ContractFormComponent extends Component
{
    use Interactions, WithFileUploads;

    public $id;

    public $type = 'supplier';

    public $description;

    public $total_amount;

    public $start_date;

    public $end_date;

    public $ci;

    public $full_name;

    public $phone;

    public $person_id;

    public $file;

    public $existingFile;

    public function mount($id = 0)
    {
        $this->start_date = now()->format('Y-m-d');

        if ($id) {
            $contract = Contract::with('person')->findOrFail($id);
            $this->id = $contract->id;
            $this->type = $contract->type;
            $this->description = $contract->description;
            $this->total_amount = $contract->total_amount;
            $this->start_date = $contract->start_date->format('Y-m-d');
            $this->end_date = $contract->end_date?->format('Y-m-d');
            $this->existingFile = $contract->file;

            if ($contract->person) {
                $this->ci = $contract->person->ci;
                $this->full_name = $contract->person->full_name;
                $this->phone = $contract->person->phone;
                $this->person_id = $contract->person->id;
            }
        }
    }

    public function render()
    {
        return view('livewire.contracts.contract-form-component');
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
    }

    public function onSupplierSelected($payload)
    {
        $this->ci = $payload['ci'];
        $this->full_name = $payload['full_name'];
        $this->phone = $payload['phone'];
        $this->person_id = $payload['person_id'];

        $person = Person::with(['supplier', 'customer'])->find($payload['person_id']);
        if ($person) {
            $this->type = $person->customer ? 'customer' : ($person->supplier ? 'supplier' : $this->type);
        }
    }

    public function rules()
    {
        return [
            'type' => ['required', 'in:supplier,customer'],
            'ci' => ['required', 'string', 'max:15'],
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:15'],
            'description' => ['required', 'string', 'max:400'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
        ];
    }

    public function store()
    {
        $this->authorize('Crear contratos');
        $this->validate();

        DB::transaction(function () {
            $person = $this->resolvePerson();

            $filepath = $this->existingFile;
            if ($this->file) {
                $filepath = $this->file->store('contracts', 'public');
            }

            Contract::create([
                'description' => $this->description,
                'total_amount' => $this->total_amount,
                'person_id' => $person->id,
                'type' => $this->type,
                'status' => 'in_progress',
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'file' => $filepath,
                'user_id' => auth()->id(),
            ]);
        });

        $this->toast()
            ->success('Contrato creado', 'El contrato fue registrado correctamente')
            ->send();

        return redirect()->route('contracts');
    }

    public function update()
    {
        $this->authorize('Editar contratos');
        $this->validate();

        DB::transaction(function () {
            $contract = Contract::findOrFail($this->id);
            $person = $this->resolvePerson();

            $filepath = $contract->file;
            if ($this->file) {
                $filepath = $this->file->store('contracts', 'public');
            }

            $contract->update([
                'description' => $this->description,
                'total_amount' => $this->total_amount,
                'person_id' => $person->id,
                'type' => $this->type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'file' => $filepath,
            ]);
        });

        $this->toast()
            ->success('Contrato actualizado', 'El contrato fue actualizado correctamente')
            ->send();

        return redirect()->route('contracts');
    }

    private function resolvePerson(): Person
    {
        if ($this->type === 'supplier') {
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
