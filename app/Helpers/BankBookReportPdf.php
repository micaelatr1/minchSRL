<?php

namespace App\Helpers;

class BankBookReportPdf extends ReportPdf
{
    protected array $data;

    public function __construct(array $data, array $filters)
    {
        parent::__construct('P', 'mm', 'LETTER');
        $this->data = $data;
        $this->setReportTitle('LIBRO DE BANCOS');
        $this->setSubtitle('(EXPRESADO EN BOLIVIANOS)');
        $this->setFilters($filters);
        $this->setColumns([
            ['label' => 'Nº',           'w' => 18, 'align' => 'L'],
            ['label' => 'Fecha',        'w' => 18, 'align' => 'L'],
            ['label' => 'Descripción',  'w' => 55, 'align' => 'L'],
            ['label' => 'Dcto.Ref.',    'w' => 20, 'align' => 'L'],
            ['label' => 'Débito',       'w' => 26, 'align' => 'R'],
            ['label' => 'Crédito',      'w' => 26, 'align' => 'R'],
            ['label' => 'Saldo',        'w' => 26, 'align' => 'R'],
        ]);
    }

    public function build(): string
    {
        $this->AliasNbPages();
        $this->AddPage();

        $this->tableHeader();

        $totalDebito = 0;
        $totalCredito = 0;

        foreach ($this->data as $i => $row) {
            $this->tableRow([
                $row['number'] ?? '',
                $row['date'] ?? '',
                $row['description'] ?? '',
                $row['ref'] ?? '',
                (float) ($row['debito'] ?? 0),
                (float) ($row['credito'] ?? 0),
                (float) ($row['saldo'] ?? 0),
            ], $i % 2 === 0);

            $totalDebito += (float) ($row['debito'] ?? 0);
            $totalCredito += (float) ($row['credito'] ?? 0);
        }

        $lastSaldo = count($this->data) > 0
            ? (float) (end($this->data)['saldo'] ?? 0)
            : 0;

        $this->tableFooter([
            '', '', '', '',
            $totalDebito,
            $totalCredito,
            $lastSaldo,
        ]);

        return $this->Output('S');
    }

    public function getFilename(): string
    {
        return 'reporte_bancos_'.now()->format('Ymd_His').'.pdf';
    }
}
