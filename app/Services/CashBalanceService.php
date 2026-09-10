<?php

namespace App\Services;

use App\Models\Movement;
use Carbon\Carbon;

class CashBalanceService
{
    public function recalculateFromDate(Carbon|string $date): void
    {
        $modifiedMonth = Carbon::parse($date)->startOfMonth();
        $nextMonth = $modifiedMonth->copy()->addMonth()->startOfMonth();

        $lastBalance = Movement::where('type', 'B')
            ->whereDoesntHave('transaction')
            ->where('date', '<', $modifiedMonth)
            ->orderBy('date', 'desc')
            ->first();

        if ($lastBalance) {
            $cursor = Carbon::parse($lastBalance->date)->startOfMonth()->addMonth();

            while ($cursor < $modifiedMonth) {
                $this->upsertBalanceMovement(
                    $cursor->copy(),
                    $cursor->copy()->subMonth()->startOfMonth(),
                    $cursor->copy()->subMonth()->endOfMonth()
                );
                $cursor->addMonth();
            }
        }

        $this->upsertBalanceMovement(
            $modifiedMonth,
            $modifiedMonth->copy()->subMonth()->startOfMonth(),
            $modifiedMonth->copy()->subMonth()->endOfMonth()
        );

        $this->upsertBalanceMovement(
            $nextMonth,
            $modifiedMonth->copy()->startOfMonth(),
            $modifiedMonth->copy()->endOfMonth()
        );

        $balanceRecords = Movement::where('type', 'B')
            ->whereDoesntHave('transaction')
            ->where('date', '>', $modifiedMonth->copy()->endOfMonth())
            ->orderBy('date')
            ->get();

        foreach ($balanceRecords as $balanceRecord) {
            $balanceMonth = Carbon::parse($balanceRecord->date)->startOfMonth();
            $previousMonthStart = $balanceMonth->copy()->subMonth()->startOfMonth();
            $previousMonthEnd = $balanceMonth->copy()->subMonth()->endOfMonth();

            $balanceRecord->amount = $this->calculateMonthBalance(
                $previousMonthStart,
                $previousMonthEnd
            );
            $balanceRecord->save();
        }
    }

    private function upsertBalanceMovement(Carbon $balanceMonth, Carbon $from, Carbon $to): void
    {
        $amount = $this->calculateMonthBalance($from, $to);

        $existing = Movement::where('type', 'B')
            ->whereDoesntHave('transaction')
            ->whereBetween('date', [
                $balanceMonth->copy()->startOfMonth(),
                $balanceMonth->copy()->endOfMonth(),
            ])
            ->first();

        if ($existing) {
            $existing->amount = $amount;
            $existing->save();

            return;
        }

        Movement::create([
            'date' => $balanceMonth->toDateString(),
            'description' => 'SALDO ANTERIOR',
            'type' => 'B',
            'amount' => $amount,
            'person_id' => null,
            'user_id' => auth()->id(),
        ]);
    }

    private function calculateMonthBalance(Carbon $from, Carbon $to): float
    {
        $hasBalanceInRange = Movement::where('type', 'B')
            ->whereDoesntHave('transaction')
            ->whereBetween('date', [$from, $to])
            ->exists();

        $totalDebit = Movement::where(function ($q) {
            $q->whereHas('box')->orWhere(function ($q) {
                $q->where('type', 'B')->whereDoesntHave('transaction');
            });
        })
            ->whereIn('type', ['D', 'B'])
            ->whereBetween('date', [$from, $to])
            ->sum('amount');

        $totalCredit = Movement::where(function ($q) {
            $q->whereHas('box')->orWhere(function ($q) {
                $q->where('type', 'B')->whereDoesntHave('transaction');
            });
        })
            ->where('type', 'C')
            ->whereBetween('date', [$from, $to])
            ->sum('amount');

        $balance = (float) ($totalDebit - $totalCredit);

        if (! $hasBalanceInRange) {
            $previousBalance = Movement::where('type', 'B')
                ->whereDoesntHave('transaction')
                ->where('date', '<', $from)
                ->orderBy('date', 'desc')
                ->value('amount');

            if ($previousBalance) {
                $balance += (float) $previousBalance;
            }
        }

        return $balance;
    }
}
