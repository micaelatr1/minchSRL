<?php

namespace App\Helpers;

class NumberHelper
{
    private static array $unidades = [
        '', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO',
        'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ',
        'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE',
        'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE', 'VEINTE',
        'VEINTIÚN', 'VEINTIDÓS', 'VEINTITRÉS', 'VEINTICUATRO', 'VEINTICINCO',
        'VEINTISÉIS', 'VEINTISIETE', 'VEINTIOCHO', 'VEINTINUEVE',
    ];

    private static array $decenas = [
        '', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA',
        'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA',
    ];

    private static array $centenas = [
        '', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS',
        'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS',
    ];

    public static function toLiteral(float $numero, string $moneda = 'BOLIVIANOS'): string
    {
        $numero = round($numero, 2);
        $entero = (int) $numero;
        $decimal = round(($numero - $entero) * 100);

        $literal = self::convertirEntero($entero);
        $literal = trim($literal);

        $literal .= ' '.str_pad((int) $decimal, 2, '0', STR_PAD_LEFT).'/100';

        return ucfirst(strtolower($literal));
    }

    private static function convertirEntero(int $numero): string
    {
        if ($numero === 0) {
            return 'CERO';
        }
        if ($numero === 100) {
            return 'CIEN';
        }
        if ($numero < 30) {
            return self::$unidades[$numero];
        }

        if ($numero < 100) {
            $decena = (int) ($numero / 10);
            $unidad = $numero % 10;

            return $unidad === 0
                ? self::$decenas[$decena]
                : self::$decenas[$decena].' Y '.self::$unidades[$unidad];
        }

        if ($numero < 1000) {
            $centena = (int) ($numero / 100);
            $resto = $numero % 100;

            return $resto === 0
                ? self::$centenas[$centena]
                : self::$centenas[$centena].' '.self::convertirEntero($resto);
        }

        if ($numero < 2000) {
            $resto = $numero % 1000;

            return $resto === 0
                ? 'MIL'
                : 'MIL '.self::convertirEntero($resto);
        }

        if ($numero < 1000000) {
            $miles = (int) ($numero / 1000);
            $resto = $numero % 1000;

            return $resto === 0
                ? self::convertirEntero($miles).' MIL'
                : self::convertirEntero($miles).' MIL '.self::convertirEntero($resto);
        }

        if ($numero < 2000000) {
            $resto = $numero % 1000000;

            return $resto === 0
                ? 'UN MILLÓN'
                : 'UN MILLÓN '.self::convertirEntero($resto);
        }

        $millones = (int) ($numero / 1000000);
        $resto = $numero % 1000000;

        return $resto === 0
            ? self::convertirEntero($millones).' MILLONES'
            : self::convertirEntero($millones).' MILLONES '.self::convertirEntero($resto);
    }
}
