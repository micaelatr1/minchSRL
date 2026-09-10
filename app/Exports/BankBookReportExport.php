<?php

namespace App\Exports;

class BankBookReportExport extends BaseReportExport
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->title = 'LIBRO DE BANCOS';
        $this->subtitle = '(EXPRESADO EN BOLIVIANOS)';
        $this->columns = [
            ['label' => 'Nº', 'w' => 10, 'align' => 'L'],
            ['label' => 'Fecha', 'w' => 14, 'align' => 'L'],
            ['label' => 'Descripción', 'w' => 30, 'align' => 'L'],
            ['label' => 'Dcto.Ref.', 'w' => 12, 'align' => 'L'],
            ['label' => 'Débito', 'w' => 14, 'align' => 'R'],
            ['label' => 'Crédito', 'w' => 14, 'align' => 'R'],
            ['label' => 'Saldo', 'w' => 14, 'align' => 'R'],
        ];
    }

    protected function mapRow($item): array
    {
        return [
            $item['number'] ?? '',
            $item['date'] ?? '',
            $item['description'] ?? '',
            $item['ref'] ?? '',
            $item['debito'] ?? 0,
            $item['credito'] ?? 0,
            $item['saldo'] ?? 0,
        ];
    }
}
