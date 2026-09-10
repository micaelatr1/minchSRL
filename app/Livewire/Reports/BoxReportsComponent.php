<?php

namespace App\Livewire\Reports;

use App\Exports\BoxReportExport;
use App\Helpers\BoxReportPdf;
use App\Models\Movement;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use TallStackUi\Traits\Interactions;

class BoxReportsComponent extends Component
{
    use Interactions;

    public $fechaInicio;

    public $fechaFin;

    public $tipoMovimiento = '';

    public function mount()
    {
        $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = now()->endOfMonth()->format('Y-m-d');
    }

    public function getRowsProperty()
    {
        $start = Carbon::parse($this->fechaInicio)->startOfDay();
        $end = Carbon::parse($this->fechaFin)->endOfDay();

        $movements = Movement::where(function ($q) {
            $q->whereHas('box')->orWhere(function ($q) {
                $q->where('type', 'B')->whereDoesntHave('transaction');
            });
        })
            ->with(['box', 'person'])
            ->whereBetween('date', [$start, $end]);

        if (! empty($this->tipoMovimiento)) {
            $movements->where('type', $this->tipoMovimiento);
        }

        $movements = $movements->orderBy('date')
            ->orderBy('id')
            ->select('*')
            ->selectRaw('SUM(CASE WHEN type IN ("D", "B") THEN amount ELSE -amount END) OVER (ORDER BY date, id) as balance')
            ->get();

        return $movements->values()->map(function ($m, $index) {
            return [
                'number' => $index + 1,
                'date' => Carbon::parse($m->date)->format('d/m/Y'),
                'description' => $m->description,
                'debe' => in_array($m->type, ['D', 'B']) ? (float) $m->amount : 0,
                'haber' => $m->type === 'C' ? (float) $m->amount : 0,
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
        if (! empty($this->tipoMovimiento)) {
            $label = $this->tipoMovimiento === 'D' ? 'Debe' : 'Haber';
            $filters[] = 'Tipo: '.$label;
        }

        $pdf = new BoxReportPdf($this->rows, $filters);
        $content = $pdf->build();

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $pdf->getFilename());
    }

    public function exportarExcel()
    {
        $this->authorize('Exportar reportes');

        return Excel::download(
            new BoxReportExport($this->rows),
            'reporte_caja_'.now()->format('Ymd_His').'.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.reports.box-reports-component');
    }
}
