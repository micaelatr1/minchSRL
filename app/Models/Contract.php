<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'total_amount',
        'person_id',
        'type',
        'status',
        'start_date',
        'end_date',
        'file',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $maxCode = DB::table('contracts')->lockForUpdate()->max('code');
            $nextNumber = $maxCode ? (int) substr($maxCode, 5) + 1 : 1;
            $model->code = 'CONT-'.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->movements()->where('type', 'C')->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->paid_amount);
    }

    public function getProgressAttribute(): float
    {
        if ((float) $this->total_amount <= 0) {
            return 0;
        }

        return round(($this->paid_amount / (float) $this->total_amount) * 100, 2);
    }
}
