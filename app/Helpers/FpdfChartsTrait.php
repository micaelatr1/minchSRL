<?php

namespace App\Helpers;

/**
 * FpdfChartsTraitV2
 *
 * Trait para dibujar gráficos 2D (Pie, Donut, Bar, Line) dentro de documentos FPDF.
 * La configuración de cada gráfico sigue una estructura similar a ApexCharts.
 *
 * @requires FPDF >= 1.82
 */
trait FpdfChartsTrait
{
    private array $chartDefaultColors = [
        '#008FFB',
        '#00E396',
        '#FEB019',
        '#FF4560',
        '#775DD0',
        '#3F51B5',
        '#546E7A',
        '#D4526E',
        '#8D5B4C',
        '#F86624',
        '#D7263D',
        '#1B998B',
    ];

    private string $chartFont = 'Arial';

    private array $_swCache = [];

    public function setChartFont(string $font): void
    {
        $this->chartFont = $font;
        $this->_swCache = [];
    }

    private function _cf(string $style, int $size): void
    {
        $this->SetFont($this->chartFont, $style, $size);
    }

    private function _sw(string $text): float
    {
        $key = $this->FontFamily.$this->FontStyle.$this->FontSizePt.$text;

        return $this->_swCache[$key] ??= $this->GetStringWidth($text);
    }

    private function validateConfig(array $config, string $type): bool
    {
        if (empty($config['series'])) {
            trigger_error("FpdfChartsTrait [{$type}]: 'series' es requerido y no puede estar vacío.", E_USER_WARNING);

            return false;
        }

        return true;
    }

    public function drawPieChart(array $config): void
    {
        if (! $this->validateConfig($config, 'Pie')) {
            return;
        }
        $this->renderPieOrDonut($config, false);
    }

    public function drawDonutChart(array $config): void
    {
        if (! $this->validateConfig($config, 'Donut')) {
            return;
        }
        $this->renderPieOrDonut($config, true);
    }

