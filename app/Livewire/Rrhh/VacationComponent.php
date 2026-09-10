<?php

namespace App\Livewire\Rrhh;

use App\Models\Employee;
use App\Models\Vacation;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class VacationComponent extends Component
{
    use Interactions, WithPagination;

    public $search = '';

    public function with(): array
    {
        $vacations = Vacation::with('employee.person')
            ->where('status', 'taken')
            ->orderByDesc('start_date')
            ->paginate(10)
            ->withQueryString();

        $rows = $vacations->through(fn ($v) => (object) [
            'id' => $v->id,
            'employee' => $v->employee?->person?->full_name ?? '-',
            'ci' => $v->employee?->person?->ci ?? '-',
            'start_date' => $v->start_date->format('d/m/Y'),
            'end_date' => $v->end_date->format('d/m/Y'),
            'days' => $v->days,
            'model' => $v,
        ]);

        $pendingEmployees = Employee::with('person')
            ->where('status', 'active')
            ->whereNotNull('hire_date')
            ->where('hire_date', '<=', Carbon::today()->subYear())
            ->whereDoesntHave('vacations', fn ($q) => $q->whereIn('status', ['taken', 'approved']))
            ->get()
            ->filter(function ($employee) {
                if (! $this->search) {
                    return true;
                }
                $s = strtolower($this->search);
                return str_contains(strtolower($employee->full_name), $s)
                    || str_contains($employee->person?->ci ?? '', $s);
            })
            ->map(function ($employee) {
                $years = Carbon::parse($employee->hire_date)->diffInYears(Carbon::today());
                $vacationDays = match (true) {
                    $years > 10 => 30,
                    $years >= 5 => 20,
                    default => 15,
                };
                return (object) [
                    'id' => $employee->id,
                    'ci' => $employee->person?->ci ?? '-',
                    'full_name' => $employee->full_name,
                    'tenure' => $employee->tenure,
                    'vacation_days' => $vacationDays,
                ];
            })
            ->values();

        return [
            'headers' => [
                ['index' => 'employee', 'label' => 'Empleado'],
                ['index' => 'ci', 'label' => 'CI'],
                ['index' => 'start_date', 'label' => 'Inicio'],
                ['index' => 'end_date', 'label' => 'Fin'],
                ['index' => 'days', 'label' => 'Días'],
                ['index' => 'action', 'label' => 'Acción'],
            ],
            'rows' => $rows,
            'pendingEmployees' => $pendingEmployees,
        ];
    }

    public function delete($vacationId): void
    {
        $vacationId = is_array($vacationId) ? (int) ($vacationId[0] ?? 0) : (int) $vacationId;
        $vacation = Vacation::with('employee.person')->findOrFail($vacationId);
        $name = $vacation->employee?->person?->full_name ?? 'Empleado #'.$vacation->employee_id;
        $vacation->delete();

        $this->toast()
            ->expandable(false)
            ->success('Vacación revocada', "La vacación de {$name} fue revocada correctamente")
            ->send();
    }

    public function validateVacation($employeeId): void
    {
        $employeeId = is_array($employeeId) ? (int) ($employeeId[0] ?? 0) : (int) $employeeId;
        $employee = Employee::with('person')->findOrFail($employeeId);
        $years = Carbon::parse($employee->hire_date)->diffInYears(Carbon::today());
        $days = match (true) {
            $years > 10 => 30,
            $years >= 5 => 20,
            default => 15,
        };

        Vacation::create([
            'employee_id' => $employeeId,
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays($days - 1),
            'days' => $days,
            'status' => 'taken',
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Vacación validada', "La vacación de {$employee->full_name} fue registrada correctamente")
            ->send();
    }

    public function revokeVacation($vacationId): void
    {
        $vacationId = is_array($vacationId) ? (int) ($vacationId[0] ?? 0) : (int) $vacationId;
        $vacation = Vacation::with('employee.person')->findOrFail($vacationId);
        $name = $vacation->employee?->person?->full_name ?? 'Empleado #'.$vacation->employee_id;
        $vacation->delete();

        $this->toast()
            ->expandable(false)
            ->success('Vacación revocada', "La vacación de {$name} fue revocada correctamente")
            ->send();
    }

    public function render()
    {
        return view('livewire.rrhh.vacation-component');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
