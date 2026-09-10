<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Cooperative;
use App\Models\Movement;
use App\Models\Retention;
use App\Models\Supplier;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $selectedMonth;

    public $selectedYear;

    public function mount()
    {
        $now = Carbon::now();
        $this->selectedMonth = $now->month;
        $this->selectedYear = $now->year;
    }

    public function render()
    {
        $now = Carbon::now();
        $filterDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1);
        $monthStart = $filterDate->copy()->startOfMonth();
        $monthEnd = $filterDate->copy()->endOfMonth();

        // ── KPI Cards ──
        $totalSuppliers = Supplier::count();
        $totalCooperatives = Cooperative::count();
        $totalMovements = Movement::whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count();

        $retencionesMes = Retention::whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count();
        $montoRetencionesMes = Retention::whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount');

        // Cash balance (current month)
        $boxDebit = Movement::whereHas('box')->whereIn('type', ['D', 'B'])->whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount');
        $boxCredit = Movement::whereHas('box')->where('type', 'C')->whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount');
        $saldoCaja = $boxDebit - $boxCredit;

        // Bank balance (current month)
        $bankDebit = Movement::whereHas('transaction')->whereIn('type', ['D', 'B'])->whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount');
        $bankCredit = Movement::whereHas('transaction')->where('type', 'C')->whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('amount');
        $saldoBancos = $bankDebit - $bankCredit;

        // ── Monthly movement chart (12 months from current date, unfiltered) ──
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }

        $monthlyData = $months->map(function ($month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            return [
                'month' => $month->format('M Y'),
                'debit' => Movement::where('type', 'D')->whereBetween('date', [$start, $end])->sum('amount'),
                'credit' => Movement::where('type', 'C')->whereBetween('date', [$start, $end])->sum('amount'),
            ];
        });

        $movementChartData = [
            'categories' => $monthlyData->pluck('month')->toArray(),
            'debit' => $monthlyData->pluck('debit')->toArray(),
            'credit' => $monthlyData->pluck('credit')->toArray(),
        ];

        // ── Balance per account (B opening + D - C of selected month) ──
        $accounts = Account::all();
        $balanceByAccount = [];
        foreach ($accounts as $account) {
            $openingB = $account->movements()
                ->where('type', 'B')
                ->where('date', '<=', $monthEnd)
                ->orderBy('date', 'desc')
                ->orderBy('movements.id', 'desc')
                ->first();

            $monthDebe = $account->movements()
                ->where('type', 'D')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
            $monthHaber = $account->movements()
                ->where('type', 'C')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');

            $label = $account->name.' ('.$account->account_number.')';
            $balanceByAccount[$label] = ($openingB?->amount ?? 0) + $monthDebe - $monthHaber;
        }

        $balanceChartData = [
            'categories' => array_keys($balanceByAccount),
            'series' => array_values($balanceByAccount),
        ];

        // ── Retention pie chart (S vs G amounts in selected month) ──
        $retByType = Retention::whereBetween('date', [$monthStart, $monthEnd])
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $retentionPieData = [
            'labels' => $retByType->keys()->map(fn ($t) => $t === 'S' ? 'Servicios' : 'Bienes')->toArray(),
            'series' => $retByType->values()->map(fn ($v) => (float) $v)->toArray(),
            'colors' => ['#4361EE', '#3A0CA3'],
        ];

        // ── Monthly retention amounts (6 months from current date, unfiltered) ──
        $retMonths = collect();
        for ($i = 5; $i >= 0; $i--) {
            $retMonths->push($now->copy()->subMonths($i));
        }

        $retMonthly = $retMonths->map(function ($month) {
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            return [
                'month' => $month->format('M Y'),
                'total' => Retention::whereBetween('date', [$start, $end])->sum('amount'),
            ];
        });

        $retentionBarData = [
            'categories' => $retMonthly->pluck('month')->toArray(),
            'series' => $retMonthly->pluck('total')->toArray(),
        ];

        // ── Recent movements (unfiltered) ──
        $recentMovements = Movement::with('person', 'transaction')
            ->latest()
            ->limit(5)
            ->get();

        // ── Recent retentions (unfiltered) ──
        $recentRetentions = Retention::with('supplier.person')
            ->latest()
            ->limit(5)
            ->get();

        $this->dispatch('dashboard-updated');

        return view('livewire.dashboard', compact(
            'totalSuppliers',
            'totalCooperatives',
            'totalMovements',
            'retencionesMes',
            'montoRetencionesMes',
            'saldoCaja',
            'saldoBancos',
            'movementChartData',
            'balanceChartData',
            'retentionPieData',
            'retentionBarData',
            'recentMovements',
            'recentRetentions',
        ));
    }
}
