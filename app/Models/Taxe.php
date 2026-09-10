<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxe extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $label = [
        'S' => 'Servicios',
        'G' => 'Bienes',
        'A' => 'Todos',
    ];

    public function retention(): BelongsTo
    {
        return $this->belongsTo(Retention::class);
    }

    public function Label(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->label[$this->type]
        );
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }
}