    public function drawBarChart(array $config): void
    {
        if (! $this->validateConfig($config, 'Bar')) {
            return;
        }

        $chart = $config['chart'] ?? [];
        $plotOpts = $config['plotOptions'] ?? [];
        $barOpts = $plotOpts['bar'] ?? [];
        $dataLabels = $config['dataLabels'] ?? [];
        $legendOpts = $config['legend'] ?? [];
        $gridOpts = $config['grid'] ?? [];
        $xaxis = $config['xaxis'] ?? [];
        $yaxis = $config['yaxis'] ?? [];

        $x0 = (float) ($chart['x'] ?? 20);
        $y0 = (float) ($chart['y'] ?? 60);
        $width = (float) ($chart['width'] ?? 120);
        $height = (float) ($chart['height'] ?? 80);

        $horizontal = (bool) ($barOpts['horizontal'] ?? false);
        $borderRad = (int) ($barOpts['borderRadius'] ?? 0);
        $stacked = (bool) ($barOpts['stacked'] ?? false);
        $showLabels = (bool) ($dataLabels['enabled'] ?? false);
        $labelFS = (int) ($dataLabels['fontSize'] ?? 6);
        $showGrid = (bool) ($gridOpts['show'] ?? true);
        $showLegend = (bool) ($legendOpts['show'] ?? true);
        $legendPos = (string) ($legendOpts['position'] ?? 'top');

        $seriesList = $config['series'] ?? [];
        $categories = $xaxis['categories'] ?? [];
        $colors = $config['colors'] ?? $this->chartDefaultColors;
        $annotations = $config['annotations'] ?? [];

        $this->renderChartTitle($config, $x0, $y0, $width);
        $titleOffset = $this->getChartTitleOffset($config);

        $plotY = $y0 + $titleOffset;
        $plotHeight = $height - $titleOffset;

        $legendHeight = 0;
        if ($showLegend && $legendPos === 'top') {
            $legendHeight = $this->renderBarLegend($seriesList, $colors, $x0, $plotY, $width, 'top');
            $plotY += $legendHeight;
            $plotHeight -= $legendHeight;
        }

        $allValues = [];
        if ($stacked) {
            $numCats = max(count($categories), 1);
            for ($ci = 0; $ci < $numCats; $ci++) {
                $sum = 0;
                foreach ($seriesList as $s) {
                    $sum += (float) ($s['data'][$ci] ?? 0);
                }
                $allValues[] = $sum;
            }
        } else {
            foreach ($seriesList as $s) {
                foreach (($s['data'] ?? []) as $v) {
                    $allValues[] = (float) $v;
                }
            }
        }
        if (empty($allValues)) {
            return;
        }

        $minVal = (float) ($yaxis['min'] ?? 0);
        $maxVal = (float) ($yaxis['max'] ?? max($allValues) * 1.1);
        $maxVal = $maxVal ?: 1;
        $tickAmt = max(1, (int) ($yaxis['tickAmount'] ?? 5));

        $marginLeft = $horizontal ? 20 : 12;
        $marginBottom = $horizontal ? 8 : 12;
        $marginRight = 4;
        $marginTop = 4;

        $plotX = $x0 + $marginLeft;
        $plotW = $width - $marginLeft - $marginRight;
        $plotH = $plotHeight - $marginTop - $marginBottom;
        $plotYY = $plotY + $marginTop;

        $this->renderBarAxes(
            $plotX, $plotYY, $plotW, $plotH,
            $minVal, $maxVal, $tickAmt, $categories, $showGrid, $horizontal
        );

        $this->renderAnnotations($annotations, $plotX, $plotYY, $plotW, $plotH, $minVal, $maxVal, $categories, $horizontal);

        $numSeries = count($seriesList);
        $numCats = max(count($categories), 1);

        $groupWidthPct = (float) (rtrim($barOpts['columnWidth'] ?? '70%', '%')) / 100;
        $barHeightPct = (float) (rtrim($barOpts['barHeight'] ?? '70%', '%')) / 100;

        if (! $horizontal) {
            $groupW = ($plotW / $numCats) * $groupWidthPct;
            $singleW = $stacked ? $groupW : ($numSeries > 0 ? $groupW / $numSeries : $groupW);

            if ($stacked) {
                for ($ci = 0; $ci < $numCats; $ci++) {
                    $catCenter = $plotX + ($plotW / $numCats) * ($ci + 0.5);
                    $bx = $catCenter - $groupW / 2;
                    $offsetH = 0;

                    foreach ($seriesList as $si => $serie) {
                        $value = (float) ($serie['data'][$ci] ?? 0);
                        if ($value <= 0) {
                            continue;
                        }

                        $barH = $plotH * ($value - $minVal) / ($maxVal - $minVal);
                        $by = $plotYY + $plotH - $offsetH - $barH;

                        [$r, $g, $b] = $this->_resolveColor($colors, $si, $value);
                        $this->SetFillColor($r, $g, $b);

                        if ($borderRad > 0 && $offsetH === 0) {
                            $this->_chartRoundedRect($bx, $by, $groupW - 0.5, $barH, min($borderRad, 2), 'F');
                        } else {
                            $this->Rect($bx, $by, $groupW - 0.5, $barH, 'F');
                        }

                        if ($showLabels && $barH > 4) {
                            $this->SetTextColor(255, 255, 255);
                            $this->_cf('B', $labelFS);
                            $label = $this->formatChartNumber($value);
                            $tw = $this->_sw($label);
                            $this->SetXY($bx + ($groupW - 0.5 - $tw) / 2, $by + 1.5);
                            $this->Cell($tw, $labelFS * 0.4, $label);
                            $this->SetTextColor(0, 0, 0);
                        }

                        $offsetH += $barH;
                    }
                }
            } else {
                foreach ($seriesList as $si => $serie) {
                    foreach (($serie['data'] ?? []) as $ci => $value) {
                        $value = (float) $value;
                        $catCenter = $plotX + ($plotW / $numCats) * ($ci + 0.5);
                        $groupLeft = $catCenter - ($groupW / 2);
                        $bx = $groupLeft + $si * $singleW;
                        $barH = $plotH * ($value - $minVal) / ($maxVal - $minVal);
                        $by = $plotYY + $plotH - $barH;

                        [$r, $g, $b] = $this->_resolveColor($colors, $si, $value);
                        $this->SetFillColor($r, $g, $b);

                        if ($borderRad > 0) {
                            $this->_chartRoundedRect($bx, $by, $singleW - 0.5, $barH, min($borderRad, 2), 'F');
                        } else {
                            $this->Rect($bx, $by, $singleW - 0.5, $barH, 'F');
                        }

                        if ($showLabels && $barH > 4) {
                            $this->SetTextColor(255, 255, 255);
                            $this->_cf('B', $labelFS);
                            $label = $this->formatChartNumber($value);
                            $tw = $this->_sw($label);
                            $this->SetTextColor(0, 0, 0);
                            $this->SetXY($bx + ($singleW - 1.75 - $tw), $by - 2.25);
                            $this->Cell($tw, $labelFS * 0.4, $label);
                            $this->SetTextColor(0, 0, 0);
                        }
                    }
                }
            }
        } else {
            $groupH = ($plotH / $numCats) * $barHeightPct;
            $singleH = $stacked ? $groupH : ($numSeries > 0 ? $groupH / $numSeries : $groupH);

            if ($stacked) {
                for ($ci = 0; $ci < $numCats; $ci++) {
                    $catCenter = $plotYY + ($plotH / $numCats) * ($ci + 0.5);
                    $by = $catCenter - $groupH / 2;
                    $offsetW = 0;

                    foreach ($seriesList as $si => $serie) {
                        $value = (float) ($serie['data'][$ci] ?? 0);
                        if ($value <= 0) {
                            continue;
                        }

                        $barW = $plotW * ($value - $minVal) / ($maxVal - $minVal);

                        [$r, $g, $b] = $this->_resolveColor($colors, $si, $value);
                        $this->SetFillColor($r, $g, $b);
                        $this->Rect($plotX + $offsetW, $by, $barW, $groupH - 0.5, 'F');

                        if ($showLabels && $barW > 6) {
                            $this->SetTextColor(255, 255, 255);
                            $this->_cf('B', $labelFS);
                            $label = $this->formatChartNumber($value);
                            $tw = $this->_sw($label);
                            $this->SetXY($plotX + $offsetW + $barW - $tw - 1, $by + ($groupH - 0.5 - $labelFS * 0.35) / 2);
                            $this->Cell($tw, $labelFS * 0.35, $label);
                            $this->SetTextColor(0, 0, 0);
                        }

                        $offsetW += $barW;
                    }
                }
            } else {
                foreach ($seriesList as $si => $serie) {
                    foreach (($serie['data'] ?? []) as $ci => $value) {
                        $value = (float) $value;
                        $catCenter = $plotYY + ($plotH / $numCats) * ($ci + 0.5);
                        $groupTop = $catCenter - ($groupH / 2);
                        $by = $groupTop + $si * $singleH;
                        $barW = $plotW * ($value - $minVal) / ($maxVal - $minVal);

                        [$r, $g, $b] = $this->_resolveColor($colors, $si, $value);
                        $this->SetFillColor($r, $g, $b);

                        if ($borderRad > 0) {
                            $this->_chartRoundedRect($plotX, $by, $barW, $singleH - 0.5, min($borderRad, 2), 'F');
                        } else {
                            $this->Rect($plotX, $by, $barW, $singleH - 0.5, 'F');
                        }

                        if ($showLabels && $barW > 6) {
                            $this->SetTextColor(255, 255, 255);
                            $this->_cf('B', $labelFS);
                            $label = $this->formatChartNumber($value);
                            $tw = $this->_sw($label);
                            $this->SetXY($plotX + $barW - $tw - 1, $by + ($singleH - 0.5 - $labelFS * 0.35) / 2);
                            $this->Cell($tw, $labelFS * 0.35, $label);
                            $this->SetTextColor(0, 0, 0);
                        }
                    }
                }
            }
        }

        if ($showLegend && $legendPos === 'bottom') {
            $legendY = $plotY + $plotHeight;
            $this->renderBarLegend($seriesList, $colors, $x0, $legendY + 1, $width, 'bottom');
        }
    }

