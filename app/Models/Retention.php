<?php

namespace App\Models;

use App\Helpers\NumberHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retention extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function taxes(): HasMany
    {
        return $this->hasMany(Taxe::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getcalculateTotalAttribute()
    {
        return $this->discounts()->sum('amount') + $this->amount;
    }

    public function getamountFormattedAttribute()
    {
        return number_format((float) $this->amount, 2, ',', '.');
    }

    public function getcalculateTotalFormattedAttribute()
    {
        return number_format((float) $this->calculate_total, 2, ',', '.');
    }

    public function getcalculateLabelAttribute()
    {
        $literal = NumberHelper::toLiteral($this->discounts()->sum('amount') + $this->amount);

        return $literal;
    }

    public function getdateLabelAttribute()
    {
        Carbon::setLocale('es');

        $fecha = Carbon::parse($this->date);
        $literal = $fecha->translatedFormat('d \d\\e F \d\\e Y');

        return $literal;
    }

    public function getdateCodeAttribute()
    {
        $meses = [
            '01' => 'ENE',
            '02' => 'FEB',
            '03' => 'MAR',
            '04' => 'ABR',
            '05' => 'MAY',
            '06' => 'JUN',
            '07' => 'JUL',
            '08' => 'AGO',
            '09' => 'SEP',
            '10' => 'OCT',
            '11' => 'NOV',
            '12' => 'DIC',
        ];
        $mesNumero = Carbon::parse($this->date)->format('m');
        $abreviatura = $meses[$mesNumero];

        return $abreviatura.'-'.$this->code;
    }
}
