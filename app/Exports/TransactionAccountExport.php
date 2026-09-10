<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class TransactionAccountExport implements FromCollection, ShouldAutoSize, WithEvents
{
    protected Collection $transactions;

    protected string $accountName;

    protected string $accountNumber;

    protected string $moneda;

    protected string $periodLabel;

    public function __construct(Collection $transactions, string $accountName, string $accountNumber, string $moneda, string $periodLabel)
    {
        $this->transactions = $transactions;
        $this->accountName = $accountName;
        $this->accountNumber = $accountNumber;
        $this->moneda = $moneda;
        $this->periodLabel = $periodLabel;
    }

    public function collection(): Collection
    {
        return collect();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->insertNewRowBefore(1, 1);

                $logoPath = public_path('image/logo.png');
                if (file_exists($logoPath)) {
                    $worksheet = $sheet->getDelegate();
                    $drawing = new Drawing;
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A1');
                    $drawing->setWidth(100);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($worksheet);
                }
                $sheet->getRowDimension(1)->setRowHeight(50);

                $headerRow = 2;
                $dataStartRow = 4;
                $lastColumn = 'G';

                // Header: Account info
                $sheet->setCellValue('A'.$headerRow, 'CUENTA: '.$this->accountNumber.' - '.$this->accountName);
                $sheet->setCellValue('E'.$headerRow, 'MONEDA: '.$this->moneda);
                $sheet->getStyle('A'.$headerRow.':'.$lastColumn.$headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3D3DF']],
                ]);

                // Period row
                $periodRow = 3;
                $sheet->setCellValue('A'.$periodRow, 'PERIODO: '.$this->periodLabel);
                $sheet->mergeCells('A'.$periodRow.':'.$lastColumn.$periodRow);
                $sheet->getStyle('A'.$periodRow.':'.$lastColumn.$periodRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3D3DF']],
                ]);

                // Column headers
                $headers = ['Nº', 'Fecha', 'Descripción', 'Dcto. Ref.', 'Debe', 'Haber', 'Saldo'];
                foreach ($headers as $i => $header) {
                    $col = $this->numToLetter($i + 1);
                    $sheet->setCellValue($col.$dataStartRow, $header);
                }
                $sheet->getStyle('A'.$dataStartRow.':'.$lastColumn.$dataStartRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DB4F87']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Data rows
                $rowIndex = $dataStartRow + 1;
                $counter = 1;
                $totals = ['debe' => 0, 'haber' => 0];

                foreach ($this->transactions as $transaction) {
                    $numberLabel = $transaction->transaction?->number_label ?? '';
                    $debe = ($transaction->type == 'D' || $transaction->type == 'B') ? $transaction->amount : '';
                    $haber = ($transaction->type == 'C') ? $transaction->amount : '';

                    if (is_numeric($debe)) {
                        $totals['debe'] += $debe;
                    }
                    if (is_numeric($haber)) {
                        $totals['haber'] += $haber;
                    }

                    $sheet->setCellValue('A'.$rowIndex, $counter);
                    $sheet->setCellValue('B'.$rowIndex, $transaction->date);
                    $sheet->setCellValue('C'.$rowIndex, $transaction->description);
                    $sheet->setCellValue('D'.$rowIndex, $numberLabel);
                    $sheet->setCellValue('E'.$rowIndex, $debe ?: '');
                    $sheet->setCellValue('F'.$rowIndex, $haber ?: '');
                    $sheet->setCellValue('G'.$rowIndex, $transaction->balance);

                    // Format numeric columns
                    if ($debe !== '') {
                        $sheet->getStyle('E'.$rowIndex)->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                    if ($haber !== '') {
                        $sheet->getStyle('F'.$rowIndex)->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                    $sheet->getStyle('G'.$rowIndex)->getNumberFormat()->setFormatCode('#,##0.00');

                    $counter++;
                    $rowIndex++;
                }

                // Auto-height for data rows
                for ($r = $dataStartRow + 1; $r <= $rowIndex - 1; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(-1);
                }

                // Totals row
                $totalRow = $rowIndex;
                $totalSaldo = $totals['debe'] - $totals['haber'];
                $sheet->setCellValue('A'.$totalRow, 'TOTALES');
                $sheet->mergeCells('A'.$totalRow.':D'.$totalRow);
                $sheet->setCellValue('E'.$totalRow, $totals['debe']);
                $sheet->setCellValue('F'.$totalRow, $totals['haber']);
                $sheet->setCellValue('G'.$totalRow, $totalSaldo);

                $sheet->getStyle('E'.$totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('F'.$totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('G'.$totalRow)->getNumberFormat()->setFormatCode('#,##0.00');

                $sheet->getStyle('A'.$totalRow.':'.$lastColumn.$totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3D3DF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Apply borders to all data
                $lastDataRow = $totalRow - 1;
                if ($lastDataRow >= $dataStartRow) {
                    $sheet->getStyle('A'.$dataStartRow.':'.$lastColumn.$lastDataRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                }

                // Alignments
                $sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Text wrap for description column
                $sheet->getStyle('C')->getAlignment()->setWrapText(true);

                // Column widths
                $sheet->getColumnDimension('A')->setAutoSize(false);
                $sheet->getColumnDimension('A')->setWidth(2);
                $sheet->getColumnDimension('C')->setWidth(45);
            },
        ];
    }

    private function numToLetter(int $num): string
    {
        $letter = '';
        while ($num > 0) {
            $mod = ($num - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $num = intdiv(($num - $mod - 1), 26);
        }

        return $letter;
    }
}