    public function drawLineChart(array $config): void
    {
        if (! $this->validateConfig($config, 'Line')) {
            return;
        }

        $chart = $config['chart'] ?? [];
        $strokeOpts = $config['stroke'] ?? [];
        $markerOpts = $config['markers'] ?? [];
        $fillOpts = $config['fill'] ?? [];
        $dataLabels = $config['dataLabels'] ?? [];
        $legendOpts = $config['legend'] ?? [];
        $gridOpts = $config['grid'] ?? [];
        $xaxis = $config['xaxis'] ?? [];
        $yaxis = $config['yaxis'] ?? [];

        $x0 = (float) ($chart['x'] ?? 20);
        $y0 = (float) ($chart['y'] ?? 60);
        $width = (float) ($chart['width'] ?? 150);
        $height = (float) ($chart['height'] ?? 80);

        $strokeW = (float) ($strokeOpts['width'] ?? 1.0);
        $curve = (string) ($strokeOpts['curve'] ?? 'straight');
        $markerSize = (float) ($markerOpts['size'] ?? 0);
        $fillType = (string) ($fillOpts['type'] ?? 'none');
        $fillOpac = (float) ($fillOpts['opacity'] ?? 0.1);
        $showLabels = (bool) ($dataLabels['enabled'] ?? false);
        $labelFS = (int) ($dataLabels['fontSize'] ?? 6);
        $showGrid = (bool) ($gridOpts['show'] ?? true);
        $showLegend = (bool) ($legendOpts['show'] ?? true);
        $legendPos = (string) ($legendOpts['position'] ?? 'top');

        $seriesList = $config['series'] ?? [];
        $categories = $xaxis['categories'] ?? [];
        $colors = $config['colors'] ?? $this->chartDefaultColors;
        $annotations = $config['annotations'] ?? [];

        $this->renderChartTitle($config, $x0, $y0, $width);
        $titleOffset = $this->getChartTitleOffset($config);

        $plotY = $y0 + $titleOffset;
        $plotHeight = $height - $titleOffset;

        $legendHeight = 0;
        if ($showLegend && $legendPos === 'top') {
            $legendHeight = $this->renderBarLegend($seriesList, $colors, $x0, $plotY, $width, 'top');
            $plotY += $legendHeight;
            $plotHeight -= $legendHeight;
        }

        $allValues = [];
        foreach ($seriesList as $s) {
            foreach (($s['data'] ?? []) as $v) {
                $allValues[] = (float) $v;
            }
        }
        if (empty($allValues)) {
            return;
        }

        $minVal = (float) ($yaxis['min'] ?? min($allValues) * 0.9);
        $maxVal = (float) ($yaxis['max'] ?? max($allValues) * 1.1);
        $maxVal = ($maxVal == $minVal) ? $maxVal + 1 : $maxVal;
        $tickAmt = max(1, (int) ($yaxis['tickAmount'] ?? 5));

        $marginLeft = 12;
        $marginBottom = 12;
        $marginRight = 4;
        $marginTop = 4;

        $plotX = $x0 + $marginLeft;
        $plotW = $width - $marginLeft - $marginRight;
        $plotH = $plotHeight - $marginTop - $marginBottom;
        $plotYY = $plotY + $marginTop;

        $this->renderBarAxes(
            $plotX, $plotYY, $plotW, $plotH,
            $minVal, $maxVal, $tickAmt, $categories, $showGrid, false
        );

        $this->renderAnnotations($annotations, $plotX, $plotYY, $plotW, $plotH, $minVal, $maxVal, $categories, false);

        $numCats = max(count($categories), 2);

        foreach ($seriesList as $si => $serie) {
            $data = $serie['data'] ?? [];
            if (empty($data)) {
                continue;
            }

            $color = $colors[$si % count($colors)] ?? '#008FFB';
            [$r, $g, $b] = $this->_chartHexToRgb($color);

            $pts = [];
            foreach ($data as $ci => $value) {
                $px = $plotX + $plotW * $ci / ($numCats - 1);
                $py = $plotYY + $plotH - $plotH * ((float) $value - $minVal) / ($maxVal - $minVal);
                $pts[] = [$px, $py];
            }

            if ($fillType !== 'none' && $fillOpac > 0 && count($pts) >= 2) {
                $this->_drawLineArea($pts, $plotX, $plotYY, $plotW, $plotH, $r, $g, $b, $fillOpac, $curve);
            }

            $this->SetDrawColor($r, $g, $b);
            $this->SetLineWidth($strokeW * 0.352778);

            if ($curve === 'smooth' && count($pts) >= 2) {
                $this->_drawSmoothLine($pts);
            } else {
                for ($i = 0; $i < count($pts) - 1; $i++) {
                    $this->Line($pts[$i][0], $pts[$i][1], $pts[$i + 1][0], $pts[$i + 1][1]);
                }
            }

            $this->SetLineWidth(0.2);

            if ($markerSize > 0) {
                $this->SetFillColor($r, $g, $b);
                foreach ($pts as $pt) {
                    $this->_drawCircle($pt[0], $pt[1], $markerSize * 0.5);
                }
            }

            if ($showLabels) {
                $this->_cf('B', $labelFS);
                $this->SetTextColor($r, $g, $b);
                foreach ($pts as $i => $pt) {
                    $label = $this->formatChartNumber((float) $data[$i]);
                    $tw = $this->_sw($label);
                    $this->SetXY($pt[0] - $tw / 2, $pt[1] - $labelFS * 0.35 - 2);
                    $this->Cell($tw, $labelFS * 0.35, $label, 0, 0, 'C');
                }
                $this->SetTextColor(0, 0, 0);
            }
        }

        $this->SetDrawColor(0, 0, 0);

        if ($showLegend && $legendPos === 'bottom') {
            $legendY = $plotY + $plotHeight;
            $this->renderBarLegend($seriesList, $colors, $x0, $legendY + 1, $width, 'bottom');
        }
    }

