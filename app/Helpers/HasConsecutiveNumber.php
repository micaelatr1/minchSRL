<?php

namespace App\Helpers;

use App\Models\Movement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// app/Traits/HasConsecutiveNumber.php
// app/Traits/HasConsecutiveNumber.php

trait HasConsecutiveNumber
{
    protected static function bootHasConsecutiveNumber(): void
    {
        static::creating(function ($model) {
            $model->number = static::calcularSiguienteNumero($model);
        });
    }

    private static function calcularSiguienteNumero($model): int
    {
        return DB::transaction(function () use ($model) {
            // La fecha viene del movement relacionado
            $fecha = Movement::find($model->movement_id)?->date
                ?? now();

            $desde = Carbon::parse($fecha)->startOfMonth();
            $hasta = Carbon::parse($fecha)->endOfMonth();

            $query = DB::table(static::tablaDetalle())
                ->join('movements', 'movements.id', '=', static::tablaDetalle().'.movement_id')
                ->whereIn('movements.type', ['D', 'C'])
                ->whereBetween('movements.date', [$desde, $hasta])
                ->lockForUpdate();

            // transactions filtra además por account_id
            if (static::tablaDetalle() === 'transactions') {
                $query->where('transactions.account_id', $model->account_id);
            }

            $max = $query->max(static::tablaDetalle().'.number');

            return ($max ?? 0) + 1;
        });
    }

    abstract protected static function tablaDetalle(): string;
}
