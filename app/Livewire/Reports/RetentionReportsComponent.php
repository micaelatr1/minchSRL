<?php

namespace App\Livewire\Reports;

use App\Exports\RetentionReportExport;
use App\Helpers\RetentionReportPdf;
use App\Models\Retention;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use TallStackUi\Traits\Interactions;

class RetentionReportsComponent extends Component
{
    use Interactions;

    public $fechaInicio;

    public $fechaFin;

    public $tipo = '';

    public function mount()
    {
        $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = now()->endOfMonth()->format('Y-m-d');
    }

    public function getRowsProperty()
    {
        $start = Carbon::parse($this->fechaInicio)->startOfDay();
        $end = Carbon::parse($this->fechaFin)->endOfDay();

        $retentions = Retention::with(['discounts', 'supplier.person'])
            ->withSum('discounts as total_discounts', 'amount')
            ->whereBetween('date', [$start, $end]);

        if (! empty($this->tipo)) {
            $retentions->where('type', $this->tipo);
        }

        return $retentions->orderBy('date')->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'code' => $r->code,
                'date' => Carbon::parse($r->date)->format('d/m/Y'),
                'supplier' => $r->supplier?->full_name ?? $r->supplier?->person?->full_name ?? '-',
                'type' => $r->type,
                'nit' => $r->supplier?->ci ?? '-',
                'amount' => (float) $r->amount,
                'discounts' => (float) ($r->total_discounts ?? 0),
                'total' => (float) $r->calculate_total,
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
        if (! empty($this->tipo)) {
            $filters[] = 'Tipo: '.($this->tipo === 'S' ? 'Servicios' : 'Bienes');
        }

        $pdf = new RetentionReportPdf($this->rows, $filters);
        $content = $pdf->build();

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $pdf->getFilename());
    }

    public function exportarExcel()
    {
        $this->authorize('Exportar reportes');

        return Excel::download(
            new RetentionReportExport($this->rows),
            'reporte_retenciones_'.now()->format('Ymd_His').'.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.reports.retention-reports-component');
    }
}
