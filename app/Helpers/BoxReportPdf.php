<?php

namespace App\Helpers;

class BoxReportPdf extends ReportPdf
{
    protected array $data;

    public function __construct(array $data, array $filters)
    {
        parent::__construct('P', 'mm', 'LETTER');
        $this->data = $data;
        $this->setReportTitle('LIBRO DE CAJA');
        $this->setSubtitle('(EXPRESADO EN BOLIVIANOS)');
        $this->setFilters($filters);
        $this->setColumns([
            ['label' => 'Nº',           'w' => 18, 'align' => 'L'],
            ['label' => 'Fecha',        'w' => 18, 'align' => 'L'],
            ['label' => 'Descripción',  'w' => 70, 'align' => 'L'],
            ['label' => 'Debe',         'w' => 28, 'align' => 'R'],
            ['label' => 'Haber',        'w' => 28, 'align' => 'R'],
            ['label' => 'Saldo',        'w' => 28, 'align' => 'R'],
        ]);
    }

    public function build(): string
    {
        $this->AliasNbPages();
        $this->AddPage();

        $this->tableHeader();

        $totalDebe = 0;
        $totalHaber = 0;

        foreach ($this->data as $i => $row) {
            $this->tableRow([
                $row['number'] ?? '',
                $row['date'] ?? '',
                $row['description'] ?? '',
                (float) ($row['debe'] ?? 0),
                (float) ($row['haber'] ?? 0),
                (float) ($row['saldo'] ?? 0),
            ], $i % 2 === 0);

            $totalDebe += (float) ($row['debe'] ?? 0);
            $totalHaber += (float) ($row['haber'] ?? 0);
        }

        $lastSaldo = count($this->data) > 0
            ? (float) (end($this->data)['saldo'] ?? 0)
            : 0;

        $this->tableFooter([
            '', '', '',
            $totalDebe,
            $totalHaber,
            $lastSaldo,
        ]);

        return $this->Output('S');
    }

    public function getFilename(): string
    {
        return 'reporte_caja_'.now()->format('Ymd_His').'.pdf';
    }
}