    private function renderPieOrDonut(array $config, bool $isDonut): void
    {
        $chart = $config['chart'] ?? [];
        $plotOpts = $config['plotOptions'] ?? [];
        $legendOpts = $config['legend'] ?? [];

        $cx = (float) ($chart['x'] ?? 60);
        $cy = (float) ($chart['y'] ?? 80);
        $size = (float) ($chart['size'] ?? 80);

        $radius = $size / 2;

        $holeRatio = 0.0;
        $centerLabelText = 'Total';
        $centerLabelFontSize = 20;
        $showLabelTotal = false;

        if ($isDonut) {
            $rawSize = $plotOpts['pie']['donut']['size'] ?? '55%';
            $holeRatio = max(0.1, min(0.9, (float) rtrim($rawSize, '%') / 100));
            $donutLabels = $plotOpts['pie']['donut']['labels']['total'] ?? [];
            $showLabelTotal = (bool) ($donutLabels['show'] ?? false);
            $centerLabelText = (string) ($donutLabels['label'] ?? 'Total');
            $centerLabelFontSize = (int) ($donutLabels['fontSize'] ?? 20);
        }

        $rInner = $radius * $holeRatio;

        $series = $config['series'] ?? [];
        $labels = $config['labels'] ?? [];
        $colors = $config['colors'] ?? $this->chartDefaultColors;
        $total = array_sum($series);

        if (! empty($config['title']['text'])) {
            $titleFS = (int) ($config['title']['fontSize'] ?? 10);
            $this->_cf('B', $titleFS);
            $this->SetTextColor(55, 61, 63);
            $tw = $this->_sw($config['title']['text']);
            $this->SetXY($cx - $tw / 2, $cy - $radius - $titleFS * 0.5 - 3);
            $this->Cell($tw, $titleFS * 0.5, $config['title']['text']);
        }

        $segments = [];
        $startAngle = -90.0;

        foreach ($series as $i => $value) {
            $value = (float) $value;
            if ($value <= 0) {
                continue;
            }

            $sweep = 360.0 * $value / $total;
            $segments[] = [
                'index' => $i,
                'sweep' => $sweep,
                'startAngle' => $startAngle,
                'midAngle' => $startAngle + $sweep / 2,
                'endAngle' => $startAngle + $sweep,
                'pct' => round($value / $total * 100, 1),
                'color' => $colors[$i % count($colors)] ?? '#008FFB',
            ];

            $startAngle += $sweep;
        }

        foreach ($segments as $seg) {
            [$r, $g, $b] = $this->_chartHexToRgb($seg['color']);
            $this->SetFillColor($r, $g, $b);

            if ($isDonut) {
                $this->drawDonutSegment($cx, $cy, $radius, $holeRatio, $seg['startAngle'], $seg['endAngle']);
            } else {
                $this->drawPieSegment($cx, $cy, $radius, $seg['startAngle'], $seg['endAngle']);
            }
        }

        $labelR = ($radius + $rInner) / 2.0;
        $fs = 9;

        $this->_cf('B', $fs);
        $this->SetTextColor(255, 255, 255);

        foreach ($segments as $seg) {
            $lx = $cx + $labelR * cos(deg2rad($seg['midAngle']));
            $ly = $cy + $labelR * sin(deg2rad($seg['midAngle']));
            $text = $seg['pct'].'%';

            $this->SetFillColor(35, 45, 55);
            $this->SetXY($lx - 5.0, $ly - ($fs * 0.45) / 2);
            $this->Cell(10.0, $fs * 0.45, $text, 0, 0, 'C', true);
        }

        if ($showLabelTotal) {
            $cellW = $rInner * 1.8;
            $cellH = $centerLabelFontSize * 0.42;
            $yOff = ($centerLabelText !== '') ? -$cellH * 0.3 : 0.0;

            $this->SetTextColor(50, 50, 50);
            $this->SetFont('Arial', 'B', $centerLabelFontSize);
            $this->SetXY($cx - $cellW / 2, $cy - $cellH / 2 + $yOff);
            $this->Cell($cellW, $cellH, $total, 0, 0, 'C');

            if ($centerLabelText !== '') {
                $this->SetFont('Arial', '', $centerLabelFontSize * 0.72);
                $this->SetTextColor(120, 120, 120);
                $this->SetXY($cx - $cellW / 2, $cy + $cellH * 0.15);
                $this->Cell($cellW, $cellH * 0.75, $centerLabelText, 0, 0, 'C');
            }
        }

        $this->SetTextColor(0, 0, 0);

        if ((bool) ($legendOpts['show'] ?? true)) {
            $this->renderPieLegend(
                $series, $labels, $colors, $cx, $cy, $radius,
                (string) ($legendOpts['position'] ?? 'right'), $total
            );
        }
    }

    private function drawPieSegment(float $cx, float $cy, float $r, float $startDeg, float $endDeg): void
    {
        $points = $this->arcPoints($cx, $cy, $r, $startDeg, $endDeg);
        $path = sprintf('%.2f %.2f m', $cx, $cy);
        $path .= sprintf(' %.2f %.2f l', $points[0][0], $points[0][1]);

        for ($i = 1; $i + 2 < count($points); $i += 3) {
            $path .= sprintf(
                ' %.2f %.2f %.2f %.2f %.2f %.2f c',
                $points[$i][0], $points[$i][1],
                $points[$i + 1][0], $points[$i + 1][1],
                $points[$i + 2][0], $points[$i + 2][1]
            );
        }

        $path .= sprintf(' %.2f %.2f l f', $cx, $cy);
        $this->_out($this->_convertPath($path));
    }

