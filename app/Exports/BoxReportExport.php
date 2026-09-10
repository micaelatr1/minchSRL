<?php

namespace App\Exports;

class BoxReportExport extends BaseReportExport
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->title = 'LIBRO DE CAJA';
        $this->subtitle = '(EXPRESADO EN BOLIVIANOS)';
        $this->columns = [
            ['label' => 'Nº', 'w' => 10, 'align' => 'L'],
            ['label' => 'Fecha', 'w' => 14, 'align' => 'L'],
            ['label' => 'Descripción', 'w' => 35, 'align' => 'L'],
            ['label' => 'Debe', 'w' => 14, 'align' => 'R'],
            ['label' => 'Haber', 'w' => 14, 'align' => 'R'],
            ['label' => 'Saldo', 'w' => 14, 'align' => 'R'],
        ];
    }

    protected function mapRow($item): array
    {
        return [
            $item['number'] ?? '',
            $item['date'] ?? '',
            $item['description'] ?? '',
            $item['debe'] ?? 0,
            $item['haber'] ?? 0,
            $item['saldo'] ?? 0,
        ];
    }
}
