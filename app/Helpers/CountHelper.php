<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class CountHelper
{
    public function getNextNumberForType($type)
    {
        return DB::transaction(function () use ($type) {
            $sequence = DB::table('transactions')
                ->where('type', $type)
                ->lockForUpdate() // Evita duplicados en concurrencia
                ->first();

            if (! $sequence) {
                // Inicializar con 0
                $lastNumber = 0;
                DB::table('transactions')->insert([
                    'type' => $type,
                    'last_number' => 0,
                ]);
            } else {
                $lastNumber = $sequence->last_number;
            }

            $newNumber = $lastNumber + 1;
            DB::table('transactions')
                ->where('type', $type)
                ->update(['last_number' => $newNumber]);

            return str_pad($newNumber, 8, '0', STR_PAD_LEFT);
        });
    }
}
