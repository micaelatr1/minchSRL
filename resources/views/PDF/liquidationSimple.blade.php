<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Liquidación {{ $l->lote }}</title>
    <style>
        @page { margin: 7mm 7mm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.3;
            color: #1e2a3a;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 0; padding: 2px 4px; vertical-align: middle; font-size: 9pt }
        .label { font-weight: 600;  white-space: nowrap; }
        .section-title { font-weight: 700; font-size: 9pt; background: #FFE699; text-align: center; border: 1px solid #000; }
        .num { text-align: right; font-family: 'Courier New', monospace; white-space: nowrap; }
        .bl { border-left: 1px solid #000; }
        .br { border-right: 1px solid #000; }
        .bb { border-bottom: 1px solid #000; }
    </style>
</head>
<body>
@php
    $metal = $l->metal;
    $ley = $metal === 'zn' ? (float) $l->zinc_grade : (float) $l->lead_grade;
    $precioMetal = $metal === 'zn' ? (float) $l->market_zn : (float) $l->market_pb;
    $nim = $metal === 'zn' ? (float) $l->quincenal_zn : (float) $l->quincenal_pb;
    $dedMetal = $metal === 'zn' ? 8 : 3;
    $dedAg = $metal === 'zn' ? 3 : 1.6;
    $payAg = $metal === 'zn' ? 0.7 : 0.95;
    $payPb = $metal === 'pb' ? 0.95 : 1;
    $tmsP = $metal === 'pb' ? 5 : 3;
    $tms = round($l->tmh - ($l->tmh * $l->h2o / 100), $tmsP);
    $tmnsRaw = -($tms * $l->merma / 100) + $tms;
    $tmns = round($tmnsRaw, $tmsP);
    $ag = $l->dm * 100;
    $platacalc = ($l->dm * 100) / 31.1035;
    $leyPorc = $ley - $dedMetal;
    $valMetal = round($leyPorc * $precioMetal / 100, 2) * $payPb;

    $plataPorc = $platacalc - $dedAg;
    $plataVal = $l->market_ag * $payAg;
    $totalPlata = round($plataPorc * $plataVal, 2);
    $baseEsc = max($precioMetal - (float) $l->base, 0);
    $baseTotal = $baseEsc * (float) $l->base_percentage;
    $refTot = round($platacalc * (float) $l->refinacion, 2);

    // Penalties
    $calcPenalty = fn($val, $threshold, $usd, $pct) =>
        $val > $threshold ? ($val - $threshold) * ($usd / max($pct, 0.001)) : 0;

    $asT = $calcPenalty((float) $l->as_pct, (float) $l->p_as, (float) $l->p_as_usd, (float) $l->p_as_pct);
    $sbT = $calcPenalty((float) $l->sb_pct, (float) $l->p_sb, (float) $l->p_sb_usd, (float) $l->p_sb_pct);
    $feT = $calcPenalty((float) $l->fe_pct, (float) $l->p_fe, (float) $l->p_fe_usd, (float) $l->p_fe_pct);
    $sio2T = $calcPenalty((float) $l->sio2_pct, (float) $l->p_sio2, (float) $l->p_sio2_usd, (float) $l->p_sio2_pct);
    $snT = $calcPenalty((float) $l->sn_pct, (float) $l->p_sn, (float) $l->p_sn_usd, (float) $l->p_sn_pct);

    $totalCT = (float) $l->maquila + $baseTotal + $asT + $sbT + $feT + $sio2T + $snT + ($metal === 'pb' ? $refTot : 0);
    $valorNeto = round($valMetal + $totalPlata - $totalCT, 2);
    $totalNetoUSD = ($valMetal + $totalPlata - $totalCT) * $tmns;

    $gastosOp = (
        ($tmns * 1000 * $ley / 100 * 2.2046223 * $nim * 5 / 100)
        + ($l->dm * $tmns / 10 * 32.15073 * (float) $l->quincenal_ag * 6 / 100)
    ) - (
        ($tmns * 1000 * $ley / 100 * 2.2046223 * $nim * 3 / 100)
        + ($l->dm * $tmns / 10 * 32.15073 * (float) $l->quincenal_ag * 3.6 / 100)
    );

    $totFlete = (float) $l->flete * $l->tmh;
    $totRoll = (float) $l->rollback * $l->tmh;
    $totRemesa = ($totalNetoUSD * (float) $l->remesa_pct / 100) + (float) $l->remesa_fijo;
    $totGastos = $totFlete + $totRoll + $gastosOp + $totRemesa;
    $totalUSD = $totalNetoUSD - $totGastos;
    $totalBs = $totalUSD * (float) $l->tc;

    $regMetal = $metal === 'zn' ? (float) $l->regalia_zn : (float) $l->regalia_pb;
    $regMetalBs = ($tmns * 1000 * $ley / 100 * 2.2046223 * $nim * $regMetal / 100) * (float) $l->factor_regalia;
    $regAgBs = ($l->dm * $tmns / 10 * 32.15073 * (float) $l->quincenal_ag * (float) $l->regalia_ag / 100) * (float) $l->factor_regalia;
    $totalRM = $regMetalBs + $regAgBs;

    $cnsBs = $totalBs * (float) $l->cns_pct / 100;
    $comBs = $totalBs * (float) $l->comibol_pct / 100;
    $fedBs = $totalBs * (float) $l->fedecomin_pct / 100;
    $fenBs = $totalBs * (float) $l->fencomin_pct / 100;
    $apBs = $totalBs * (float) $l->aporte_coop_pct / 100;
    $totAportes = $cnsBs + $comBs + $fedBs + $fenBs + $apBs;
    $costoFinal = $totalBs - $totalRM - $totAportes;

    $f = fn($v, $d = 2) => number_format((float) $v, $d, ',', '.');
    $fc = fn($v, $d = 2) => ceil((float) $v * pow(10, $d)) / pow(10, $d);
    $fc_display = fn($v, $d = 2) => number_format((float) $fc($v, $d), $d, ',', '.');
    $valorNetoCeil = $fc($valMetal + $totalPlata - $totalCT);
    $tmnsCeil = $fc($tmnsRaw, 3);
    $totalNetoUSDCeil = ($valMetal + $totalPlata - $totalCT) * $tmns;
    $metalNombre = $metal === 'zn' ? 'Zinc' : 'Plomo';
    $metalLabel = $metal === 'zn' ? 'Zn' : 'Pb';
@endphp

<table style="width:100%; border-collapse:collapse;">
    {{-- HEADER --}}
    <tr>
        <td rowspan="3" width="25%" align="center" style="padding-top:10px;font-size:12px;vertical-align:top;border:1px solid #000;">
            <img src="{{ public_path('image/logo.png') }}" alt="" height="40"><br><br>
            <strong>EMPRESA MINCH S.R.L</strong>
        </td>
        <td colspan="2" rowspan="3" width="55%" align="center" style="padding:1px;border:1px solid #000;">
            <strong style="font-size: 18px;">LIQUIDACIÓN DE MINERALES {{ $metalLabel }}<br></strong>
        </td>
        <td width="20%" align="center" style="padding:1px;border:1px solid #000;">
            <strong style="font-size: 14px;">Código: P02 {{$metalLabel=='Pb'?'F04':'F03'}}</strong>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding:1px;border:1px solid #000;">
            <strong style="font-size: 14px;">Revisión: 0</strong>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding:1px;border:1px solid #000;background:#2c3e50;color:white;">
            <strong>PÚBLICO</strong>
        </td>
    </tr>
</table>

<table style="width:100%; border-collapse:collapse;">
    {{-- DATOS GENERALES --}}

    <tr>
        <td class="label bl" colspan="2" style="text-align:right;">N° LOTE:</td>
        <td colspan="2" style="text-align:left;">{{ $l->lote }}</td>
        <td class="label" colspan="2" style="text-align:right;">FECHA DE LIQ.:</td>
        <td class="br" colspan="2" style="text-align:left;">{{ $l->date->locale('es')->isoFormat('D \d\e MMMM \d\e YYYY') }}</td>
    </tr>
    <tr>
        <td class="label bl" colspan="2" style="text-align:right;">PROVEEDOR:</td>
        <td colspan="2" style="text-align:left;">{{ $l->full_name }}</td>
        <td class="label" colspan="2" style="text-align:right;">C.I.:</td>
        <td class="br" colspan="2" style="text-align:left;">{{ $l->ci }}</td>
    </tr>
    <tr>
        <td class="label bl" colspan="2" style="text-align:right;">COOP. MINERA:</td>
        <td colspan="2" style="text-align:left;">{{ $l->cooperative_name }}</td>
        <td class="label" colspan="2" style="text-align:right;">NIM:</td>
        <td class="br" colspan="2" style="text-align:left;">{{ $l->nim }}</td>
    </tr>
    <tr>
        <td class="label bl" colspan="2" style="text-align:right;">CONCESIÓN:</td>
        <td colspan="2" style="text-align:left;">{{ $l->concession }}</td>
        <td class="label" colspan="2" style="text-align:right;">LAB. QUÍMICO:</td>
        <td class="br" colspan="2" style="text-align:left;">{{ $l->lab_quimico }}</td>
    </tr>
    <tr>
        <td class="label bl" colspan="2" style="text-align:right;">MINA:</td>
        <td colspan="2" style="text-align:left;">{{ $l->mine }}</td>
        <td class="label" colspan="2" style="text-align:right;">NÚMERO LAB.:</td>
        <td class="br" colspan="2" style="text-align:left;">{{ $l->number_lab }}</td>
    </tr>
    <tr>
        <td class="label bl bb" colspan="2" style="text-align:right;">MUNICIPIO:</td>
        <td class="bb" colspan="2" style="text-align:left;">{{ $l->municipality }}</td>
        <td class="label bb" colspan="2" style="text-align:right;">CÓDIGO:</td>
        <td class="br bb" colspan="2" style="text-align:left;">{{ $l->codigo }}</td>
    </tr>

    {{-- COTIZACIONES --}}
    <tr><td class="section-title" colspan="4" style="border-right:none;">Cotizaciones Quincenales</td><td class="section-title" colspan="4" style="border-left:none;">Cotizaciones</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="label" colspan="1" style="text-align:center;">{{ $metalLabel }}</td><td class="num" colspan="1" style="text-align:center;">{{ $f($nim) }}</td><td class="num" colspan="1" style="text-align:center;">USD/TM</td><td colspan="1"></td><td class="label" colspan="1" style="text-align:center;">{{ $metalLabel }}</td><td class="num" colspan="1" style="text-align:center;">{{ $f($precioMetal) }}</td><td class="num br" colspan="1" style="text-align:center;">USD/TM</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="label" colspan="1" style="text-align:center;">Ag</td><td class="num" colspan="1" style="text-align:center;">{{ $f($l->quincenal_ag) }}</td><td class="num" colspan="1" style="text-align:center;">USD/OZ</td><td colspan="1"></td><td class="label" colspan="1" style="text-align:center;">Ag</td><td class="num" colspan="1" style="text-align:center;">{{ $f($l->market_ag) }}</td><td class="num br" colspan="1" style="text-align:center;">USD/OZ</td></tr>

    {{-- PESOS / LEYES / CONTENIDOS --}}
    <tr><td class="bl bb" colspan="1" style="background:#FFE699;"></td><td class="bb" colspan="1" style="background:#FFE699;font-weight:700;text-align:center;">Pesos</td><td class="bb" colspan="1" style="background:#FFE699;"></td><td class="bb" colspan="1" style="background:#FFE699;font-weight:700;text-align:center;">Leyes</td><td class="bb" colspan="1" style="background:#FFE699;"></td><td class="bb" colspan="1" style="background:#FFE699;font-weight:700;text-align:center;">Contenidos</td><td class="bb" colspan="1" style="background:#FFE699;"></td><td class="br bb" colspan="1" style="background:#FFE699;"></td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">TMH</td><td class="num" colspan="1">{{ $f($l->tmh, 3) }}</td><td class="label" colspan="1">{{ $metalLabel }}</td><td class="num" colspan="1">{{ $f($ley) }}%</td><td class="label" colspan="1">As</td><td class="num" colspan="1">{{ $f($l->as_pct) }}%</td><td class="br" colspan="1"></td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">H2O</td><td class="num" colspan="1">{{ $f($l->h2o) }}%</td><td class="label" colspan="1">Ag</td><td class="num" colspan="1">{{ $f($ag) }} Gr/Tm</td><td class="label" colspan="1">Sb</td><td class="num" colspan="1">{{ $f($l->sb_pct) }}%</td><td class="br" colspan="1"></td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">TMS</td><td class="num" colspan="1">{{ $f($tms, 3) }}</td><td class="label" colspan="1"></td><td class="num" colspan="1">{{ $f($l->dm) }}Dm</td><td class="label" colspan="1">Fe</td><td class="num" colspan="1">{{ $f($l->fe_pct) }}%</td><td class="br" colspan="1"></td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">MERMA</td><td class="num" colspan="1">{{ $f($l->merma) }}%</td><td class="label" colspan="1">Maquila</td><td class="num" colspan="1">{{ $l->maquila?? '-' }}</td><td class="label" colspan="1">SiO2</td><td class="num" colspan="1">{{ $f($l->sio2_pct) }}%</td><td class="br" colspan="1"></td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">TMNS</td><td class="num" colspan="1">{{ $fc_display($tmnsRaw, 3) }}</td><td class="label" colspan="1">Base</td><td class="num" colspan="1">{{ $f($l->base) }}</td><td class="label" colspan="1">Sn</td><td class="num" colspan="1">{{ $f($l->sn_pct) }}%</td><td class="br" colspan="1"></td></tr>
    @if ($metal === 'pb')
    <tr><td class="bl bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="label bb" colspan="1">Ref.</td><td class="num bb" colspan="1">{{ $f($l->refinacion) }}</td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="br bb" colspan="1"></td></tr>
    @else
    <tr><td class="bl bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="br bb" colspan="1"></td></tr>
    @endif

    {{-- DEDUCCIONES (Bruto) --}}
    <tr><td colspan="8" class="section-title" style="text-align:center;">Deducciones</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">{{ $metalNombre }}</td><td class="num" colspan="1">{{ $f($ley) }}</td><td class="num" colspan="1">-{{ $dedMetal }}</td><td class="num" colspan="1">{{ $f($leyPorc) }} %</td><td colspan="1"></td><td class="num" colspan="1">{{ $precioMetal }}</td><td class="num br" colspan="1">{{ $fc_display($valMetal) }}</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">Plata</td><td class="num" colspan="1">{{ $f($platacalc) }}</td><td class="num" colspan="1">-{{ $dedAg }}</td><td class="num" colspan="1">{{ $f($plataPorc) }} Oz/Tm</td><td colspan="1"></td><td class="num" colspan="1">{{ $f($plataVal) }}</td><td class="num br" colspan="1">{{ $f($totalPlata) }}</td></tr>

    {{-- TOTALES --}}
    <tr><td class="bl" colspan="1" style="border-top:1px solid #000;"></td><td class="" colspan="1" style="border-top:1px solid #000;">Total USD</td><td class="num" colspan="1" style="border-top:1px solid #000;">{{ $f($totalUSD / $tmns) }}</td><td colspan="1" style="border-top:1px solid #000;"></td><td colspan="1" style="border-top:1px solid #000;"></td><td colspan="1" style="border-top:1px solid #000;"></td><td colspan="1" style="border-top:1px solid #000;"></td><td class="num br" colspan="1" style="border-top:1px solid #000;"><strong>{{ $f($totalUSD) }}</strong> </td></tr>
    <tr><td class="bl bb" colspan="1"></td><td class="bb" colspan="1">Total Bs</td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="bb" colspan="1"></td><td class="num br bb fw-7" colspan="1"><strong>{{ $f($totalBs) }}</strong> </td></tr>
    {{-- REGALÍAS --}}
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">Regalía {{ $metalLabel }}</td><td class="num" colspan="1">{{ $f($regMetal, 3) }}%</td><td colspan="4"></td><td class="num br" colspan="1">{{ $f($regMetalBs) }}</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">Regalía Ag</td><td class="num" colspan="1">{{ $f($l->regalia_ag, 3) }}%</td><td colspan="4"></td><td class="num br" colspan="1">{{ $f($regAgBs) }}</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="fw-7" colspan="1">Total RM</td><td colspan="1"></td><td colspan="4"></td><td class="num br" colspan="1" style="border-top:1px solid #000;"><strong>{{ $f($totalRM) }}</strong> </td></tr>

    {{-- APORTES --}}
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">CNS</td><td class="num" colspan="1">{{ $f($l->cns_pct) }}%</td><td colspan="4"></td><td class="num br" colspan="1">{{ $f($cnsBs) }}</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">Comibol</td><td class="num" colspan="1">{{ $f($l->comibol_pct) }}%</td><td colspan="4"></td><td class="num br" colspan="1">{{ $f($comBs) }}</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">Fedecomin</td><td class="num" colspan="1">{{ $f($l->fedecomin_pct) }}%</td><td colspan="4"></td><td class="num br" colspan="1">{{ $f($fedBs) }}</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">Fencomin</td><td class="num" colspan="1">{{ $f($l->fencomin_pct) }}%</td><td colspan="4"></td><td class="num br" colspan="1">{{ $f($fenBs) }}</td></tr>
    <tr><td class="bl" colspan="1"></td><td class="" colspan="1">Aporte Coop.</td><td class="num" colspan="1">{{ $f($l->aporte_coop_pct) }}%</td><td colspan="4"></td><td class="num br" colspan="1">{{ $f($apBs) }}</td></tr>
    <tr><td class="bl bb" colspan="1"></td><td class="bb fw-7" colspan="1">Total Aportes</td><td class="bb" colspan="1"></td><td class="bb" colspan="4"></td><td class="num br bb fw-7" colspan="1" style="border-top:1px solid #000;"><strong>{{ $f($totAportes) }}</strong> </td></tr>

    {{-- COSTO FINAL --}}
    <tr><td class="bl" colspan="1" style="background:#FFE699;border-top:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;"></td><td class="" colspan="1" style="background:#FFE699;border-top:1px solid #000;border-bottom:1px solid #000;">Costo Final</td><td colspan="5" style="background:#FFE699;border-top:1px solid #000;border-bottom:1px solid #000;"></td><td class="num br" colspan="1" style="background:#FFE699;border-top:1px solid #000;border-bottom:1px solid #000;border-right:1px solid #000;"><strong>Bs {{ $f($costoFinal) }}</strong>  </td></tr>

</table>

{{-- FIRMAS --}}
<table style="width:100%;margin-top:25px;">
    <tr>
        <td style="width:50%;border:0;text-align:center;padding-top:40px;">
            <div style="border-top:1px solid #000;width:60%;margin:0 auto 5px;"></div>
            <br>
            Liquidador
        </td>
        <td style="width:50%;border:0;text-align:center;padding-top:40px;">
            <div style="border-top:1px solid #000;width:60%;margin:0 auto 5px;"></div>
            {{ $l->full_name }}<br>
            C.I:{{ $l->ci }} <br>
            Cliente
        </td>
    </tr>
</table>
</body>
</html>