    private function drawDonutSegment(float $cx, float $cy, float $r, float $holeRatio, float $startDeg, float $endDeg): void
    {
        $ri = $r * $holeRatio;
        $outerPoints = $this->arcPoints($cx, $cy, $r, $startDeg, $endDeg);
        $innerPoints = $this->arcPoints($cx, $cy, $ri, $endDeg, $startDeg);

        if (empty($outerPoints) || empty($innerPoints)) {
            return;
        }

        $path = sprintf('%.2f %.2f m', $outerPoints[0][0], $outerPoints[0][1]);

        for ($i = 1; $i + 2 < count($outerPoints); $i += 3) {
            $path .= sprintf(
                ' %.2f %.2f %.2f %.2f %.2f %.2f c',
                $outerPoints[$i][0], $outerPoints[$i][1],
                $outerPoints[$i + 1][0], $outerPoints[$i + 1][1],
                $outerPoints[$i + 2][0], $outerPoints[$i + 2][1]
            );
        }

        $path .= sprintf(' %.2f %.2f l', $innerPoints[0][0], $innerPoints[0][1]);

        for ($i = 1; $i + 2 < count($innerPoints); $i += 3) {
            $path .= sprintf(
                ' %.2f %.2f %.2f %.2f %.2f %.2f c',
                $innerPoints[$i][0], $innerPoints[$i][1],
                $innerPoints[$i + 1][0], $innerPoints[$i + 1][1],
                $innerPoints[$i + 2][0], $innerPoints[$i + 2][1]
            );
        }

        $path .= ' h f';
        $this->_out($this->_convertPath($path));
    }

    private function renderBarAxes(float $plotX, float $plotY, float $plotW, float $plotH, float $minVal, float $maxVal, int $tickAmt, array $categories, bool $showGrid, bool $horizontal): void
    {
        $this->SetDrawColor(200, 200, 200);
        $this->SetTextColor(100, 100, 100);
        $this->_cf('', 6);
        $range = $maxVal - $minVal;

        if (! $horizontal) {
            $this->SetDrawColor(150, 150, 150);
            $this->Line($plotX, $plotY + $plotH, $plotX + $plotW, $plotY + $plotH);
            $this->Line($plotX, $plotY, $plotX, $plotY + $plotH);

            for ($t = 0; $t <= $tickAmt; $t++) {
                $val = $minVal + $range * ($t / $tickAmt);
                $yPos = $plotY + $plotH - $plotH * ($t / $tickAmt);

                if ($showGrid && $t > 0) {
                    $this->SetDrawColor(220, 220, 220);
                    $this->Line($plotX, $yPos, $plotX + $plotW, $yPos);
                }

                $label = $this->formatChartNumber($val);
                $tw = $this->_sw($label);
                $this->SetXY($plotX - $tw - 1, $yPos - 1.5);
                $this->Cell($tw, 3, $label, 0, 0, 'R');
            }

            $numCats = count($categories);
            foreach ($categories as $ci => $cat) {
                $cx = $plotX + $plotW / $numCats * ($ci + 0.5);
                $this->SetXY($cx - 8, $plotY + $plotH + 1);
                $this->Cell(16, 3, $cat, 0, 0, 'C');
            }
        } else {
            $this->SetDrawColor(150, 150, 150);
            $this->Line($plotX, $plotY, $plotX + $plotW, $plotY);
            $this->Line($plotX, $plotY + $plotH, $plotX + $plotW, $plotY + $plotH);
            $this->Line($plotX, $plotY, $plotX, $plotY + $plotH);

            for ($t = 0; $t <= $tickAmt; $t++) {
                $val = $minVal + $range * ($t / $tickAmt);
                $xPos = $plotX + $plotW * ($t / $tickAmt);

                if ($showGrid && $t > 0) {
                    $this->SetDrawColor(220, 220, 220);
                    $this->Line($xPos, $plotY, $xPos, $plotY + $plotH);
                }

                $label = $this->formatChartNumber($val);
                $tw = $this->_sw($label);
                $this->SetXY($xPos - $tw / 2, $plotY + $plotH + 1);
                $this->Cell($tw, 3, $label, 0, 0, 'C');
            }

            $numCats = count($categories);
            foreach ($categories as $ci => $cat) {
                $yPos = $plotY + $plotH / $numCats * ($ci + 0.5);
                $tw = $this->_sw($cat);
                $this->SetXY($plotX - $tw - 1.5, $yPos - 1.5);
                $this->Cell($tw, 3, $cat, 0, 0, 'R');
            }
        }

        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
    }

    private function renderAnnotations(array $annotations, float $plotX, float $plotY, float $plotW, float $plotH, float $minVal, float $maxVal, array $categories, bool $horizontal): void
    {
        $range = $maxVal - $minVal ?: 1;

        foreach ($annotations['yaxis'] ?? [] as $ann) {
            $val = (float) ($ann['y'] ?? 0);
            $label = (string) ($ann['label'] ?? '');
            $color = (string) ($ann['color'] ?? '#FF4560');
            $dashArr = (int) ($ann['dashArray'] ?? 0);

            [$r, $g, $b] = $this->_chartHexToRgb($color);
            $this->SetDrawColor($r, $g, $b);
            $this->SetLineWidth(0.3);

            $yPos = $plotY + $plotH - $plotH * ($val - $minVal) / $range;

            if ($dashArr > 0) {
                $this->_dashedLine($plotX, $yPos, $plotX + $plotW, $yPos, $dashArr * 0.5);
            } else {
                $this->Line($plotX, $yPos, $plotX + $plotW, $yPos);
            }

            if ($label !== '') {
                $this->_cf('B', 6);
                $this->SetTextColor($r, $g, $b);
                $tw = $this->_sw($label);
                $this->SetXY($plotX + $plotW - $tw - 0.5, $yPos - 3);
                $this->Cell($tw, 3, $label, 0, 0, 'R');
                $this->SetTextColor(0, 0, 0);
            }

            $this->SetLineWidth(0.2);
            $this->SetDrawColor(0, 0, 0);
        }

        $numCats = max(count($categories), 1);
        foreach ($annotations['xaxis'] ?? [] as $ann) {
            $catLabel = (string) ($ann['x'] ?? '');
            $label = (string) ($ann['label'] ?? $catLabel);
            $color = (string) ($ann['color'] ?? '#775DD0');

            $ci = array_search($catLabel, $categories);
            if ($ci === false) {
                continue;
            }

            [$r, $g, $b] = $this->_chartHexToRgb($color);
            $this->SetDrawColor($r, $g, $b);
            $this->SetLineWidth(0.3);

            $xPos = $plotX + $plotW / $numCats * ($ci + 0.5);
            $this->Line($xPos, $plotY, $xPos, $plotY + $plotH);

            if ($label !== '') {
                $this->_cf('B', 6);
                $this->SetTextColor($r, $g, $b);
                $tw = $this->_sw($label);
                $this->SetXY($xPos - $tw / 2, $plotY + 1);
                $this->Cell($tw, 3, $label, 0, 0, 'C');
                $this->SetTextColor(0, 0, 0);
            }

            $this->SetLineWidth(0.2);
            $this->SetDrawColor(0, 0, 0);
        }
    }

