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
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

abstract class BaseReportExport implements FromCollection, ShouldAutoSize, WithEvents
{
    protected string $title = 'REPORTE';

    protected string $subtitle = '';

    protected array $columns = [];

    protected array $data = [];

    protected string $codigo = 'P12.F1';

    protected string $revision = '0';

    protected string $clasificacion = 'PÚBLICO';

    protected bool $narrowFirstColumn = true;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return collect();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->buildSheet($event);
            },
        ];
    }

    abstract protected function mapRow($item): array;

    protected function buildSheet(AfterSheet $event): void
    {
        $sheet = $event->sheet;

        // ── Page setup: Letter size ──
        $sheet->getDelegate()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_LETTER);

        $lastCol = count($this->columns);
        $lastLetter = $this->numToLetter($lastCol);

        // Right section: use last column (narrow) for code/revision/publico
        $rightCol = $lastLetter;
        $centerEnd = $this->numToLetter($lastCol - 1);

        // Left section: columns A-C, center section: D to centerEnd
        $leftEnd = $this->numToLetter(min(3, $lastCol - 1));
        $centerStart = $this->numToLetter(min(4, $lastCol));

        if ($lastCol > 4) {
            $centerEnd = $this->numToLetter($lastCol - 1);
        } else {
            $centerEnd = $this->numToLetter(max(1, $lastCol - 1));
        }

        // ── Header table (3 rows) ──
        $headerBorder = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        $row = 1;

        // ── Row 1 ──
        // Left: company name
        $sheet->mergeCells('A'.$row.':'.$leftEnd.$row);
        $sheet->setCellValue('A'.$row, 'EMPRESA MINCH S.R.L.');
        $sheet->getStyle('A'.$row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getStyle('A'.$row)->applyFromArray($headerBorder);

        // Center: Title
        $sheet->mergeCells($centerStart.$row.':'.$centerEnd.$row);
        $sheet->setCellValue($centerStart.$row, $this->title);
        $sheet->getStyle($centerStart.$row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getStyle($centerStart.$row)->applyFromArray($headerBorder);

        // Right: Codigo
        $sheet->setCellValue($rightCol.$row, 'Código: '.$this->codigo);
        $sheet->getStyle($rightCol.$row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle($rightCol.$row)->applyFromArray($headerBorder);

        $sheet->getRowDimension($row)->setRowHeight(22);
        $row++;

        // ── Row 2 ──
        // Left: merged (empty, keep border)
        $sheet->mergeCells('A'.$row.':'.$leftEnd.$row);
        $sheet->getStyle('A'.$row)->applyFromArray($headerBorder);

        // Center: subtitle (or empty)
        $sheet->mergeCells($centerStart.$row.':'.$centerEnd.$row);
        if ($this->subtitle) {
            $sheet->setCellValue($centerStart.$row, $this->subtitle);
            $sheet->getStyle($centerStart.$row)->applyFromArray([
                'font' => ['size' => 8, 'color' => ['rgb' => '000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }
        $sheet->getStyle($centerStart.$row)->applyFromArray($headerBorder);

        // Right: Revision
        $sheet->setCellValue($rightCol.$row, 'Revisión: '.$this->revision);
        $sheet->getStyle($rightCol.$row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle($rightCol.$row)->applyFromArray($headerBorder);

        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // ── Row 3 ──
        // Left: merged (empty, keep border)
        $sheet->mergeCells('A'.$row.':'.$leftEnd.$row);
        $sheet->getStyle('A'.$row)->applyFromArray($headerBorder);

        // Center: merged (empty, keep border)
        $sheet->mergeCells($centerStart.$row.':'.$centerEnd.$row);
        $sheet->getStyle($centerStart.$row)->applyFromArray($headerBorder);

        // Right: PUBLICO badge
        $sheet->setCellValue($rightCol.$row, '  '.$this->clasificacion.'  ');
        $sheet->getStyle($rightCol.$row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle($rightCol.$row)->applyFromArray($headerBorder);

        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        // ── Empty separator row ──
        $row++;

        // ── Column headers ──
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '81CC6A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '81CC6A']]],
        ];

        $colLetter = 'A';
        foreach ($this->columns as $col) {
            $sheet->setCellValue($colLetter.$row, $col['label']);
            $sheet->getStyle($colLetter.$row)->applyFromArray($headerStyle);
            $colLetter++;
        }
        $headerRow = $row;
        $row++;

        // ── Data rows ──
        $evenStyle = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FAFCFD']],
        ];
        $numberFormat = '###,##0.00';
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];

        foreach ($this->data as $i => $item) {
            $mapped = $this->mapRow($item);
            $colLetter = 'A';
            foreach ($mapped as $ci => $value) {
                $cell = $colLetter.$row;
                $colDef = $this->columns[$ci] ?? [];
                $align = $colDef['align'] ?? 'L';

                if ($align === 'R' && is_numeric($value)) {
                    $sheet->setCellValue($cell, $value);
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($numberFormat);
                    $sheet->getStyle($cell)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'wrapText' => true],
                    ]);
                } else {
                    $sheet->setCellValue($cell, $value);
                    $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
                }

                $sheet->getStyle($cell)->applyFromArray($borderStyle);
                if ($i % 2 === 1) {
                    $sheet->getStyle($cell)->applyFromArray($evenStyle);
                }

                $colLetter++;
            }
            $sheet->getRowDimension($row)->setRowHeight(-1);
            $row++;
        }

        // ── Totals row ──
        $totalStyle = [
            'font' => ['bold' => true, 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ];

        $sheet->setCellValue('A'.$row, 'TOTALES');
        $sheet->getStyle('A'.$row)->applyFromArray($totalStyle);

        $totals = $this->calculateTotals();
        $colLetter = 'B';
        foreach ($totals as $ti => $total) {
            if ($total !== null) {
                $sheet->setCellValue($colLetter.$row, $total);
                $sheet->getStyle($colLetter.$row)->getNumberFormat()->setFormatCode($numberFormat);
            }
            $sheet->getStyle($colLetter.$row)->applyFromArray($totalStyle);
            $colLetter++;
        }
        for ($ci = count($totals) + 1; $ci < $lastCol; $ci++) {
            $letter = $this->numToLetter($ci + 1);
            $sheet->getStyle($letter.$row)->applyFromArray($totalStyle);
        }

        // Column widths
        if ($this->narrowFirstColumn) {
            $sheet->getColumnDimension('A')->setAutoSize(false);
            $sheet->getColumnDimension('A')->setWidth(2);
            if ($lastCol >= 3) {
                $sheet->getColumnDimension('C')->setWidth(45);
            }
        }
    }

    protected function calculateTotals(): array
    {
        $lastIndex = count($this->columns) - 1;
        $totals = array_fill(0, $lastIndex, null);

        $numericIndices = [];
        foreach ($this->columns as $i => $col) {
            if (($col['align'] ?? 'L') === 'R') {
                $numericIndices[$i - 1] = true;
            }
        }

        foreach ($numericIndices as $idx => $_) {
            $sum = 0;
            foreach ($this->data as $item) {
                $mapped = $this->mapRow($item);
                $val = $mapped[$idx + 1] ?? 0;
                $sum += (float) $val;
            }
            $totals[$idx] = $sum;
        }

        return $totals;
    }

    protected function numToLetter(int $num): string
    {
        $letter = '';
        while ($num > 0) {
            $mod = ($num - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $num = intdiv(($num - $mod - 1), 26);
        }

        return $letter ?: 'A';
    }
}
