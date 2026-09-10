<?php

namespace App\Helpers;

class RetentionReportPdf extends ReportPdf
{
    protected array $data;

    public function __construct(array $data, array $filters)
    {
        parent::__construct('P', 'mm', 'LETTER');
        $this->data = $data;
        $this->setReportTitle('REPORTE DE RETENCIONES');
        $this->setSubtitle('(EXPRESADO EN BOLIVIANOS)');
        $this->setFilters($filters);
        $this->setColumns([
            ['label' => 'Numero',    'w' => 18, 'align' => 'L'],
            ['label' => 'Fecha',     'w' => 18, 'align' => 'L'],
            ['label' => 'Proveedor', 'w' => 48, 'align' => 'L'],
            ['label' => 'Tipo',      'w' => 10, 'align' => 'L'],
            ['label' => 'NIT/CI',    'w' => 25, 'align' => 'L'],
            ['label' => 'Monto',     'w' => 24, 'align' => 'R'],
            ['label' => 'Dctos.',    'w' => 24, 'align' => 'R'],
            ['label' => 'Total',     'w' => 24, 'align' => 'R'],
        ]);
    }

    public function build(): string
    {
        $this->AliasNbPages();
        $this->AddPage();

        $this->tableHeader();

        $totalMonto = 0;
        $totalDctos = 0;
        $totalGeneral = 0;

        foreach ($this->data as $i => $row) {
            $this->tableRow([
                $row['code'] ?? '',
                $row['date'] ?? '',
                $row['supplier'] ?? '',
                $row['type'] ?? '',
                $row['nit'] ?? '',
                (float) ($row['amount'] ?? 0),
                (float) ($row['discounts'] ?? 0),
                (float) ($row['total'] ?? 0),
            ], $i % 2 === 0);

            $totalMonto += (float) ($row['amount'] ?? 0);
            $totalDctos += (float) ($row['discounts'] ?? 0);
            $totalGeneral += (float) ($row['total'] ?? 0);
        }

        $this->tableFooter([
            '', '', '', '', '',
            $totalMonto,
            $totalDctos,
            $totalGeneral,
        ]);

        return $this->Output('S');
    }

    public function getFilename(): string
    {
        return 'reporte_retenciones_'.now()->format('Ymd_His').'.pdf';
    }
}
