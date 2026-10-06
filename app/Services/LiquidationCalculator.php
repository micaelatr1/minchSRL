<?php

namespace App\Services;

use App\Models\Liquidation;

/**
 * Réplica en PHP de los getters de Alpine en
 * resources/views/livewire/liquidations/liquidation-form.blade.php (líneas 174-226).
 *
 * Es la fuente de verdad de los cálculos: cualquier cambio ahí debe reflejarse aquí.
 */
class LiquidationCalculator
{
    public static function calculate(Liquidation $l): array
    {
        $metal = $l->metal;
        $isPb = $metal === 'pb';
        $decimals = $isPb ? 5 : 3;

        $tmh = (float) $l->tmh;
        $h2o = (float) $l->h2o;
        $merma = (float) $l->merma;
        $dm = (float) $l->dm;

        // get tms() / get tmns() — l.174-175
        $tms = round($tmh - ($tmh * $h2o / 100), $decimals);
        $tmnsRaw = -($tms * $merma / 100) + $tms;
        $tmns = round($tmnsRaw, $decimals);

        // get ag() / get platacalculate() — l.176-177
        $ag = $dm * 100;
        $platacalculate = ($dm * 100) / 31.1035;

        // get ley() / get precioMetal() / get nim() — l.178-180
        $ley = $isPb ? (float) $l->lead_grade : (float) $l->zinc_grade;
        $precioMetal = $isPb ? (float) $l->market_pb : (float) $l->market_zn;
        $nim = $isPb ? (float) $l->quincenal_pb : (float) $l->quincenal_zn;

        // get deduccionMetal() / deduccionAg() / payAg() — l.181-183
        $deduccionMetal = $isPb ? 3 : 8;
        $deduccionAg = $isPb ? 1.6 : 3;
        $payAg = $isPb ? 0.95 : 0.7;
        $payPb = $isPb ? 0.95 : 1;

        // get leyPorcentual() — l.184
        $leyPorcentual = $ley - $deduccionMetal;

        // get valorMetal() — l.185-188 (redondea a 2 en Zn y Pb)
        $valorMetalBase = $leyPorcentual * $precioMetal / 100;
        $valorMetal = round($valorMetalBase, 2) * $payPb;

        // get plataporcentual() / platavalue() / totalplata() — l.189-194
        $plataporcentual = $platacalculate - $deduccionAg;
        $platavalue = (float) $l->market_ag * $payAg;
        $totalPlataRaw = $plataporcentual * $platavalue;
        $totalplata = round($totalPlataRaw, 2);

        // get baseEscala() / baseTotal() — l.195-196
        $base = (float) $l->base;
        $baseEscala = $precioMetal > $base ? $precioMetal - $base : 0;
        $baseTotal = $precioMetal > $base ? $baseEscala * (float) $l->base_percentage : 0;

        // get refinacionTotal() — l.197-200
        $refinacionRaw = $platacalculate * (float) $l->refinacion;
        $refinacionTotal = $isPb ? round($refinacionRaw, 2) : $refinacionRaw;

        // get AsTotal() ... SnTotal() — l.201-205
        $asTotal = self::penalty((float) $l->as_pct, (float) $l->p_as, (float) $l->p_as_usd, (float) $l->p_as_pct);
        $sbTotal = self::penalty((float) $l->sb_pct, (float) $l->p_sb, (float) $l->p_sb_usd, (float) $l->p_sb_pct);
        $feTotal = self::penalty((float) $l->fe_pct, (float) $l->p_fe, (float) $l->p_fe_usd, (float) $l->p_fe_pct);
        $sio2Total = self::penalty((float) $l->sio2_pct, (float) $l->p_sio2, (float) $l->p_sio2_usd, (float) $l->p_sio2_pct);
        $snTotal = self::penalty((float) $l->sn_pct, (float) $l->p_sn, (float) $l->p_sn_usd, (float) $l->p_sn_pct);

        // get totalCT() — l.206
        $totalCT = (float) $l->maquila + $baseTotal + $asTotal + $sbTotal + $feTotal + $sio2Total + $snTotal
            + ($isPb ? $refinacionTotal : 0);

        // get valorNeto() / totalNetoUSD() — l.207-208
        $valorNeto = round($valorMetal + $totalplata - $totalCT, 2);
        $totalNetoUSD = ($valorMetal + $totalplata - $totalCT) * $tmns;

        // get gastosOp() — l.209
        $quincenalAg = (float) $l->quincenal_ag;
        $gastosOp = (
            ($tmns * 1000 * $ley / 100 * 2.2046223 * $nim * 5 / 100)
            + ($dm * $tmns / 10 * 32.15073 * $quincenalAg * 6 / 100)
        ) - (
            ($tmns * 1000 * $ley / 100 * 2.2046223 * $nim * 3 / 100)
            + ($dm * $tmns / 10 * 32.15073 * $quincenalAg * 3.6 / 100)
        );

        // get totalFlete() / totalRollback() / totalRemesa() / totalGastos() — l.210-213
        $totalFlete = (float) $l->flete * $tmh;
        $totalRollback = (float) $l->rollback * $tmh;
        $totalRemesa = ($totalNetoUSD * (float) $l->remesa_pct / 100) + (float) $l->remesa_fijo;
        $totalGastos = $totalFlete + $totalRollback + $gastosOp + $totalRemesa;

        // get totalUSD() / totalUSDperTMNS() / totalBs() — l.214-216
        $totalUSD = $totalNetoUSD - $totalGastos;
        $totalUSDperTMNS = $tmns ? $totalUSD / $tmns : 0;
        $totalBs = $totalUSD * (float) $l->tc;

        // get regaliaMetalBs() / regaliaAgBs() / totalRM() — l.217-219
        $regaliaMetal = $isPb ? (float) $l->regalia_pb : (float) $l->regalia_zn;
        $regaliaMetalBs = ($tmns * 1000 * $ley / 100 * 2.2046223 * $nim * $regaliaMetal / 100) * (float) $l->factor_regalia;
        $regaliaAgBs = ($dm * $tmns / 10 * 32.15073 * $quincenalAg * (float) $l->regalia_ag / 100) * (float) $l->factor_regalia;
        $totalRM = $regaliaMetalBs + $regaliaAgBs;

        // get cnsBs() ... aporteCoopBs() / totalAportes() — l.220-225
        $cnsBs = $totalBs * (float) $l->cns_pct / 100;
        $comibolBs = $totalBs * (float) $l->comibol_pct / 100;
        $fedecominBs = $totalBs * (float) $l->fedecomin_pct / 100;
        $fencominBs = $totalBs * (float) $l->fencomin_pct / 100;
        $aporteCoopBs = $totalBs * (float) $l->aporte_coop_pct / 100;
        $totalAportes = $cnsBs + $comibolBs + $fedecominBs + $fencominBs + $aporteCoopBs;

        // get costoFinal() — l.226
        $totalDeducc = $totalRM + $totalAportes;
        $liquidoPagable = $totalBs - $totalDeducc;

        return [
            'metal' => $metal,
            'tmh' => $tmh,
            'h2o' => $h2o,
            'merma' => $merma,
            'dm' => $dm,
            'tms' => $tms,
            'tmns' => $tmns,
            'ag' => $ag,
            'platacalculate' => $platacalculate,
            'ley' => $ley,
            'precioMetal' => $precioMetal,
            'nim' => $nim,
            'leyPorcentual' => $leyPorcentual,
            'valorMetal' => $valorMetal,
            'plataporcentual' => $plataporcentual,
            'platavalue' => $platavalue,
            'totalplata' => $totalplata,
            'baseEscala' => $baseEscala,
            'baseTotal' => $baseTotal,
            'refinacionTotal' => $refinacionTotal,
            'asTotal' => $asTotal,
            'sbTotal' => $sbTotal,
            'feTotal' => $feTotal,
            'sio2Total' => $sio2Total,
            'snTotal' => $snTotal,
            'totalCT' => $totalCT,
            'valorNeto' => $valorNeto,
            'totalNetoUSD' => $totalNetoUSD,
            'gastosOp' => $gastosOp,
            'totalFlete' => $totalFlete,
            'totalRollback' => $totalRollback,
            'totalRemesa' => $totalRemesa,
            'totalGastos' => $totalGastos,
            'totalUSD' => $totalUSD,
            'totalUSDperTMNS' => $totalUSDperTMNS,
            'totalBs' => $totalBs,
            'regaliaMetal' => $regaliaMetal,
            'regaliaMetalBs' => $regaliaMetalBs,
            'regaliaAgBs' => $regaliaAgBs,
            'totalRM' => $totalRM,
            'cnsBs' => $cnsBs,
            'comibolBs' => $comibolBs,
            'fedecominBs' => $fedecominBs,
            'fencominBs' => $fencominBs,
            'aporteCoopBs' => $aporteCoopBs,
            'totalAportes' => $totalAportes,
            'totalDeducc' => $totalDeducc,
            'liquidoPagable' => $liquidoPagable,
        ];
    }

    private static function penalty(float $value, float $threshold, float $usd, float $pct): float
    {
        return $value > $threshold ? ($value - $threshold) * ($usd / max($pct, 0.001)) : 0;
    }
}
