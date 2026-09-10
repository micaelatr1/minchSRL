<?php

namespace App\Livewire\Rrhh;

use App\Models\Payroll;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class PayrollComponent extends Component
{
    use Interactions, WithPagination;

    public ?string $filterPeriodFrom = '';

    public ?string $filterPeriodTo = '';

    public function mount(): void
    {
        if (empty($this->filterPeriodFrom)) {
            $this->filterPeriodFrom = date('Y') . '-01';
        }
        if (empty($this->filterPeriodTo)) {
            $this->filterPeriodTo = date('Y-m');
        }
    }

    public string $period = '';

    public string $status = 'pending';

    public ?int $editingPayrollId = null;

    protected function rules()
    {
        $unique = Rule::unique('payrolls', 'period');

        if ($this->editingPayrollId) {
            $unique = $unique->ignore($this->editingPayrollId);
        }

        return [
            'period' => [
                'required',
                'regex:/^\d{4}-\d{2}$/',
                $unique,
            ],
            'status' => 'required|in:pending,paid',
        ];
    }
    public function with(): array
    {
        $payrolls = Payroll::query()
            ->with('user')
            ->withCount('details')
            ->when($this->filterPeriodFrom, function ($query) {
                return $query->where('period', '>=', $this->filterPeriodFrom);
            })
            ->when($this->filterPeriodTo, function ($query) {
                return $query->where('period', '<=', $this->filterPeriodTo);
            })
            ->orderByDesc('period')
            ->paginate(12)
            ->withQueryString();

        $rows = $payrolls->through(function ($payroll) {
            return (object) [
                'id' => $payroll->id,
                'period' => $payroll->period,
                'user' => $payroll->user?->name ?? '-',
                'employees' => $payroll->details_count,
                'total_paid' => $payroll->totalPaid(),
                'status' => $payroll->status,
                'model' => $payroll,
            ];
        });

        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'period', 'label' => 'Período'],
                ['index' => 'user', 'label' => 'Creado por'],
                ['index' => 'employees', 'label' => 'Pagados'],
                ['index' => 'total_paid', 'label' => 'Total Pagado (Bs.)'],
                ['index' => 'status', 'label' => 'Estado'],
                ['index' => 'action', 'label' => 'Acciones'],
            ],
            'rows' => $rows,
        ];
    }
    public function render()
    {
        return view('livewire.rrhh.payroll-component');
    }
    public function save()
    {
        $this->authorize('Crear planillas');

        $this->validate();

        Payroll::create([
            'period' => $this->period,
            'user_id' => auth()->id(),
            'status' => $this->status,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Planilla creada', 'La planilla del período '.$this->period.' fue creada correctamente')
            ->send();

        $this->clear();
    }
    public function edit(int $id): void
    {
        $this->authorize('Editar planillas');

        $payroll = Payroll::findOrFail($id);

        $this->editingPayrollId = $payroll->id;
        $this->period = $payroll->period;
        $this->status = $payroll->status;

        $this->dispatch('open-edit-modal');
    }
    public function update(): void
    {
        $this->authorize('Editar planillas');

        $this->validate();

        $payroll = Payroll::findOrFail($this->editingPayrollId);

        $payroll->update([
            'period' => $this->period,
            'status' => $this->status,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Planilla actualizada', 'La planilla del período '.$this->period.' fue actualizada correctamente')
            ->send();

        $this->clear();
    }
    public function clear()
    {
        $this->reset('period', 'status', 'editingPayrollId');
        $this->resetValidation();
        $this->dispatch('close-modal');
    }
    public function delete(Payroll $payroll)
    {
        $this->authorize('Eliminar planillas');

        $payroll->delete();

        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'La planilla fue eliminada correctamente')
            ->send();
    }
    public function updatedFilterPeriodFrom()
    {
        $this->resetPage();
    }

    public function updatedFilterPeriodTo()
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('filterPeriodFrom', 'filterPeriodTo');
        $this->resetPage();
    }
}
