<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class KardexMovement extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'quantity_in' => 'decimal:4',
            'quantity_out' => 'decimal:4',
            'unit_cost' => 'decimal:4',
            'balance_quantity' => 'decimal:4',
            'balance_avg_cost' => 'decimal:4',
            'balance_total_value' => 'decimal:4',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'purchase' => 'Compra',
            'sale' => 'Venta',
            'adjustment_in' => 'Ajuste (+',
            'adjustment_out' => 'Ajuste (-',
            default => ucfirst($this->type),
        };
    }
}
