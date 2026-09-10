<?php

namespace App\Services;

use App\Models\Person;
use App\Models\Supplier;

class PersonSupplierService
{
    public function resolve(string $ci, string $fullName, ?string $phone = null): Supplier
    {
        $person = Person::firstOrCreate(
            ['ci' => $ci],
            ['full_name' => $fullName, 'phone' => $phone]
        );

        if (! $person->wasRecentlyCreated) {
            $person->update(array_filter([
                'full_name' => $fullName,
                'phone' => $phone,
            ], fn ($value) => $value !== null));
        }

        return Supplier::firstOrCreate(
            ['person_id' => $person->id],
            ['description' => 'Proveedor registrado automáticamente']
        );
    }
}
