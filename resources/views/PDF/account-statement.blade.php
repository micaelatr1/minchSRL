<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe de Cuenta</title>
    <style>
        @page {
            margin: 1.3cm 1.3cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1e2a3a;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* Contenedor principal del informe */
        .report-container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Cabecera del informe (título y detalles) */
        .report-header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 12px;
        }

        .report-header h1 {
            font-size: 18pt;
            font-weight: 600;
            margin: 0 0 6px 0;
            color: #1e466e;
            letter-spacing: 1px;
        }

        .report-header .subtitle {
            font-size: 9pt;
            color: #4a627a;
            margin: 0;
        }

        .report-header .date-emission {
            font-size: 8.5pt;
            color: #5d6f83;
            margin-top: 6px;
        }

        /* Tabla principal: estilo corporativo, limpio y legible */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        /* Bordes sutiles pero formales */
        .data-table th,
        .data-table td {
            border: 1px solid black;
            padding: 8px 8px;
            vertical-align: top;
        }

        .data-table thead th {
            background-color: #81cc6a;
            font-weight: 600;
            text-align: left;
            font-size: 9.5pt;
            color: #1f3b4c;
            letter-spacing: 0.3px;
            margin: 0px;
            padding: 5px;
            font-weight: bold
        }

        /* Estilo para las filas de encabezado (información de cuenta y período) */

        .data-table thead tr:nth-child(2) th {
            background-color: #f0f4f9;
            font-weight: 600;
            text-align: left;
            font-size: 9.5pt;
            border-bottom: 1px solid #cbdae6;
            color: #1f3b4c;
            letter-spacing: 0.3px;
        }

        .data-table tfoot tr td {
/*             background-color: #c7daf2;
 */            font-weight: 600;
            text-align: left;
            font-size: 9.5pt;
/*             border-bottom: 4px solid #cbdae6;
 */            color: #1f3b4c;

 border:  2px solid black;
            letter-spacing: 0.3px;
        }

        /* Fila de títulos de columnas (Nº, Fecha, Descripción...) */
        .data-table thead tr:last-child th {
            background-color: #e2e9f0;
            font-weight: 700;
            border-bottom: 2px solid #9aaebf;
            padding: 9px 6px;
            color: #1f3a4b;
            text-transform: uppercase;
            font-size: 8.8pt;
            letter-spacing: 0.4px;
        }

        /* Alineación de encabezados numéricos (DEBE, HABER, SALDO) */
        .data-table thead tr:last-child th:nth-child(5),
        .data-table thead tr:last-child th:nth-child(6),
        .data-table thead tr:last-child th:nth-child(7) {
            text-align: right;
            padding-right: 12px;
        }

        /* Celdas del cuerpo: alternancia de filas para mejor lectura */
        .data-table tbody tr:nth-child(even) {
            background-color: #fafcfd;
        }

        .data-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }


        /* Alineación de columnas numéricas (DEBE, HABER, SALDO) */
        .data-table tbody td:nth-child(5),
        .data-table tbody td:nth-child(6),
        .data-table tbody td:nth-child(7) {
            text-align: right;
            font-family: 'Courier New', 'Courier', monospace;
            font-weight: 500;
            padding-right: 12px;
        }

        /* Columna Nº centrada, ancho reducido visualmente */
        .data-table tbody td:first-child {
            font-weight: 500;
            background-color: #ffffff;
        }

        /* Columna Fecha: formato compacto */
        .data-table td:nth-child(2) {
            white-space: nowrap;
            text-align: left;
        }

        /* Descripción: permite saltos de línea y legibilidad */
        .data-table td:nth-child(3) {
            word-break: break-word;
            text-align: left;
        }

        /* Documento referencia: texto normal */
        .data-table td:nth-child(4) {
            text-align: left;
        }

        /* Pie de página opcional (si se agrega resumen, se estiliza) */
        .report-footer {
            margin-top: 28px;
            font-size: 8pt;
            text-align: right;
            border-top: 1px solid #cfdee9;
            padding-top: 12px;
            color: #4f6f8f;
        }

        /* Ajuste para que los encabezados de información (cuenta, moneda, periodo)
           tengan un peso visual equilibrado */
        .data-table thead tr:first-child th,
        .data-table thead tr:nth-child(2) th {
            font-weight: 550;
        }

        /* Énfasis en los valores de cuenta/moneda/periodo dentro de esas celdas */
        .data-table thead tr:first-child th strong,
        .data-table thead tr:nth-child(2) th strong {
            font-weight: 700;
            color: #0056a7;
        }

        /* Alineación específica para primera fila de cabecera (cuenta y moneda) */
        .data-table thead tr:first-child th:first-child {
            text-align: left;
        }

        .data-table thead tr:first-child th:last-child {
            text-align: left;
        }

        /* Para la fila de periodo, alineación izquierda */
        .data-table thead tr:nth-child(2) th {
            text-align: left;
        }

        /* Espaciado y consistencia */
        .data-table th,
        .data-table td {
            transition: none;
        }

        /* Asegurar que el borde inferior de la cabecera se vea profesional */
        .data-table thead tr:last-child th {
            border-bottom: 2px solid #8ca3b9;
        }
    </style>
