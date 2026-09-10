<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Box extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'movement_id'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $movement = Movement::find($model->movement_id);
            if (! $movement) {
                return;
            }

            $type = $movement->type;
            $fecha = Carbon::parse($movement->date);
            $desde = $fecha->copy()->startOfMonth();
            $hasta = $fecha->copy()->endOfMonth();

            $maxNumber = DB::table('boxes')
                ->join('movements', 'movements.id', '=', 'boxes.movement_id')
                ->where('movements.type', $type)
                ->whereBetween('movements.date', [$desde, $hasta])
                ->lockForUpdate()
                ->max('boxes.number');

            $model->number = ($maxNumber ?? 0) + 1;
        });
    }

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }

    public function getNumberLabelAttribute(): string
    {
        return 'DOC-'.str_pad($this->number, 8, '0', STR_PAD_LEFT);
    }

    public function getFormattedLastNumberAttribute(): string
    {
        return str_pad($this->number, 8, '0', STR_PAD_LEFT);
    }
}
