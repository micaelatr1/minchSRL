<?php

namespace App\Helpers;

use FPDF;

class ReportPdf extends FPDF
{
    protected string $reportTitle = 'REPORTE';

    protected string $subtitle = '';

    protected array $filters = [];

    protected array $columns = [];

    protected int $headerRowCount = 0;

    protected float $colX = 0;

    protected float $usableWidth;

    protected string $codigo = 'P12.F1';

    protected string $revision = '0';

    protected string $clasificacion = 'PÚBLICO';

    protected float $margin = 13;

    public function __construct(string $orientation = 'P', string $unit = 'mm', string $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->SetMargins($this->margin, $this->margin, $this->margin);
        $this->SetAutoPageBreak(true, 15);
    }

    public function setReportTitle(string $title): void
    {
        $this->reportTitle = mb_strtoupper($title, 'UTF-8');
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function setRevision(string $revision): void
    {
        $this->revision = $revision;
    }

    public function setClasificacion(string $clasificacion): void
    {
        $this->clasificacion = $clasificacion;
    }

    protected function getRightColWidth(): float
    {
        return 35;
    }

    protected function getLeftColWidth(): float
    {
        return 55;
    }

    protected function getCenterColWidth(): float
    {
        $pageW = $this->w - $this->lMargin - $this->rMargin;

        return $pageW - $this->getLeftColWidth() - $this->getRightColWidth();
    }

    public function Header(): void
    {
        if ($this->PageNo() > 1) {
            $this->drawSimpleHeader();

            return;
        }

        $lMargin = $this->lMargin;
        $rightW = $this->getRightColWidth();
        $leftW = $this->getLeftColWidth();
        $centerW = $this->getCenterColWidth();

        $rowH1 = 10;
        $rowH2 = 5;
        $rowH3 = 6;
        $totalH = $rowH1 + $rowH2 + $rowH3;

        $y = $this->GetY();
        $xRight = $lMargin + $leftW + $centerW;

        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);

        // ── Row 1 ──
        $logoPath = public_path('image/logo.png');
        if (file_exists($logoPath)) {
            $this->Image($logoPath, $lMargin + 2, $y + 1, 35);
        }

        $this->SetFont('Helvetica', 'B', 9);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY($lMargin + 2, $y + $rowH1 - 5);
        $this->Cell($leftW - 4, 4, 'EMPRESA MINCH S.R.L.', 0, 0, 'C');

        // Center: title + subtitle
        $this->SetFont('Helvetica', 'B', 12);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY($lMargin + $leftW, $y + 1);
        $this->Cell($centerW, 6, $this->reportTitle, 0, 0, 'C');

        if ($this->subtitle) {
            $this->SetFont('Helvetica', '', 8);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY($lMargin + $leftW, $y + 8);
            $this->Cell($centerW, 4, $this->subtitle, 0, 0, 'C');
        }

        // Right: codigo
        $this->SetFont('Helvetica', 'B', 9);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY($xRight, $y);
        $this->Cell($rightW, $rowH1, 'Codigo: '.$this->codigo, 1, 0, 'C');

        // ── Row 2 ──
        $this->SetXY($xRight, $y + $rowH1);
        $this->SetFont('Helvetica', 'B', 9);
        $this->Cell($rightW, $rowH2, 'Revision: '.$this->revision, 1, 0, 'C');

        // ── Row 3 ──
        $this->SetXY($xRight, $y + $rowH1 + $rowH2);
        $this->SetFillColor(44, 62, 80);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Helvetica', 'B', 9);
        $this->Cell($rightW, $rowH3, '  '.$this->clasificacion.'  ', 1, 0, 'C', true);

        // ── Borders for left and center cells ──
        $this->SetDrawColor(0, 0, 0);
        $this->Rect($lMargin, $y, $leftW, $totalH);
        $this->Rect($lMargin + $leftW, $y, $centerW, $totalH);

        // ── Filters ──
        $fy = $y + $totalH + 1;
        if (! empty($this->filters)) {
            $pageW = $this->w - $lMargin - $this->rMargin;
            $this->SetFont('Helvetica', '', 7);
            $this->SetTextColor(100, 100, 100);
            $filterText = implode(' | ', $this->filters);
            $this->SetXY($lMargin, $fy);
            $this->Cell($pageW, 3.5, $filterText, 0, 0, 'C');
            $fy += 4;
        }

        // ── Bottom line ──
        $headerBottom = $fy + 1;
        $this->SetDrawColor(129, 204, 106);
        $this->SetLineWidth(0.8);
        $pageW = $this->w - $lMargin - $this->rMargin;
        $this->Line($lMargin, $headerBottom, $lMargin + $pageW, $headerBottom);
        $this->SetLineWidth(0.2);

        $this->SetY($headerBottom + 2);
    }

