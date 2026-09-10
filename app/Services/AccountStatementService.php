<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Movement;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class AccountStatementService
{
    public const TYPES = ['supplier', 'customer'];

    public function resolve(string $type, int $id): Supplier|Customer
    {
        return match ($type) {
            'supplier' => Supplier::with('person')->findOrFail($id),
            'customer' => Customer::with('person')->findOrFail($id),
            default => throw new InvalidArgumentException("Tipo de titular inválido: {$type}"),
        };
    }

    public function holderLabel(string $type): string
    {
        return match ($type) {
            'supplier' => 'Proveedor',
            'customer' => 'Cliente',
            default => 'Titular',
        };
    }

    public function movementsForPerson(int $personId): Builder
    {
        return Movement::query()
            ->with(['box', 'transaction', 'contract'])
            ->where('person_id', $personId)
            ->orderBy('date')
            ->orderBy('id')
            ->select('*')
            ->selectRaw('SUM(CASE WHEN type IN ("D", "B") THEN amount ELSE -amount END) OVER (ORDER BY date, id) as balance');
    }

    public function totalsForPerson(int $personId): array
    {
        $amountD = (float) Movement::where('person_id', $personId)->whereIn('type', ['D', 'B'])->sum('amount');
        $amountC = (float) Movement::where('person_id', $personId)->where('type', 'C')->sum('amount');

        return [
            'amountD' => $amountD,
            'amountC' => $amountC,
            'balance' => $amountD - $amountC,
        ];
    }

    public function documentReference(Movement $movement): string
    {
        if ($movement->box) {
            return $movement->box->number_label;
        }

        if ($movement->transaction) {
            return $movement->transaction->number_label;
        }

        return '';
    }
}
