<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function retention(): BelongsTo
    {
        return $this->belongsTo(Retention::class);
    }

    public function taxe(): BelongsTo
    {
        return $this->belongsTo(Taxe::class);
    }

    public function getamountFormattedAttribute()
    {
        return number_format((float) $this->amount, 2, ',', '.');
    }
}
