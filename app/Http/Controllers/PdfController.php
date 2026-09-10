<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Contract;
use App\Models\Liquidation;
use App\Models\Movement;
use App\Models\Retention;
use App\Services\AccountStatementService;
use Carbon\Carbon;
use Mpdf\Mpdf;

class PdfController extends Controller
{
    public function retentionForm($id)
    {

        $retention = Retention::find($id);

        // 2. Renderizar la vista Blade a HTML
        $html = view('PDF.retentionForm', ['retention' => $retention])->render();

        // 3. Crear instancia de mPDF y generar el PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);
        $mpdf->WriteHTML($html);

        // 4. Mostrar el PDF en el navegador (stream)
        return $mpdf->Output('recibo-rentecion.pdf', 'I');
    }

    public function transactionAccount($start, $end, $id)
    {
        $dateT = Carbon::createFromFormat('Y-m-d H:i:s', $start)->format('Y-m');
        Carbon::setLocale('es');
        $dateT = Carbon::parse($dateT);
        $dateT = ucfirst($dateT->translatedFormat('F')).' '.$dateT->format('Y');
        $transactions = Movement::whereHas('transaction', function ($query) use ($id) {
            $query->where('account_id', $id);
        })
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->select('*')
            ->selectRaw('SUM(CASE WHEN type IN ("D", "B") THEN amount ELSE -amount END) OVER (ORDER BY date) as balance')
            ->get();
        $account = Account::find($id);
        $moneda = $account->currency_type == 'BOB' ? 'BOLIVIANOS' : ($account->currency_type == 'USD' ? 'DOLARES' : 'EUROS');
        $html = view(
            'PDF.transactionAccount',
            [
                'transactions' => $transactions,
                'moneda' => $moneda,
                'dateT' => $dateT,
                'account' => $account,
            ]
        )->render();

        // 3. Crear instancia de mPDF y generar el PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);
        $mpdf->WriteHTML($html);

        // 4. Mostrar el PDF en el navegador (stream)
        return $mpdf->Output('transaction.pdf', 'I');
    }

    public function receiptTransaction($id)
    {
        $transaction = Movement::with(['person', 'transaction', 'transaction.account'])->find($id);
        $html = view('PDF.receipt', ['transaction' => $transaction])->render();

        // 3. Crear instancia de mPDF y generar el PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [215.9, 279.4],  // Carta en mm
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);
        $mpdf->WriteHTML($html);

        // 4. Mostrar el PDF en el navegador (stream)
        return $mpdf->Output('recibo.pdf', 'I');
    }

    public function receiptBox($id)
    {
        $transaction = Movement::with(['person', 'box'])->find($id);
        $html = view('PDF.receiptBox', ['transaction' => $transaction])->render();

        // 3. Crear instancia de mPDF y generar el PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [215.9, 279.4],  // Carta en mm
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);
        $mpdf->WriteHTML($html);

        // 4. Mostrar el PDF en el navegador (stream)
        return $mpdf->Output('recibo-caja.pdf', 'I');
    }

    public function accountStatement(string $type, int $id)
    {
        abort_unless(in_array($type, AccountStatementService::TYPES, true), 404);

        $service = app(AccountStatementService::class);
        $holder = $service->resolve($type, $id);
        $accountStatement = $service->movementsForPerson($holder->person_id)->get();

        $html = view('PDF.account-statement', [
            'accountStatement' => $accountStatement,
            'customer' => $holder,
            'holderLabel' => $service->holderLabel($type),
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);
        $mpdf->WriteHTML($html);

        return $mpdf->Output('estado-cuenta-'.$type.'-'.$id.'.pdf', 'I');
    }

    public function accountBox()
    {
        $date = request('date', now()->format('Y-m'));

        $start = Carbon::createFromFormat('Y-m', $date)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        Carbon::setLocale('es');
        $periodLabel = ucfirst($start->translatedFormat('F \d\e Y'));

        $accountBox = Movement::where(function ($q) {
            $q->whereHas('box')->orWhere(function ($q) {
                $q->where('type', 'B')->whereDoesntHave('transaction');
            });
        })
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('id')
            ->select('*')
            ->selectRaw('SUM(CASE WHEN type IN ("D", "B") THEN amount ELSE -amount END) OVER (ORDER BY date, id) as balance')
            ->get();
        $html = view(
            'PDF.account-box',
            [
                'accountBox' => $accountBox,
                'periodLabel' => $periodLabel,
            ]
        )->render();

        // 3. Crear instancia de mPDF y generar el PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);
        $mpdf->WriteHTML($html);

        // 4. Mostrar el PDF en el navegador (stream)
        return $mpdf->Output('caja-'.$date.'.pdf', 'I');
    }

    public function contractPdf(Contract $contract)
    {
        $contract->load(['person', 'movements.box', 'movements.transaction']);

        $html = view('PDF.contract', compact('contract'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [215.9, 279.4],
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);
        $mpdf->WriteHTML($html);

        return $mpdf->Output('contrato-'.$contract->code.'.pdf', 'I');
    }

    public function liquidationPdf(Liquidation $liquidation)
    {
        $liquidation->load('customer');

        $html = view('PDF.liquidation', ['l' => $liquidation])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);
        $mpdf->WriteHTML($html);

        return $mpdf->Output('liquidacion-'.$liquidation->lote.'.pdf', 'I');
    }
}
