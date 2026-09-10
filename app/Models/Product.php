<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'stock' => 'decimal:2',
            'average_cost' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $max = DB::table('products')->lockForUpdate()->max('code');
            $num = $max ? (int) substr($max, 4) + 1 : 1;
            $model->code = 'PRO-'.str_pad($num, 4, '0', STR_PAD_LEFT);
        });
    }

    public function kardexMovements(): HasMany
    {
        return $this->hasMany(KardexMovement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'materia_prima' => 'Materia Prima',
            'insumo' => 'Insumo',
            'repuesto' => 'Repuesto',
            'combustible' => 'Combustible',
            'otro' => 'Otro',
        };
    }

    public function getUnitLabelAttribute(): string
    {
        return match ($this->unit_of_measure) {
            'kg' => 'Kg',
            'ton' => 'Ton',
            'l' => 'L',
            'u' => 'Unid',
            'm' => 'M',
        };
    }
}
