<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Account extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function movements(): HasManyThrough
    {
        return $this->hasManyThrough(
            Movement::class,
            Transaction::class,
            'account_id',
            'id',
            'id',
            'movement_id'
        );
    }

    public function departaments()
    {
        return $this->hasMany(Departament::class);
    }
}
