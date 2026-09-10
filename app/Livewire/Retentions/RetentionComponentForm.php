<?php

namespace App\Livewire\Retentions;

use App\Models\Discount;
use App\Models\Retention;
use App\Models\Supplier;
use App\Models\Taxe;
use App\Services\PersonSupplierService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RetentionComponentForm extends Component
{
    public $id;

    public $type;

    public $amount;

    public $date;

    public $description;

    public $summary;

    public $supplier_id = null;

    public ?int $person_id = null;

    #[Validate('required|min:5|max:150')]
    public $full_name = '';

    public $ci = '';

    public function rules()
    {
        return [
            'ci' => ['required', 'min:4', 'max:15', Rule::unique('people', 'ci')->ignore($this->person_id)],
            'type' => ['required', 'in:S,G'],
            'amount' => ['required', 'numeric', 'gte:10.00', 'lte:999999999.99'],
            'date' => ['required', 'date', 'date_format:Y-m-d', 'after:2023-01-01', 'before_or_equal:today'],
            'description' => ['required', 'min:10', 'max:400'],
            'summary' => ['required', 'min:10', 'max:255'],
        ];
    }

    public function mount($id = 0)
    {
        $this->date = now()->format('Y-m-d');

        if ($id) {
            $this->id = $id;
            $retention = Retention::with('supplier.person')->findOrFail($id);
            $this->amount = $retention->amount;
            $this->date = $retention->date;
            $this->type = $retention->type;
            $this->description = $retention->description;
            $this->summary = $retention->summary;
            $this->full_name = $retention->supplier->person?->full_name ?? '';
            $this->ci = $retention->supplier->person?->ci ?? '';
            $this->person_id = $retention->supplier->person_id;
            $this->supplier_id = $retention->supplier->id;
        }
    }

    protected function getListeners()
    {
        return [
            'supplier-selected' => 'onSupplierSelected',
            'supplier-ci-manual' => 'onSupplierCiManual',
        ];
    }

    public function onSupplierSelected($payload)
    {
        $this->ci = $payload['ci'];
        $this->full_name = $payload['full_name'];
        $this->person_id = $payload['person_id'];

        $supplier = Supplier::firstOrCreate(
            ['person_id' => $payload['person_id']],
            ['description' => 'Proveedor registrado automáticamente']
        );
        $this->supplier_id = $supplier->id;
    }

    public function onSupplierCiManual($payload)
    {
        $this->ci = $payload['ci'];
        $this->supplier_id = null;
        $this->person_id = null;
    }

    #[Computed]
    public function taxes()
    {
        return Taxe::where('type', $this->type)
            ->orWhere('type', 'A')
            ->select('number', 'applied_discount')
            ->get();
    }

    public function render()
    {
        return view('livewire.retentions.retention-component-form');
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            $supplier = app(PersonSupplierService::class)->resolve($this->ci, $this->full_name);
            $code = $this->nextRetentionCode();

            $retention = Retention::create([
                'description' => $this->description,
                'summary' => $this->summary,
                'amount' => $this->amount,
                'status' => '0',
                'type' => $this->type,
                'code' => $code,
                'date' => $this->date,
                'supplier_id' => $supplier->id,
                'user_id' => auth()->id(),
            ]);

            $this->syncDiscounts($retention);
        });

        return redirect()->route('retentions');
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            $retention = Retention::findOrFail($this->id);
            $supplier = app(PersonSupplierService::class)->resolve($this->ci, $this->full_name);

            $retention->update([
                'description' => $this->description,
                'summary' => $this->summary,
                'amount' => $this->amount,
                'status' => '1',
                'type' => $this->type,
                'date' => $this->date,
                'supplier_id' => $supplier->id,
            ]);

            $retention->discounts()->delete();
            $this->syncDiscounts($retention);
        });

        return redirect()->route('retentions');
    }

    private function nextRetentionCode(): int
    {
        $date = Carbon::parse($this->date);

        $lastCode = Retention::query()
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->lockForUpdate()
            ->max('code');

        return ($lastCode ?? 0) + 1;
    }

    private function syncDiscounts(Retention $retention): void
    {
        $taxes = Taxe::where('type', $this->type)
            ->orWhere('type', 'A')
            ->select('id', 'applied_discount')
            ->get();

        $total = round($this->amount / (1 - ($taxes->sum('applied_discount') / 100)), 2);

        foreach ($taxes as $taxe) {
            Discount::create([
                'amount' => round($total * $taxe->applied_discount / 100, 2),
                'retention_id' => $retention->id,
                'taxe_id' => $taxe->id,
            ]);
        }
    }
}
