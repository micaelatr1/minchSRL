<?php

namespace App\Http\Controllers;

use App\Exports\RetentionsExport;
use App\Exports\TransactionAccountExport;
use App\Models\Account;
use App\Models\Movement;
use App\Models\Retention;
use App\Models\Taxe;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function retentionMonth($date, $type)
    {
        $start = Carbon::createFromFormat('Y-m', $date)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $retentions = Retention::with(['supplier', 'discounts'])
            ->whereBetween('date', [$start, $end])
            ->where('type', $type)
            ->get();
        $taxes = Taxe::where('type', $type)->orWhere('type', 'A')->get();

        return Excel::download(new RetentionsExport($retentions, $taxes, $type), 'retenciones_'.now()->format('Ymd_His').'.xlsx');
    }

    public function transactionAccount($start, $end, $id)
    {
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $start)->format('Y-m');
        Carbon::setLocale('es');
        $periodLabel = ucfirst(Carbon::parse($startDate)->translatedFormat('F')).' '.Carbon::parse($startDate)->format('Y');

        $transactions = Movement::whereHas('transaction', function ($query) use ($id) {
            $query->where('account_id', $id);
        })
            ->with(['transaction', 'person'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('id')
            ->select('*')
            ->selectRaw('SUM(CASE WHEN type IN ("D", "B") THEN amount ELSE -amount END) OVER (ORDER BY date, id) as balance')
            ->get();

        $account = Account::find($id);
        $moneda = $account->currency_type == 'BOB' ? 'BOLIVIANOS' : ($account->currency_type == 'USD' ? 'DOLARES' : 'EUROS');

        return Excel::download(
            new TransactionAccountExport($transactions, $account->name, $account->account_number, $moneda, $periodLabel),
            'libro_bancos_'.$account->account_number.'_'.now()->format('Ymd_His').'.xlsx'
        );
    }
}