    private function renderPieLegend(array $series, array $labels, array $colors, float $cx, float $cy, float $radius, string $position, float $total): void
    {
        $this->_cf('', 7);

        $swatchSize = 3;
        $lineH = 5;
        $n = count($series);
        $totalSafe = $total == 0 ? 1 : $total;

        $buildLabel = function (int $i, float $value) use ($labels, $totalSafe): string {
            $name = $labels[$i] ?? "Serie $i";

            return $name.' ('.round($value / $totalSafe * 100, 1).'%)';
        };

        $drawSwatch = function (float $x, float $y, int $i) use ($colors, $swatchSize, $lineH): void {
            [$r, $g, $b] = $this->_chartHexToRgb($colors[$i % count($colors)] ?? '#008FFB');
            $this->SetFillColor($r, $g, $b);
            $this->Rect($x, $y + ($lineH - $swatchSize) / 2, $swatchSize, $swatchSize, 'F');
            $this->SetTextColor(60, 60, 60);
        };

        switch ($position) {
            case 'right':
                $lx = $cx + $radius + 5;
                $ly = $cy - ($n * $lineH) / 2;

                foreach ($series as $i => $value) {
                    $label = $buildLabel($i, (float) $value);
                    $drawSwatch($lx, $ly, $i);
                    $this->SetXY($lx + $swatchSize + 1, $ly);
                    $this->Cell(30, $lineH, $label, 0, 0, 'L');
                    $ly += $lineH;
                }
                break;

            case 'left':
                $maxLabelW = 25;
                $lx = $cx - $radius - $maxLabelW - 8;
                $ly = $cy - ($n * $lineH) / 2;

                foreach ($series as $i => $value) {
                    $label = $buildLabel($i, (float) $value);
                    $drawSwatch($lx, $ly, $i);
                    $this->SetXY($lx + $swatchSize + 1, $ly);
                    $this->Cell($maxLabelW, $lineH, $label, 0, 0, 'L');
                    $ly += $lineH;
                }
                break;

            case 'top':
            case 'bottom':
            default:
                $itemWidths = [];
                $totalW = 0;

                foreach ($series as $i => $value) {
                    $label = $buildLabel($i, (float) $value);
                    $w = $swatchSize + 1 + $this->_sw($label) + 4;
                    $itemWidths[] = ['w' => $w, 'label' => $label];
                    $totalW += $w;
                }

                $maxWidth = $radius * 2.5;

                $rows = [];
                $currentRow = [];
                $rowW = 0;

                foreach ($itemWidths as $i => $item) {
                    if ($rowW + $item['w'] > $maxWidth && ! empty($currentRow)) {
                        $rows[] = $currentRow;
                        $currentRow = [];
                        $rowW = 0;
                    }
                    $currentRow[] = $i;
                    $rowW += $item['w'];
                }
                if (! empty($currentRow)) {
                    $rows[] = $currentRow;
                }

                $totalRows = count($rows);
                $legendH = $totalRows * $lineH;

                $gridStartY = ($position === 'bottom')
                    ? $cy + $radius + 4
                    : $cy - $radius - $legendH - 4;

                foreach ($rows as $rowIndex => $rowItems) {
                    $rowTotalW = array_sum(array_map(fn ($i) => $itemWidths[$i]['w'], $rowItems));
                    $startX = $cx - $rowTotalW / 2;
                    $lY = $gridStartY + $rowIndex * $lineH;

                    foreach ($rowItems as $i) {
                        ['w' => $w, 'label' => $label] = $itemWidths[$i];
                        $drawSwatch($startX, $lY, $i);
                        $this->SetXY($startX + $swatchSize + 1, $lY);
                        $this->Cell($w - $swatchSize - 1, $lineH, $label, 0, 0, 'L');
                        $startX += $w;
                    }
                }
                break;
        }

        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor(0, 0, 0);
    }

