<?php

namespace App\Livewire\Reports;

use App\Exports\LiquidationReportExport;
use App\Models\Liquidation;
use App\Services\LiquidationCalculator;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class LiquidationReportsComponent extends Component
{
    public $mes;

    public $metal = '';

    public function mount(): void
    {
        $this->mes = now()->format('Y-m');
    }

    public function getMesOptionsProperty(): array
    {
        $options = [];
        $cursor = Carbon::now()->startOfMonth();

        for ($i = 0; $i < 36; $i++) {
            $options[] = [
                'label' => ucfirst($cursor->locale('es')->isoFormat('MMMM YYYY')),
                'value' => $cursor->format('Y-m'),
            ];
            $cursor->subMonth();
        }

        return $options;
    }

    public function getMetalOptionsProperty(): array
    {
        return [
            ['label' => 'Todos los metales', 'value' => ''],
            ['label' => 'Zn (Zinc)', 'value' => 'zn'],
            ['label' => 'Pb (Plomo)', 'value' => 'pb'],
        ];
    }

    public function getPeriodoProperty(): string
    {
        if (empty($this->mes)) {
            return '';
        }

        $start = Carbon::parse($this->mes.'-01');

        return ucfirst($start->locale('es')->isoFormat('MMMM YYYY'));
    }

    public function getRowsProperty(): array
    {
        if (empty($this->mes)) {
            return [];
        }

        $start = Carbon::parse($this->mes.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $query = Liquidation::query()
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('id');

        if (! empty($this->metal)) {
            $query->where('metal', $this->metal);
        }

        return $query->get()
            ->map(fn (Liquidation $l, int $index) => $this->mapRow($l, $index))
            ->values()
            ->toArray();
    }

    public function getTotalsProperty(): array
    {
        $totals = array_fill_keys([
            'tmh', 'tmns', 'valorBrutoUsd', 'rmAg', 'rmPb', 'cns', 'comibol',
            'fedecomin', 'fencomin', 'aporteCoop', 'totalDeducc', 'liquidoPagable', 'valorLiq',
        ], 0.0);

        foreach ($this->rows as $row) {
            foreach ($totals as $key => $value) {
                $totals[$key] = $value + (float) ($row[$key] ?? 0);
            }
        }

        return $totals;
    }

    public function exportarExcel()
    {
        $this->authorize('Exportar reportes');

        return Excel::download(
            new LiquidationReportExport($this->rows),
            'cuadro_produccion_'.str_replace('-', '', $this->mes).'_'.now()->format('His').'.xlsx'
        );
    }

    private function mapRow(Liquidation $l, int $index): array
    {
        $c = LiquidationCalculator::calculate($l);
        $isPb = $l->metal === 'pb';

        return [
            'id' => $l->id,
            'n' => $index + 1,
            'date' => $l->date->format('d/m/Y'),
            'lote' => $l->lote,
            'proveedor' => $l->full_name,
            'metal' => $l->metal,
            'metalLabel' => $isPb ? 'Pb' : 'Zn',
            'tmh' => (float) $l->tmh,
            'humedad' => (float) $l->h2o,
            'merma' => (float) $l->merma,
            'tmns' => $c['tmns'],
            'agGt' => $c['ag'],
            'pbPct' => $c['ley'],
            'maquila' => (float) $l->maquila,
            'base' => (float) $l->base,
            'ref' => (float) $l->refinacion,
            'esc' => (float) $l->base_percentage,
            'pb' => $isPb ? (float) $l->market_pb : (float) $l->market_zn,
            'ag' => (float) $l->market_ag,
            'valorBrutoUsd' => $c['totalNetoUSD'],
            'rmAg' => $c['regaliaAgBs'],
            'rmPb' => $c['regaliaMetalBs'],
            'cns' => $c['cnsBs'],
            'comibol' => $c['comibolBs'],
            'fedecomin' => $c['fedecominBs'],
            'fencomin' => $c['fencominBs'],
            'aporteCoop' => $c['aporteCoopBs'],
            'totalDeducc' => $c['totalDeducc'],
            'liquidoPagable' => $c['liquidoPagable'],
            'valorLiq' => $c['totalBs'],
        ];
    }

    public function render()
    {
        return view('livewire.reports.liquidation-reports-component');
    }
}
