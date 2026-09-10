<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'hire_date' => 'date',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Departament::class);
    }

    public function payrollDetails(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->person?->full_name ?? '-';
    }

    public function getTenureAttribute(): string
    {
        if (! $this->hire_date) {
            return 'N/A';
        }

        $diff = Carbon::parse($this->hire_date)->diff(Carbon::today());

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = $diff->y.' año'.($diff->y !== 1 ? 's' : '');
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m.' mes'.($diff->m !== 1 ? 'es' : '');
        }
        $parts[] = $diff->d.' día'.($diff->d !== 1 ? 's' : '');

        return implode(', ', $parts);
    }
}
