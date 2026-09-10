<?php

namespace App\Livewire\Reports;

use App\Exports\BankBookReportExport;
use App\Helpers\BankBookReportPdf;
use App\Models\Account;
use App\Models\Movement;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use TallStackUi\Traits\Interactions;

class BankBookReportsComponent extends Component
{
    use Interactions;

    public $fechaInicio;

    public $fechaFin;

    public $cuentaId = '';

    public function mount()
    {
        $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = now()->endOfMonth()->format('Y-m-d');
    }

    public function getCuentasProperty()
    {
        return Account::orderBy('name')->get();
    }

    public function getCuentaOptionsProperty()
    {
        return Account::orderBy('name')
            ->get()
            ->map(fn ($a) => ['label' => $a->name.' ('.$a->account_number.')', 'value' => (string) $a->id])
            ->prepend(['label' => 'Todas', 'value' => ''])
            ->toArray();
    }

    public function getRowsProperty()
    {
        $start = Carbon::parse($this->fechaInicio)->startOfDay();
        $end = Carbon::parse($this->fechaFin)->endOfDay();

        $movements = Movement::whereHas('transaction', function ($q) {
            if (! empty($this->cuentaId)) {
                $q->where('account_id', $this->cuentaId);
            }
        })
            ->with(['transaction' => function ($q) {
                if (! empty($this->cuentaId)) {
                    $q->where('account_id', $this->cuentaId);
                }
            }, 'person'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('id')
            ->select('*')
            ->selectRaw('SUM(CASE WHEN type IN ("D", "B") THEN amount ELSE -amount END) OVER (ORDER BY date, id) as balance')
            ->get();

        return $movements->values()->map(function ($m, $index) {
            return [
                'number' => $index + 1,
                'date' => Carbon::parse($m->date)->format('d/m/Y'),
                'description' => $m->description,
                'ref' => $m->transaction?->number_label ?? '',
                'debito' => in_array($m->type, ['D', 'B']) ? (float) $m->amount : 0,
                'credito' => $m->type === 'C' ? (float) $m->amount : 0,
                'saldo' => (float) $m->balance,
            ];
        })->toArray();
    }

    public function exportarPdf()
    {
        $this->authorize('Exportar reportes');

        $filters = [];
        if ($this->fechaInicio && $this->fechaFin) {
            $filters[] = 'Período: '.Carbon::parse($this->fechaInicio)->format('d/m/Y').' - '.Carbon::parse($this->fechaFin)->format('d/m/Y');
        }
        if (! empty($this->cuentaId)) {
            $cuenta = Account::find($this->cuentaId);
            if ($cuenta) {
                $filters[] = 'Cuenta: '.$cuenta->name;
            }
        }

        $pdf = new BankBookReportPdf($this->rows, $filters);
        $content = $pdf->build();

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $pdf->getFilename());
    }

    public function exportarExcel()
    {
        $this->authorize('Exportar reportes');

        return Excel::download(
            new BankBookReportExport($this->rows),
            'reporte_bancos_'.now()->format('Ymd_His').'.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.reports.bank-book-reports-component');
    }
}
