<?php

namespace App\Models;

use App\Helpers\NumberHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'description',
        'type',
        'amount',
        'number_vol',
        'person_id',
        'contract_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    public function box(): HasOne
    {
        return $this->hasOne(Box::class);
    }

    public function getCalculateLabelAttribute()
    {
        return NumberHelper::toLiteral($this->amount);
    }

    public function getFormattedLastNumberAttribute()
    {
        return $this->transaction ? $this->transaction->number_check : '';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function getSupplierAttribute()
    {
        if ($this->person?->supplier) {
            return $this->person->supplier;
        }

        return $this->person;
    }

    public function getdateLabelAttribute()
    {
        Carbon::setLocale('es');

        $fecha = Carbon::parse($this->date);
        $literal = $fecha->translatedFormat('d \d\\e F \d\\e Y');

        return $literal;
    }
}
