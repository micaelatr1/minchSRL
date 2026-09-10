<?php

namespace App\Livewire\Rrhh;

use App\Models\Employee;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class EmployeeComponent extends Component
{
    use Interactions, WithPagination;

    public $employeeId;

    public $full_name;

    public $ci;

    public $department_id;

    public $code;

    public $position;

    public $base_salary;

    public $hire_date;

    public $status = 'active';

    public ?int $quantity = 10;

    public ?string $search = null;

    protected function rules()
    {
        return [
            'full_name' => 'required|min:5|max:150',
            'ci' => [
                'required',
                'min:4',
                'max:15',
                Rule::unique('people', 'ci')->ignore($this->employeeId, 'id'),
            ],
            'department_id' => 'required|exists:departaments,id',
            'code' => [
                'required',
                'max:30',
                Rule::unique('employees', 'code')->ignore($this->employeeId),
            ],
            'position' => 'required|max:150',
            'base_salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function with(): array
    {
        $employees = Employee::query()
            ->with('person', 'department')
            ->when($this->search, function (Builder $query) {
                return $query->whereHas('person', function ($q) {
                    $q->where('full_name', 'like', "%{$this->search}%")
                        ->orWhere('ci', 'like', "%{$this->search}%");
                })->orWhere('code', 'like', "%{$this->search}%");
            })
            ->paginate($this->quantity)
            ->withQueryString();

        $rows = $employees->through(function ($employee) {
            return (object) [
                'id' => $employee->id,
                'code' => $employee->code,
                'ci' => $employee->person->ci ?? '',
                'full_name' => $employee->person->full_name ?? '',
                'position' => $employee->position,
                'department' => $employee->department?->area ?? '',
                'base_salary' => $employee->base_salary,
                'status' => $employee->status,
                'model' => $employee,
            ];
        });

        return [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'code', 'label' => 'Código'],
                ['index' => 'ci', 'label' => 'CI'],
                ['index' => 'full_name', 'label' => 'Nombre Completo'],
                ['index' => 'position', 'label' => 'Cargo'],
                ['index' => 'department', 'label' => 'Departamento'],
                ['index' => 'base_salary', 'label' => 'Salario Base'],
                ['index' => 'status', 'label' => 'Estado'],
                ['index' => 'action', 'label' => 'Acciones'],
            ],
            'rows' => $rows,
        ];
    }

    public function render()
    {
        return view('livewire.rrhh.employee-component');
    }

    public function store()
    {
        $this->authorize('Crear empleados');

        $this->validate();

        $person = Person::create([
            'full_name' => $this->full_name,
            'ci' => $this->ci,
        ]);

        Employee::create([
            'person_id' => $person->id,
            'department_id' => $this->department_id,
            'code' => $this->code,
            'position' => $this->position,
            'base_salary' => $this->base_salary,
            'hire_date' => $this->hire_date,
            'status' => $this->status,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Registro almacenado', 'El empleado se registró correctamente')
            ->send();

        $this->clear();
    }

    #[On('load::employee')]
    public function edit(Employee $employee)
    {
        $this->employeeId = $employee->id;
        $this->full_name = $employee->person->full_name;
        $this->ci = $employee->person->ci;
        $this->department_id = $employee->department_id;
        $this->code = $employee->code;
        $this->position = $employee->position;
        $this->base_salary = $employee->base_salary;
        $this->hire_date = $employee->hire_date->format('Y-m-d');
        $this->status = $employee->status;
        $this->js("window.\$tsui.open.modal('crud-modal')");
    }

    public function update()
    {
        $this->authorize('Editar empleados');

        $this->validate();

        $employee = Employee::with('person')->find($this->employeeId);

        $employee->person->update([
            'full_name' => $this->full_name,
            'ci' => $this->ci,
        ]);

        $employee->update([
            'department_id' => $this->department_id,
            'code' => $this->code,
            'position' => $this->position,
            'base_salary' => $this->base_salary,
            'hire_date' => $this->hire_date,
            'status' => $this->status,
        ]);

        $this->toast()
            ->expandable(false)
            ->success('Registro actualizado', 'Datos del empleado actualizados correctamente')
            ->send();

        $this->clear();
    }

    public function delete(Employee $employee)
    {
        $this->authorize('Eliminar empleados');

        $employee->delete();

        $this->toast()
            ->expandable(false)
            ->success('Registro eliminado', 'El empleado fue eliminado correctamente')
            ->send();
    }

    public function clear()
    {
        $this->resetValidation();
        $this->dispatch('close-modal');
        $this->reset(['employeeId', 'full_name', 'ci', 'department_id', 'code', 'position', 'base_salary', 'hire_date', 'status']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