    private function renderBarLegend(array $seriesList, array $colors, float $x, float $y, float $width, string $position): float
    {
        $this->_cf('', 7);
        $swatchSize = 3;
        $lineH = 5;
        $padding = 2;

        $items = [];
        foreach ($seriesList as $i => $serie) {
            $name = $serie['name'] ?? "Serie $i";
            $w = $swatchSize + 1 + $this->_sw($name) + 4;
            $items[] = [
                'name' => $name,
                'w' => $w,
                'color' => $colors[$i % count($colors)] ?? '#008FFB',
            ];
        }

        $rows = [];
        $currentRow = [];
        $rowW = 0;

        foreach ($items as $i => $item) {
            if ($rowW + $item['w'] > $width && ! empty($currentRow)) {
                $rows[] = $currentRow;
                $currentRow = [];
                $rowW = 0;
            }
            $currentRow[] = $i;
            $rowW += $item['w'];
        }
        if (! empty($currentRow)) {
            $rows[] = $currentRow;
        }

        $totalRows = count($rows);
        $totalHeight = $totalRows * $lineH + $padding;

        foreach ($rows as $rowIndex => $rowItems) {
            $rowTotalW = array_sum(array_map(fn ($i) => $items[$i]['w'], $rowItems));
            $startX = $x + ($width - $rowTotalW) / 2;
            $lY = $y + $rowIndex * $lineH;

            foreach ($rowItems as $i) {
                $item = $items[$i];
                [$r, $g, $b] = $this->_chartHexToRgb($item['color']);
                $this->SetFillColor($r, $g, $b);
                $this->Rect($startX, $lY + ($lineH - $swatchSize) / 2, $swatchSize, $swatchSize, 'F');
                $this->SetTextColor(60, 60, 60);
                $this->SetXY($startX + $swatchSize + 1, $lY);
                $this->Cell($item['w'] - $swatchSize - 1, $lineH, $item['name'], 0, 0, 'L');
                $startX += $item['w'];
            }
        }

        $this->SetTextColor(0, 0, 0);

        return $totalHeight;
    }

    private function renderChartTitle(array $config, float $x, float $y, float $width): void
    {
        if (empty($config['title']['text'])) {
            return;
        }
        $fs = (int) ($config['title']['fontSize'] ?? 10);
        $this->_cf('B', $fs);
        $this->SetTextColor(55, 61, 63);
        $this->SetXY($x, $y);
        $this->Cell($width, $fs * 0.5, $config['title']['text'], 0, 0, 'L');
    }

    private function getChartTitleOffset(array $config): float
    {
        if (empty($config['title']['text'])) {
            return 0;
        }
        $fs = (int) ($config['title']['fontSize'] ?? 10);

        return $fs * 0.5 + 2;
    }

    private function _drawLineArea(array $pts, float $plotX, float $plotY, float $plotW, float $plotH, int $r, int $g, int $b, float $opacity, string $curve): void
    {
        $bottom = $plotY + $plotH;
        $n = count($pts);

        $fr = (int) ($r + (255 - $r) * (1 - $opacity));
        $fg = (int) ($g + (255 - $g) * (1 - $opacity));
        $fb = (int) ($b + (255 - $b) * (1 - $opacity));

        $this->SetFillColor($fr, $fg, $fb);

        $path = sprintf('%.2f %.2f m', $pts[0][0], $bottom);
        $path .= sprintf(' %.2f %.2f l', $pts[0][0], $pts[0][1]);

        if ($curve === 'smooth' && $n >= 2) {
            $cps = $this->_catmullRomControlPoints($pts);
            for ($i = 0; $i < $n - 1; $i++) {
                $path .= sprintf(
                    ' %.2f %.2f %.2f %.2f %.2f %.2f c',
                    $cps[$i][0], $cps[$i][1],
                    $cps[$i][2], $cps[$i][3],
                    $pts[$i + 1][0], $pts[$i + 1][1]
                );
            }
        } else {
            for ($i = 1; $i < $n; $i++) {
                $path .= sprintf(' %.2f %.2f l', $pts[$i][0], $pts[$i][1]);
            }
        }

        $path .= sprintf(' %.2f %.2f l', $pts[$n - 1][0], $bottom);
        $path .= ' h f';
        $this->_out($this->_convertPath($path));
    }

    private function _drawSmoothLine(array $pts): void
    {
        $n = count($pts);
        $cps = $this->_catmullRomControlPoints($pts);

        $path = sprintf('%.2f %.2f m', $pts[0][0], $pts[0][1]);
        for ($i = 0; $i < $n - 1; $i++) {
            $path .= sprintf(
                ' %.2f %.2f %.2f %.2f %.2f %.2f c',
                $cps[$i][0], $cps[$i][1],
                $cps[$i][2], $cps[$i][3],
                $pts[$i + 1][0], $pts[$i + 1][1]
            );
        }
        $path .= ' S';
        $this->_out($this->_convertPath($path));
    }

    private function _catmullRomControlPoints(array $pts, float $tension = 0.4): array
    {
        $n = count($pts);
        $cps = [];

        for ($i = 0; $i < $n - 1; $i++) {
            $p0 = $pts[max(0, $i - 1)];
            $p1 = $pts[$i];
            $p2 = $pts[$i + 1];
            $p3 = $pts[min($n - 1, $i + 2)];

            $cps[] = [
                $p1[0] + ($p2[0] - $p0[0]) * $tension,
                $p1[1] + ($p2[1] - $p0[1]) * $tension,
                $p2[0] - ($p3[0] - $p1[0]) * $tension,
                $p2[1] - ($p3[1] - $p1[1]) * $tension,
            ];
        }

        return $cps;
    }

    private function _drawCircle(float $cx, float $cy, float $r): void
    {
        $k = $this->k;
        $h = $this->h;
        $cp = $r * 0.5523;

        $this->_out(sprintf(
            '%.2f %.2f m %.2f %.2f %.2f %.2f %.2f %.2f c '
                .'%.2f %.2f %.2f %.2f %.2f %.2f c '
                .'%.2f %.2f %.2f %.2f %.2f %.2f c '
                .'%.2f %.2f %.2f %.2f %.2f %.2f c f',
            ($cx) * $k, ($h - $cy - $r) * $k,
            ($cx + $cp) * $k, ($h - $cy - $r) * $k,
            ($cx + $r) * $k, ($h - $cy - $cp) * $k,
            ($cx + $r) * $k, ($h - $cy) * $k,
            ($cx + $r) * $k, ($h - $cy + $cp) * $k,
            ($cx + $cp) * $k, ($h - $cy + $r) * $k,
            ($cx) * $k, ($h - $cy + $r) * $k,
            ($cx - $cp) * $k, ($h - $cy + $r) * $k,
            ($cx - $r) * $k, ($h - $cy + $cp) * $k,
            ($cx - $r) * $k, ($h - $cy) * $k,
            ($cx - $r) * $k, ($h - $cy - $cp) * $k,
            ($cx - $cp) * $k, ($h - $cy - $r) * $k,
            ($cx) * $k, ($h - $cy - $r) * $k
        ));
    }