    protected function drawSimpleHeader(): void
    {
        $lMargin = $this->lMargin;
        $rightW = $this->getRightColWidth();
        $leftW = $this->getLeftColWidth();
        $centerW = $this->getCenterColWidth();
        $pageW = $this->w - $lMargin - $this->rMargin;

        $rowH1 = 8;
        $rowH2 = 4;
        $rowH3 = 5;
        $totalH = $rowH1 + $rowH2 + $rowH3;

        $y = $this->GetY();
        $xRight = $lMargin + $leftW + $centerW;

        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);

        // Row 1
        $logoPath = public_path('image/logo.png');
        if (file_exists($logoPath)) {
            $this->Image($logoPath, $lMargin + 2, $y + 1, 25);
        }

        $this->SetFont('Helvetica', 'B', 7);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY($lMargin + 2, $y + $rowH1 - 4);
        $this->Cell($leftW - 4, 3, 'EMPRESA MINCH S.R.L.', 0, 0, 'C');

        $this->SetFont('Helvetica', 'B', 9);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY($lMargin + $leftW, $y + 1);
        $this->Cell($centerW, 5, $this->reportTitle, 0, 0, 'C');

        if ($this->subtitle) {
            $this->SetFont('Helvetica', '', 7);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY($lMargin + $leftW, $y + 6);
            $this->Cell($centerW, 3, $this->subtitle, 0, 0, 'C');
        }

        // Right
        $this->SetFont('Helvetica', 'B', 7);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY($xRight, $y);
        $this->Cell($rightW, $rowH1, 'Código: '.$this->codigo, 1, 0, 'C');

        $this->SetXY($xRight, $y + $rowH1);
        $this->Cell($rightW, $rowH2, 'Revisión: '.$this->revision, 1, 0, 'C');

        $this->SetXY($xRight, $y + $rowH1 + $rowH2);
        $this->SetFillColor(44, 62, 80);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($rightW, $rowH3, '  '.$this->clasificacion.'  ', 1, 0, 'C', true);

        // Borders for left and center
        $this->SetDrawColor(0, 0, 0);
        $this->Rect($lMargin, $y, $leftW, $totalH);
        $this->Rect($lMargin + $leftW, $y, $centerW, $totalH);

