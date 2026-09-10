<?php

namespace App\Livewire\AccountStatements;

use App\Models\Cooperative;
use App\Models\Customer;
use App\Models\Person;
use App\Models\Supplier;
use App\Services\AccountStatementService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class AccountStatementComponent extends Component
{
    use Interactions, WithFileUploads, WithPagination;

    public string $activeTab = 'supplier';

    public $search;

    public $id;

    public $quantity = 10;

    public $full_name;

    public $phone;

    public $ci;

    public $file;

    public $cooperative_id;

    public $person_id;

    public function updatedActiveTab(): void
    {
        $this->resetPage();
        $this->clear();
    }

    public function with(): array
    {
        $query = $this->activeTab === 'customer'
            ? Customer::with('person')
            : Supplier::with('person');

        $records = $query
            ->when($this->search, function (Builder $query) {
                $query->whereHas('person', function ($personQuery) {
                    $personQuery->where('full_name', 'like', "%{$this->search}%")
                        ->orWhere('ci', 'like', "%{$this->search}%");
                });
            })
            ->paginate($this->quantity)
            ->withQueryString();

        $rows = $records->through(function ($record) {
            $service = app(AccountStatementService::class);
            $totals = $service->totalsForPerson($record->person_id);

            return (object) [
                'id' => $record->id,
                'full_name' => $record->person->full_name ?? '',
                'phone' => $record->person->phone ?? '',
                'ci' => $record->person->ci ?? '',
                'file' => $record->file,
                'balance' => $totals['balance'],
                'model' => $record,
            ];
        });

        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'full_name', 'label' => 'Nombre Completo'],
                ['index' => 'ci', 'label' => 'Cédula de Identidad'],
                ['index' => 'balance', 'label' => 'Saldo'],
                ['index' => 'action', 'label' => 'Operaciones'],
            ],
            'rows' => $rows,
        ];
    }

    public function render()
    {
        $cooperatives = Cooperative::select('name', 'id')->orderBy('name')->get();
        $options = $cooperatives->map(fn ($cooperative) => [
            'label' => $cooperative->name,
            'value' => $cooperative->id,
        ])->toArray();

        $holderLabel = app(AccountStatementService::class)->holderLabel($this->activeTab);

        return view('livewire.account-statements.account-statement-component', compact('options', 'holderLabel'));
    }

    public function rules(): array
    {
        return [
            'ci' => [
                'required',
                'string',
                'max:15',
                Rule::unique('people', 'ci')->ignore($this->person_id),
            ],
            'full_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:15',
            'cooperative_id' => 'nullable|exists:cooperatives,id',
            'file' => 'nullable|file|max:2048',
        ];
    }

    public function store(): void
    {
        $this->validate();
        $this->activeTab === 'customer' ? $this->persistCustomer() : $this->persistSupplier();

        $this->toast()
            ->expandable(false)
            ->success('Registro agregado', 'El registro fue creado correctamente')
            ->send();

        $this->clear();
    }

    public function update(): void
    {
        $this->validate();
        $this->activeTab === 'customer' ? $this->updateCustomer() : $this->updateSupplier();

        $this->toast()
            ->expandable(false)
            ->success('Registro actualizado', 'El registro fue actualizado correctamente')
            ->send();

        $this->clear();
    }

    #[On('load::supplier')]
    public function editSupplier(Supplier $supplier): void
    {
        $this->activeTab = 'supplier';
        $this->fillFromRecord($supplier);
    }

    #[On('load::customer')]
    public function editCustomer(Customer $customer): void
    {
        $this->activeTab = 'customer';
        $this->fillFromRecord($customer);
    }

    public function deleteSupplier(Supplier $supplier): void
    {
        $this->deleteRecord($supplier);
    }

    public function deleteCustomer(Customer $customer): void
    {
        $this->deleteRecord($customer);
    }

    public function clear(): void
    {
        $this->dispatch('close-modal');
        $this->resetValidation();
        $this->reset(['full_name', 'file', 'cooperative_id', 'phone', 'id', 'ci', 'person_id']);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    private function fillFromRecord(Supplier|Customer $record): void
    {
        $this->id = $record->id;
        $this->person_id = $record->person_id;
        $this->full_name = $record->person->full_name ?? '';
        $this->ci = $record->person->ci ?? '';
        $this->phone = $record->person->phone ?? '';
        $this->cooperative_id = $record->cooperative_id;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    private function persistSupplier(): void
    {
        $person = Person::create([
            'ci' => $this->ci,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
        ]);

        Supplier::create([
            'person_id' => $person->id,
            'cooperative_id' => $this->cooperative_id,
            'file' => $this->storeUploadedFile('suppliers/contract'),
            'description' => 'Proveedor registrado desde estado de cuenta',
        ]);
    }

    private function persistCustomer(): void
    {
        $person = Person::create([
            'ci' => $this->ci,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
        ]);

        Customer::create([
            'person_id' => $person->id,
            'cooperative_id' => $this->cooperative_id,
            'file' => $this->storeUploadedFile('customers/contract'),
        ]);
    }

    private function updateSupplier(): void
    {
        $supplier = Supplier::with('person')->findOrFail($this->id);
        $person = $this->syncPerson($supplier->person);

        $supplier->update([
            'person_id' => $person->id,
            'cooperative_id' => $this->cooperative_id,
            'file' => $this->storeUploadedFile('suppliers/contract', $supplier->file),
        ]);
    }

    private function updateCustomer(): void
    {
        $customer = Customer::with('person')->findOrFail($this->id);
        $person = $this->syncPerson($customer->person);

        $customer->update([
            'person_id' => $person->id,
            'cooperative_id' => $this->cooperative_id,
            'file' => $this->storeUploadedFile('customers/contract', $customer->file),
        ]);
    }

    private function syncPerson(?Person $currentPerson): Person
    {
        $person = Person::firstOrCreate(
            ['ci' => $this->ci],
            ['full_name' => $this->full_name, 'phone' => $this->phone]
        );

        if (! $person->wasRecentlyCreated) {
            $person->update([
                'full_name' => $this->full_name,
                'phone' => $this->phone,
            ]);
        }

        return $person;
    }

    private function storeUploadedFile(string $directory, ?string $currentFile = null): ?string
    {
        if (! $this->file) {
            return $currentFile;
        }

        $fileName = uniqid().'.'.$this->file->extension();
        $this->file->storeAs($directory.'/', $fileName, 'public');

        if ($currentFile && file_exists(public_path('storage/'.$directory.'/'.$currentFile))) {
            unlink(public_path('storage/'.$directory.'/'.$currentFile));
        }

        return $fileName;
    }

    private function deleteRecord(Supplier|Customer $record): void
    {
        if ($record->file) {
            $directory = $record instanceof Supplier ? 'suppliers/contract' : 'customers/contract';

            if (file_exists(public_path('storage/'.$directory.'/'.$record->file))) {
                unlink(public_path('storage/'.$directory.'/'.$record->file));
            }
        }

        $record->delete();

        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El registro fue eliminado correctamente')
            ->send();

        $this->clear();
    }
}
