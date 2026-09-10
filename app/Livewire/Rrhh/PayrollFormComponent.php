<?php

namespace App\Livewire\Rrhh;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class PayrollFormComponent extends Component
{
    use Interactions;

    public ?int $id = null;

    public Payroll $payroll;

    public string $period = '';

    public string $searchPaid = '';

    public string $searchPending = '';

    public array $paidEmployees = [];

    public array $pendingEmployees = [];

    public ?int $editingDetailId = null;

    public ?int $selectedEmployeeId = null;

    public string $selectedEmployeeName = '';

    public string $selectedEmployeeCi = '';

    public string $selectedEmployeeTenure = '';

    public float $selectedEmployeeBaseSalary = 0;

    public float $modalBonuses = 0;

    public float $modalDiscounts = 0;

    public function mount(int $id): void
    {
        $this->id = $id;
        $this->payroll = Payroll::with('details.employee.person')->findOrFail($id);
        $this->period = $this->payroll->period;
        $this->loadEmployees();
    }

    private function loadEmployees(): void
    {
        $paidIds = $this->payroll->details->pluck('employee_id')->toArray();

        $allPaid = $this->payroll->details->map(fn ($d) => [
            'id' => $d->id,
            'employee_id' => $d->employee_id,
            'employee_name' => $d->employee?->person?->full_name ?? 'Empleado #'.$d->employee_id,
            'ci' => $d->employee?->person?->ci ?? '-',
            'base_salary' => (float) $d->base_salary,
            'bonuses' => (float) $d->bonuses,
            'discounts' => (float) $d->discounts,
            'net_salary' => (float) $d->net_salary,
        ]);

        $allPending = Employee::with('person')
            ->where('status', 'active')
            ->whereNotIn('id', $paidIds)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'employee_name' => $e->person?->full_name ?? 'Empleado #'.$e->id,
                'ci' => $e->person?->ci ?? '-',
                'tenure' => $e->tenure,
                'base_salary' => (float) $e->base_salary,
            ]);

        $this->paidEmployees = $this->filterBySearch($allPaid, $this->searchPaid);
        $this->pendingEmployees = $this->filterBySearch($allPending, $this->searchPending);
    }

    private function filterBySearch($items, string $term): array
    {
        if (blank($term)) {
            return $items->toArray();
        }

        $lower = mb_strtolower($term);

        return $items->filter(fn ($i) =>
            mb_strpos(mb_strtolower($i['employee_name']), $lower) !== false ||
            mb_strpos(mb_strtolower($i['ci'] ?? ''), $lower) !== false
        )->values()->toArray();
    }

    public function updatedSearchPaid(): void
    {
        $this->loadEmployees();
    }

    public function updatedSearchPending(): void
    {
        $this->loadEmployees();
    }

    public function openPaymentModal(int $employeeId): void
    {
        $employee = Employee::with('person')->findOrFail($employeeId);

        $this->editingDetailId = null;
        $this->selectedEmployeeId = $employee->id;
        $this->selectedEmployeeName = $employee->person?->full_name ?? 'Empleado #'.$employee->id;
        $this->selectedEmployeeCi = $employee->person?->ci ?? '-';
        $this->selectedEmployeeTenure = $employee->tenure;
        $this->selectedEmployeeBaseSalary = (float) $employee->base_salary;
        $this->modalBonuses = 0;
        $this->modalDiscounts = 0;

        $this->dispatch('open-payment-modal-window');
    }

    public function editPayment(int $detailId): void
    {
        $detail = PayrollDetail::with('employee.person')->findOrFail($detailId);

        $this->editingDetailId = $detail->id;
        $this->selectedEmployeeId = $detail->employee_id;
        $this->selectedEmployeeName = $detail->employee?->person?->full_name ?? 'Empleado #'.$detail->employee_id;
        $this->selectedEmployeeCi = $detail->employee?->person?->ci ?? '-';
        $this->selectedEmployeeTenure = $detail->employee?->tenure ?? 'N/A';
        $this->selectedEmployeeBaseSalary = (float) $detail->base_salary;
        $this->modalBonuses = (float) $detail->bonuses;
        $this->modalDiscounts = (float) $detail->discounts;

        $this->dispatch('open-payment-modal-window');
    }

    public function confirmPayment(): void
    {
        if ($this->editingDetailId) {
            $this->updatePayment();
        } else {
            $this->createPayment();
        }
    }

    private function createPayment(): void
    {
        $this->validate([
            'selectedEmployeeId' => 'required|integer',
            'modalBonuses' => 'required|numeric|min:0',
            'modalDiscounts' => 'required|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($this->selectedEmployeeId);
        $netSalary = $this->selectedEmployeeBaseSalary + $this->modalBonuses - $this->modalDiscounts;

        try {
            $this->payroll->payEmployee($employee, $this->selectedEmployeeBaseSalary, auth()->id(), $this->modalBonuses, $this->modalDiscounts);

            $this->payroll->load('details.employee.person');
            $this->loadEmployees();

            $this->clear();

            $this->toast()
                ->expandable(false)
                ->success('Empleado pagado', $this->selectedEmployeeName.' fue pagado correctamente (Bs. '.number_format($netSalary, 2).')')
                ->send();
        } catch (\RuntimeException $e) {
            $this->toast()->expandable(false)->error('Error', $e->getMessage())->send();
        }
    }

    private function updatePayment(): void
    {
        $this->validate([
            'editingDetailId' => 'required|integer',
            'modalBonuses' => 'required|numeric|min:0',
            'modalDiscounts' => 'required|numeric|min:0',
        ]);

        $netSalary = $this->selectedEmployeeBaseSalary + $this->modalBonuses - $this->modalDiscounts;

        try {
            $this->payroll->updatePayDetail($this->editingDetailId, $this->modalBonuses, $this->modalDiscounts);

            $this->payroll->load('details.employee.person');
            $this->loadEmployees();

            $this->clear();

            $this->toast()
                ->expandable(false)
                ->success('Pago actualizado', $this->selectedEmployeeName.' fue actualizado correctamente (Bs. '.number_format($netSalary, 2).')')
                ->send();
        } catch (\RuntimeException $e) {
            $this->toast()->expandable(false)->error('Error', $e->getMessage())->send();
        }
    }

    public function delete(PayrollDetail $detail): void
    {
        $name = $detail->employee?->person?->full_name ?? 'Empleado #'.$detail->employee_id;

        $this->payroll->deletePayDetail($detail->id);

        $this->payroll->load('details.employee.person');
        $this->loadEmployees();

        $this->toast()
            ->expandable(false)
            ->success('Pago eliminado', 'El pago a '.$name.' fue eliminado correctamente')
            ->send();
    }

    public function clear(): void
    {
        $this->reset('editingDetailId', 'selectedEmployeeId', 'selectedEmployeeName', 'selectedEmployeeCi', 'selectedEmployeeTenure', 'selectedEmployeeBaseSalary', 'modalBonuses', 'modalDiscounts');
        $this->resetValidation();
        $this->dispatch('modal:payment-modal-close');
    }

    public function render()
    {
        return view('livewire.rrhh.payroll-form-component');
    }
}
