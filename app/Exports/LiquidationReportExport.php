<?php

namespace App\Exports;

class LiquidationReportExport extends BaseReportExport
{
    /** Columnas cuyo total sí tiene sentido sumar (índices 0-based de $columns). */
    private const TOTAL_INDICES = [4, 7, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->title = 'CUADRO PRODUCCION';
        $this->subtitle = '(EXPRESADO EN DÓLARES AMERICANOS Y BOLIVIANOS)';
        $this->narrowFirstColumn = false;
        $this->columns = [
            ['label' => 'N°', 'align' => 'L'],
            ['label' => 'FECHA', 'align' => 'L'],
            ['label' => 'Nº LOTE', 'align' => 'L'],
            ['label' => 'PROVEEDOR', 'align' => 'L'],
            ['label' => 'PESO BRUTO TMH', 'align' => 'R'],
            ['label' => 'HUMEDAD %', 'align' => 'L'],
            ['label' => 'MERMA %', 'align' => 'L'],
            ['label' => 'PESO NETO TMS', 'align' => 'R'],
            ['label' => 'AG (g/t)', 'align' => 'L'],
            ['label' => 'PB %', 'align' => 'L'],
            ['label' => 'MAQ.', 'align' => 'L'],
            ['label' => 'BASE', 'align' => 'L'],
            ['label' => 'REF.', 'align' => 'L'],
            ['label' => 'ESC.', 'align' => 'L'],
            ['label' => 'PB', 'align' => 'L'],
            ['label' => 'AG', 'align' => 'L'],
            ['label' => 'VALOR BRUTO USD', 'align' => 'R'],
            ['label' => 'RM. AG. Bs', 'align' => 'R'],
            ['label' => 'RM. PB Bs', 'align' => 'R'],
            ['label' => 'C.N.S. Bs', 'align' => 'R'],
            ['label' => 'COMIBOL Bs', 'align' => 'R'],
            ['label' => 'FEDECOMIN Bs', 'align' => 'R'],
            ['label' => 'FENCOMIN Bs', 'align' => 'R'],
            ['label' => 'APORTE COOP. Bs', 'align' => 'R'],
            ['label' => 'TOTAL DEDUCC Bs', 'align' => 'R'],
            ['label' => 'LIQUIDO PAGABLE Bs', 'align' => 'R'],
            ['label' => 'VALOR LIQ. Bs', 'align' => 'R'],
        ];
    }

    protected function mapRow($item): array
    {
        return [
            $item['n'] ?? '',
            $item['date'] ?? '',
            $item['lote'] ?? '',
            $item['proveedor'] ?? '',
            $item['tmh'] ?? 0,
            $item['humedad'] ?? 0,
            $item['merma'] ?? 0,
            $item['tmns'] ?? 0,
            $item['agGt'] ?? 0,
            $item['pbPct'] ?? 0,
            $item['maquila'] ?? 0,
            $item['base'] ?? 0,
            $item['ref'] ?? 0,
            $item['esc'] ?? 0,
            $item['pb'] ?? 0,
            $item['ag'] ?? 0,
            $item['valorBrutoUsd'] ?? 0,
            $item['rmAg'] ?? 0,
            $item['rmPb'] ?? 0,
            $item['cns'] ?? 0,
            $item['comibol'] ?? 0,
            $item['fedecomin'] ?? 0,
            $item['fencomin'] ?? 0,
            $item['aporteCoop'] ?? 0,
            $item['totalDeducc'] ?? 0,
            $item['liquidoPagable'] ?? 0,
            $item['valorLiq'] ?? 0,
        ];
    }

    protected function calculateTotals(): array
    {
        $totals = array_fill(0, count($this->columns) - 1, null);

        foreach (self::TOTAL_INDICES as $i) {
            $sum = 0;
            foreach ($this->data as $item) {
                $sum += (float) ($this->mapRow($item)[$i] ?? 0);
            }
            $totals[$i - 1] = $sum;
        }

        return $totals;
    }
}
