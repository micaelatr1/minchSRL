<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Payroll extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function totalPaid(): float
    {
        return (float) $this->details()->sum('net_salary');
    }

    private function getAccount()
    {
        $dept = Departament::where('area', 'Recursos Humanos')->first();
        $account = $dept?->account;

        if (! $account) {
            throw new \RuntimeException('No hay una cuenta bancaria asignada al departamento Recursos Humanos.');
        }

        return $account;
    }

    private function createPaymentMovement(Employee $employee, float $amount, int $userId): Movement
    {
        $employeeName = $employee->person?->full_name ?? 'Empleado #'.$employee->id;
        $description = 'Pago planilla '.$this->period.' - '.$employeeName;

        $movement = Movement::create([
            'type' => 'C',
            'amount' => $amount,
            'description' => $description,
            'date' => Carbon::today(),
            'user_id' => $userId,
            'person_id' => $employee->person_id,
        ]);

        Transaction::create([
            'movement_id' => $movement->id,
            'account_id' => $this->getAccount()->id,
            'payment_type' => 'T',
            'number_check' => 'PL-'.$this->id.'-'.$employee->id,
            'description' => $description,
            'date' => Carbon::today(),
        ]);

        return $movement;
    }

    public function payEmployee(Employee $employee, float $baseSalary, int $userId, float $bonuses = 0, float $discounts = 0): PayrollDetail
    {
        $netSalary = $baseSalary + $bonuses - $discounts;

        return DB::transaction(function () use ($employee, $baseSalary, $netSalary, $bonuses, $discounts, $userId) {
            $exists = $this->details()->where('employee_id', $employee->id)->exists();

            if ($exists) {
                throw new \RuntimeException('Este empleado ya fue pagado en esta planilla.');
            }

            $movement = $this->createPaymentMovement($employee, $netSalary, $userId);

            return $this->details()->create([
                'employee_id' => $employee->id,
                'base_salary' => $baseSalary,
                'bonuses' => $bonuses,
                'discounts' => $discounts,
                'net_salary' => $netSalary,
                'movement_id' => $movement->id,
            ]);
        });
    }

    public function updatePayDetail(int $detailId, float $bonuses, float $discounts): PayrollDetail
    {
        return DB::transaction(function () use ($detailId, $bonuses, $discounts) {
            $detail = $this->details()->findOrFail($detailId);
            $netSalary = $detail->base_salary + $bonuses - $discounts;

            $detail->update([
                'bonuses' => $bonuses,
                'discounts' => $discounts,
                'net_salary' => $netSalary,
            ]);

            if ($detail->movement_id) {
                $detail->movement->update(['amount' => $netSalary]);
            }

            return $detail;
        });
    }

    public function deletePayDetail(int $detailId): void
    {
        DB::transaction(function () use ($detailId) {
            $detail = $this->details()->findOrFail($detailId);

            if ($detail->movement_id) {
                $detail->movement->transaction()->delete();
                $detail->movement->delete();
            }

            $detail->delete();
        });
    }

    public function pay(array $employees, int $userId): void
    {
        DB::transaction(function () use ($employees, $userId) {
            foreach ($employees as $data) {
                $employee = $data instanceof Employee ? $data : Employee::findOrFail($data['id']);
                $baseSalary = is_array($data) ? ($data['base_salary'] ?? $employee->base_salary) : $employee->base_salary;
                $bonuses = is_array($data) ? ($data['bonuses'] ?? 0) : 0;
                $discounts = is_array($data) ? ($data['discounts'] ?? 0) : 0;

                $this->payEmployee($employee, (float) $baseSalary, $userId, (float) $bonuses, (float) $discounts);
            }

            $this->update(['status' => 'paid']);
        });
    }
}
