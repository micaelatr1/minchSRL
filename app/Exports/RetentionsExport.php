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

class RetentionsExport implements FromCollection, ShouldAutoSize, WithEvents
{
    protected $retentions;

    protected $taxes;

    protected $type;

    public function __construct(Collection $retentions, $taxes, $type)
    {
        $this->retentions = $retentions;
        $this->taxes = $taxes;
        $this->type = $type;
    }

    public function collection()
    {
        return collect();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $totalTaxes = $this->taxes->count();
                $lastColumnIndex = 7 + $totalTaxes + 1;
                $lastColumnLetter = $this->numToLetter($lastColumnIndex);

                // --- 1. Insertar una fila al principio para el logo ---
                $sheet->insertNewRowBefore(1, 1);

                // --- 2. Agregar el logo en la celda A1 de la nueva fila ---
                $logoPath = public_path('image/logo.png');
                if (file_exists($logoPath)) {
                    $worksheet = $sheet->getDelegate(); // Obtener el objeto Worksheet real
                    $drawing = new Drawing;
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A1');
                    $drawing->setWidth(100);
                    $drawing->setHeight(40);
                    $drawing->setWorksheet($worksheet);
                }

                // Ajustar altura de la fila del logo
                $sheet->getRowDimension(1)->setRowHeight(50);

                // --- 3. Ahora los encabezados comienzan en la fila 2 (originalmente eran filas 1,2,3) ---
                // Definimos las filas base (offset +1)
                $headerRow1 = 2; // antes 1
                $headerRow2 = 3; // antes 2
                $headerRow3 = 4; // antes 3
                $dataStartRow = 5; // antes 4

                // --- ENCABEZADOS (con los nuevos índices) ---
                $sheet->setCellValue('A'.$headerRow1, 'Nro');
                $sheet->setCellValue('B'.$headerRow1, 'Fecha');
                $sheet->setCellValue('C'.$headerRow1, 'Nombre y Apellido');
                $sheet->setCellValue('D'.$headerRow1, 'ROS');
                $sheet->setCellValue('E'.$headerRow1, 'Cédula de Identidad');
                $sheet->setCellValue('F'.$headerRow1, $this->type == 'S' ? 'Servicio' : 'Bien');
                $sheet->setCellValue('G'.$headerRow1, 'Monto');
                $startRetCol = 'H';
                $endRetCol = $this->numToLetter(7 + $totalTaxes);
                $sheet->mergeCells($startRetCol.$headerRow1.':'.$endRetCol.$headerRow1);
                $sheet->setCellValue($startRetCol.$headerRow1, 'Retenciones');
                $importeCol = $this->numToLetter(7 + $totalTaxes + 1);
                $sheet->setCellValue($importeCol.$headerRow1, 'Importe a cancelar');

                $sheet->getStyle('A'.$headerRow1.':'.$lastColumnLetter.$headerRow1)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
                ]);

                // Segunda fila de encabezados (iniciales)
                $col = 'H';
                foreach ($this->taxes as $taxe) {
                    $sheet->setCellValue($col.$headerRow2, $taxe->initials);
                    $col++;
                }
                $sheet->getStyle('A'.$headerRow2.':'.$lastColumnLetter.$headerRow2)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
                ]);

                // Tercera fila de encabezados (números)
                $col = 'H';
                foreach ($this->taxes as $taxe) {
                    $sheet->setCellValue($col.$headerRow3, $taxe->number);
                    $col++;
                }
                $sheet->getStyle('A'.$headerRow3.':'.$lastColumnLetter.$headerRow3)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
                ]);

                // Fusiones verticales (primeras 7 columnas + importe)
                for ($i = 1; $i <= 7; $i++) {
                    $colLetter = $this->numToLetter($i);
                    $sheet->mergeCells($colLetter.$headerRow1.':'.$colLetter.$headerRow3);
                }
                $importeColLetter = $this->numToLetter(7 + $totalTaxes + 1);
                $sheet->mergeCells($importeColLetter.$headerRow1.':'.$importeColLetter.$headerRow3);

                $sheet->getStyle('A'.$headerRow1.':'.$lastColumnLetter.$headerRow3)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Text wrap for nombre y apellido column
                $sheet->getStyle('C')->getAlignment()->setWrapText(true);

                // --- DATOS ---
                $rowIndex = $dataStartRow;
                $counter = 1;
                $totals = array_fill(0, 7 + $totalTaxes + 1, 0);
                $montoColIndex = 6;
                $importeColIndex = 7 + $totalTaxes;
                $descuentoStartIndex = 7;

                foreach ($this->retentions as $retention) {
                    $dataRow = $this->mapRow($retention, $counter);
                    $colIndex = 1;
                    foreach ($dataRow as $idx => $value) {
                        $cell = $this->numToLetter($colIndex).$rowIndex;
                        $sheet->setCellValue($cell, $value);
                        $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
                        $colIndex++;
                    }
                    $this->accumulateTotals($retention, $totals, $totalTaxes, $montoColIndex, $importeColIndex, $descuentoStartIndex);
                    $sheet->getRowDimension($rowIndex)->setRowHeight(-1);
                    $counter++;
                    $rowIndex++;
                }

                // --- FILA DE TOTALES ---
                if ($rowIndex > $dataStartRow) {
                    $totalRow = $rowIndex;
                    $sheet->setCellValue('A'.$totalRow, 'TOTALES');
                    $sheet->mergeCells('A'.$totalRow.':F'.$totalRow);
                    $sheet->getStyle('A'.$totalRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    $sheet->setCellValue($this->numToLetter($montoColIndex + 1).$totalRow, $this->formatNumber($totals[$montoColIndex]));
                    for ($i = 0; $i < $totalTaxes; $i++) {
                        $colLetter = $this->numToLetter($descuentoStartIndex + $i + 1);
                        $sheet->setCellValue($colLetter.$totalRow, $this->formatNumber($totals[$descuentoStartIndex + $i]));
                    }
                    $sheet->setCellValue($this->numToLetter($importeColIndex + 1).$totalRow, $this->formatNumber($totals[$importeColIndex]));

                    $sheet->getStyle('A'.$totalRow.':'.$lastColumnLetter.$totalRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                }

                // Column widths
                $sheet->getColumnDimension('A')->setAutoSize(false);
                $sheet->getColumnDimension('A')->setWidth(2);
                $sheet->getColumnDimension('B')->setWidth(11);
                $sheet->getColumnDimension('C')->setWidth(35);
                $sheet->getColumnDimension('D')->setWidth(8);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(35);
                $sheet->getColumnDimension('G')->setWidth(14);

                for ($i = 1; $i <= $totalTaxes; $i++) {
                    $sheet->getColumnDimension($this->numToLetter(7 + $i))->setWidth(12);
                }
                $sheet->getColumnDimension($this->numToLetter(7 + $totalTaxes + 1))->setWidth(17);

                // Bordes a los datos
                $lastDataRow = $rowIndex - 1;
                if ($lastDataRow >= $dataStartRow) {
                    $sheet->getStyle('A'.$dataStartRow.':'.$lastColumnLetter.$lastDataRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                }
            },
        ];
    }

    private function mapRow($retention, int $counter): array
    {
        $row = [
            $counter,
            $retention->date,
            $retention->supplier->full_name ?? '',
            $retention->code,
            $retention->supplier->ci ?? '',
            $retention->summary,
            $retention->amount_formatted,
        ];

        foreach ($this->taxes as $taxe) {
            $discount = $retention->discounts->firstWhere('taxe_id', $taxe->id);
            $row[] = $discount ? $discount->amount_formatted : '0,00';
        }

        $row[] = $retention->calculate_total_formatted;

        return $row;
    }

    private function accumulateTotals($retention, array &$totals, int $totalTaxes, int $montoColIndex, int $importeColIndex, int $descuentoStartIndex): void
    {
        $totals[$montoColIndex] += $retention->amount;

        foreach ($this->taxes as $i => $taxe) {
            $discount = $retention->discounts->firstWhere('taxe_id', $taxe->id);
            $totals[$descuentoStartIndex + $i] += $discount ? $discount->amount : 0;
        }

        $totals[$importeColIndex] += $retention->calculate_total;
    }

    private function formatNumber($value): string
    {
        return number_format((float) $value, 2, ',', '.');
    }

    private function numToLetter($num)
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