        // Bottom green line
        $this->SetDrawColor(129, 204, 106);
        $this->SetLineWidth(0.5);
        $yLine = $y + $totalH + 1;
        $this->Line($lMargin, $yLine, $lMargin + $pageW, $yLine);
        $this->SetLineWidth(0.2);
        $this->SetY($yLine + 2);
    }

    public function Footer(): void
    {
        $this->SetY(-12);
        $this->SetFont('Helvetica', '', 6.5);
        $this->SetTextColor(130, 130, 130);

        $pageW = $this->w - $this->lMargin - $this->rMargin;
        $this->Cell($pageW, 4, 'Informe generado automáticamente', 0, 0, 'L');

        $this->SetFont('Helvetica', '', 7);
        $this->Cell(0, 4, 'Página '.$this->PageNo().' de {nb}', 0, 0, 'R');
    }

    public function tableHeader(): void
    {
        if (empty($this->columns)) {
            return;
        }

        $this->SetFont('Helvetica', 'B', 7.5);
        $this->SetFillColor(129, 204, 106);
        $this->SetTextColor(255, 255, 255);

        $x = $this->GetX();
        $y = $this->GetY();
        $rowH = 7;

        // Check page break
        if ($y + $rowH > $this->h - $this->bMargin) {
            $this->AddPage();
            $x = $this->lMargin;
            $y = $this->GetY();
        }

        $this->SetDrawColor(129, 204, 106);

        $totalW = 0;
        foreach ($this->columns as $col) {
            $totalW += $col['w'];
        }
        $this->colX = $x;

        foreach ($this->columns as $col) {
            $w = $col['w'];
            $label = mb_strtoupper($col['label'] ?? '', 'UTF-8');
            $align = $col['align'] ?? 'L';
            $this->Cell($w, $rowH, ' '.$label, 1, 0, $align === 'R' ? 'R' : 'L', true);
        }

        $this->Ln();
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);

        $this->headerRowCount++;
    }

    public function tableRow(array $data, bool $isEven = false): void
    {
        if (empty($this->columns)) {
            return;
        }

        $rowH = 6;
        $y = $this->GetY();

        if ($y + $rowH > $this->h - $this->bMargin) {
            $this->AddPage();
            $this->tableHeader();
        }

        if ($isEven) {
            $this->SetFillColor(250, 252, 253);
        } else {
            $this->SetFillColor(255, 255, 255);
        }

        $this->SetDrawColor(220, 220, 220);
        $this->SetTextColor(60, 60, 60);

        // Determine numeric columns (those with right alignment)
        $numericColumns = [];
        foreach ($this->columns as $i => $col) {
            if (($col['align'] ?? 'L') === 'R') {
                $numericColumns[$i] = true;
            }
        }

        $this->SetX($this->lMargin);
        foreach ($this->columns as $i => $col) {
            $w = $col['w'];
            $value = $data[$i] ?? '';
            $align = $col['align'] ?? 'L';

            if ($align === 'R') {
                $this->SetFont('Courier', '', 7);
                $display = number_format((float) $value, 2, ',', '.');
                $this->Cell($w, $rowH, $display.' ', 1, 0, 'R', true);
            } else {
                $this->SetFont('Helvetica', '', 7);
                $this->Cell($w, $rowH, ' '.$value, 1, 0, 'L', true);
            }
        }

        $this->Ln();
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
    }

    public function tableFooter(array $totals): void
    {
        if (empty($this->columns)) {
            return;
        }

        $rowH = 7;
        $y = $this->GetY();

        if ($y + $rowH > $this->h - $this->bMargin) {
            $this->AddPage();
            $this->tableHeader();
        }

        $this->SetFont('Helvetica', 'B', 7.5);
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(30, 30, 30);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.5);

        $this->SetX($this->lMargin);

        $numericColumns = [];
        foreach ($this->columns as $i => $col) {
            if (($col['align'] ?? 'L') === 'R') {
                $numericColumns[$i] = true;
            }
        }

        foreach ($this->columns as $i => $col) {
            $w = $col['w'];
            $value = $totals[$i] ?? '';

            if ($i === 0) {
                $this->SetFont('Helvetica', 'B', 7.5);
                $this->Cell($w, $rowH, ' TOTALES', 'LTB', 0, 'L', true);
            } elseif ($i === count($this->columns) - 1) {
                if (isset($numericColumns[$i])) {
                    $this->SetFont('Courier', 'B', 7.5);
                    $display = number_format((float) $value, 2, ',', '.');
                    $this->Cell($w, $rowH, $display.' ', 'LTB', 0, 'R', true);
                } else {
                    $this->SetFont('Helvetica', 'B', 7.5);
                    $this->Cell($w, $rowH, ' '.$value, 'LTB', 0, 'L', true);
                }
            } else {
                if (isset($numericColumns[$i])) {
                    $this->SetFont('Courier', 'B', 7.5);
                    $display = number_format((float) $value, 2, ',', '.');
                    $this->Cell($w, $rowH, $display.' ', 'LTB', 0, 'R', true);
                } else {
                    $this->SetFont('Helvetica', '', 7.5);
                    $this->Cell($w, $rowH, '', 'LTB', 0, 'L', true);
                }
            }
        }

        $this->Ln();
        $this->SetLineWidth(0.2);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
    }
}