    private function _dashedLine(float $x1, float $y1, float $x2, float $y2, float $dashLen = 1.0): void
    {
        $totalLen = sqrt(($x2 - $x1) ** 2 + ($y2 - $y1) ** 2);
        if ($totalLen <= 0) {
            return;
        }

        $dx = ($x2 - $x1) / $totalLen * $dashLen;
        $dy = ($y2 - $y1) / $totalLen * $dashLen;
        $drawing = true;
        $cx = $x1;
        $cy = $y1;
        $remaining = $totalLen;

        while ($remaining > 0) {
            $len = min($dashLen, $remaining);
            $nx = $cx + $dx * ($len / $dashLen);
            $ny = $cy + $dy * ($len / $dashLen);
            if ($drawing) {
                $this->Line($cx, $cy, $nx, $ny);
            }
            $cx = $nx;
            $cy = $ny;
            $remaining -= $dashLen;
            $drawing = ! $drawing;
        }
    }

    private function _resolveColor(array $colors, int $seriesIndex, float $value): array
    {
        $color = $colors[$seriesIndex % count($colors)] ?? '#008FFB';

        if (is_array($color) && isset($color['threshold'])) {
            $hex = $value >= (float) $color['threshold']
                ? ($color['above'] ?? '#00E396')
                : ($color['below'] ?? '#FF4560');

            return $this->_chartHexToRgb($hex);
        }

        return $this->_chartHexToRgb((string) $color);
    }

    private function arcPoints(float $cx, float $cy, float $r, float $startDeg, float $endDeg): array
    {
        $startRad = deg2rad($startDeg);
        $endRad = deg2rad($endDeg);
        $sweep = $endRad - $startRad;
        $absSweep = abs($sweep);

        $numSegs = max(1, (int) ceil($absSweep / (M_PI / 2)));
        $segSweep = $sweep / $numSegs;

        $points = [];
        $points[] = [
            $cx + $r * cos($startRad),
            $cy + $r * sin($startRad),
        ];

        for ($s = 0; $s < $numSegs; $s++) {
            $a0 = $startRad + $s * $segSweep;
            $a1 = $a0 + $segSweep;
            $k = (4 / 3) * tan($segSweep / 4);

            $points[] = [
                $cx + $r * (cos($a0) - $k * sin($a0)),
                $cy + $r * (sin($a0) + $k * cos($a0)),
            ];
            $points[] = [
                $cx + $r * (cos($a1) + $k * sin($a1)),
                $cy + $r * (sin($a1) - $k * cos($a1)),
            ];
            $points[] = [
                $cx + $r * cos($a1),
                $cy + $r * sin($a1),
            ];
        }

        return $points;
    }

    private function _convertPath(string $path): string
    {
        $k = $this->k;
        $h = $this->h * $k;

        return preg_replace_callback(
            '/(-?[\d.]+)\s+(-?[\d.]+)/',
            function ($m) use ($k, $h) {
                $x = (float) $m[1] * $k;
                $y = $h - (float) $m[2] * $k;

                return sprintf('%.2f %.2f', $x, $y);
            },
            $path
        );
    }

    private function _chartRoundedRect(float $x, float $y, float $w, float $h, float $r, string $style = 'F'): void
    {
        if ($r <= 0 || $w <= 0 || $h <= 0) {
            $this->Rect($x, $y, $w, $h, $style);

            return;
        }

        $r = min($r, $w / 2, $h / 2);
        $hp = $this->h;
        $k = $this->k;
        $cp = $r * 0.5523;

        $op = match ($style) {
            'F' => 'f',
            'FD', 'DF' => 'B',
            default => 'S',
        };

        $this->_out(sprintf(
            '%.2f %.2f m '
                .'%.2f %.2f l '
                .'%.2f %.2f %.2f %.2f %.2f %.2f c '
                .'%.2f %.2f l '
                .'%.2f %.2f %.2f %.2f %.2f %.2f c '
                .'%.2f %.2f l '
                .'%.2f %.2f %.2f %.2f %.2f %.2f c '
                .'%.2f %.2f l '
                .'%.2f %.2f %.2f %.2f %.2f %.2f c '
                .'%s',
            ($x + $r) * $k, ($hp - $y) * $k,
            ($x + $w - $r) * $k, ($hp - $y) * $k,
            ($x + $w - $r + $cp) * $k, ($hp - $y) * $k,
            ($x + $w) * $k, ($hp - ($y + $r - $cp)) * $k,
            ($x + $w) * $k, ($hp - ($y + $r)) * $k,
            ($x + $w) * $k, ($hp - ($y + $h - $r)) * $k,
            ($x + $w) * $k, ($hp - ($y + $h - $r + $cp)) * $k,
            ($x + $w - $r + $cp) * $k, ($hp - ($y + $h)) * $k,
            ($x + $w - $r) * $k, ($hp - ($y + $h)) * $k,
            ($x + $r) * $k, ($hp - ($y + $h)) * $k,
            ($x + $r - $cp) * $k, ($hp - ($y + $h)) * $k,
            $x * $k, ($hp - ($y + $h - $r + $cp)) * $k,
            $x * $k, ($hp - ($y + $h - $r)) * $k,
            $x * $k, ($hp - ($y + $r)) * $k,
            $x * $k, ($hp - ($y + $r - $cp)) * $k,
            ($x + $r - $cp) * $k, ($hp - $y) * $k,
            ($x + $r) * $k, ($hp - $y) * $k,
            $op
        ));
    }

    private function _chartHexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    public function formatChartNumber(float $value): string
    {
        if (abs($value) >= 1_000_000) {
            return round($value / 1_000_000, 1).'M';
        }
        if (abs($value) >= 1_000) {
            return round($value / 1_000, 1).'K';
        }

        return (string) round($value, $value == (int) $value ? 0 : 1);
    }
}
