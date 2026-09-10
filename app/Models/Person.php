<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $fillable = ['ci', 'full_name', 'phone'];

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class, 'person_id');
    }
}
