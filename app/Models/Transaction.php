<?php

namespace App\Models;

use App\Helpers\NumberHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $movement = Movement::find($model->movement_id);
            if (! $movement) {
                return;
            }

            $date = Carbon::parse($movement->date);
            $type = $movement->type;

            $yearStart = $date->month >= 10
                ? $date->copy()->startOfYear()->addMonths(9)
                : $date->copy()->startOfYear()->subMonths(3);

            $yearEnd = $yearStart->copy()->addYear()->subDay();

            $maxNumber = DB::table('transactions')
                ->join('movements', 'movements.id', '=', 'transactions.movement_id')
                ->where('movements.type', $type)
                ->whereBetween('movements.date', [$yearStart, $yearEnd])
                ->lockForUpdate()
                ->max('transactions.number');

            $model->number = ($maxNumber ?? 0) + 1;
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movement(): BelongsTo
    {
        return $this->belongsTo(Movement::class);
    }

    public function getcalculateLabelAttribute()
    {
        $literal = NumberHelper::toLiteral($this->amount);

        return $literal;
    }

    public function getdateLabelAttribute()
    {
        Carbon::setLocale('es');

        $fecha = Carbon::parse($this->date);
        $literal = $fecha->translatedFormat('d \d\\e F \d\\e Y');

        return $literal;
    }

    public function getFormattedLastNumberAttribute(): string
    {
        return str_pad($this->number, 8, '0', STR_PAD_LEFT);
    }

    public function getNumberLabelAttribute(): string
    {
        if ($this->number_check) {
            $prefix = $this->payment_type === 'T' ? 'T-' : 'CH-';

            return $prefix.$this->number_check;
        }

        return 'DOC-'.str_pad($this->number, 8, '0', STR_PAD_LEFT);
    }
}
