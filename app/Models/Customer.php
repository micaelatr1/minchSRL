<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class, 'person_id', 'person_id');
    }

    public function getFullNameAttribute(): ?string
    {
        return $this->person?->full_name;
    }

    public function getCiAttribute(): ?string
    {
        return $this->person?->ci;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->person?->phone;
    }
}