</head>

<body>
    <div class="report-container">
        <table border="1" style="width: 100%">
            <tr>
                <td colspan="2" rowspan="3"  align="center" style="font-size: 9px"><img
                        src="{{ public_path('image/logo.png') }}" alt="" height="35">
                    <br><strong>EMPRESA MINCH S.R.L</strong>
                </td>
                <td colspan="2" rowspan="3" width="450px" align="center">
                    <strong style="font-size: 16px">ESTADO DE CUENTA<br>
                        {{ $customer->full_name }}
                    </strong>
                    <br>
                    <span>{{ $holderLabel ?? 'Titular' }} · (EXPRESADO EN BOLIVIANOS)</span>
                </td>
                <td width="120px" align="center">
                    <strong style="font-size: 12px;">Código: P12.F06</strong>

                </td>
            </tr>
            <tr>
                <td width="120px" style="padding: 1px 1px; border-collapse: collapse" align="center">
                    <strong style="font-size: 12px;">Revisión: 0</strong><br>
                </td>
            </tr>
            <tr>
                <td width="120px" align="center" style="padding: 1px 1px;background:#2c3e50;color:white; border-collapse: collapse">
                    <strong>PUBLICO</strong>
                </td>
            </tr>
        </table>
        <table class="data-table">
            <thead>
                <tr >
                    <th align="center" scope="col">Nº</th>
                    <th align="center" scope="col">FECHA</th>
                    <th align="center" scope="col">DESCRIPCIÓN</th>
                    <th align="center" scope="col">DCTO. REF.</th>
                    <th align="center" scope="col">Nº VOLQ.</th>
                    <th align="center" scope="col">DEBE</th>
                    <th align="center" scope="col">HABER</th>
                    <th align="center" scope="col">SALDO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accountStatement as $account)
                    <tr>
                        <td>
                            {{ $loop->iteration }}
                        </td>
                        <td>
                            {{ $account->date }}
                        </td>
                        <td>
                            {{ $account->description }}
                        </td>
                        <td>
                            {{ $account->box?->number_label ?? ($account->transaction?->number_label ?? '') }}
                        </td>
                        <td>
                            {{ $account->number_vol }}
                        </td>
                        <td>
                            {{ in_array($account->type, ['D', 'B']) ? number_format((float)$account->amount, 2, ',', '.') : '' }}
                        </td>
                        <td>
                            {{ $account->type == 'C' ? number_format((float)$account->amount, 2, ',', '.') : '' }}
                        </td>
                        <td>
                            {{ number_format((float)$account->balance, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td align="right" colspan="5">total:</td>
                    <td align="right">{{ number_format((float) $accountStatement->whereIn('type', ['D', 'B'])->sum('amount'), 2, ',', '.') }} </td>
                    <td align="right">{{ number_format((float) $accountStatement->whereIn('type', ['C'])->sum('amount'), 2, ',', '.') }}</td>
                    <td align="right">
                        {{ number_format((float) ($accountStatement->whereIn('type', ['D', 'B'])->sum('amount') - $accountStatement->whereIn('type', ['C'])->sum('amount')), 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Pie de página formal (información complementaria) -->
        <div class="report-footer">
            <span>Informe generado automáticamente{{--  &nbsp;|&nbsp; Datos sujetos a verificación --}}</span>
        </div>
    </div>
</body>

</html>
