<?php

namespace App\Exports;

class RetentionReportExport extends BaseReportExport
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->narrowFirstColumn = false;
        $this->title = 'REPORTE DE RETENCIONES';
        $this->subtitle = '(EXPRESADO EN BOLIVIANOS)';
        $this->columns = [
            ['label' => 'Código', 'w' => 14, 'align' => 'L'],
            ['label' => 'Fecha', 'w' => 14, 'align' => 'L'],
            ['label' => 'Proveedor', 'w' => 12, 'align' => 'L'],
            ['label' => 'Tipo', 'w' => 8, 'align' => 'L'],
            ['label' => 'NIT/CI', 'w' => 16, 'align' => 'L'],
            ['label' => 'Monto', 'w' => 14, 'align' => 'R'],
            ['label' => 'Dctos.', 'w' => 14, 'align' => 'R'],
            ['label' => 'Total', 'w' => 14, 'align' => 'R'],
        ];
    }

    protected function mapRow($item): array
    {
        return [
            $item['code'] ?? '',
            $item['date'] ?? '',
            $item['supplier'] ?? '',
            $item['type'] ?? '',
            $item['nit'] ?? '',
            $item['amount'] ?? 0,
            $item['discounts'] ?? 0,
            $item['total'] ?? 0,
        ];
    }
}
