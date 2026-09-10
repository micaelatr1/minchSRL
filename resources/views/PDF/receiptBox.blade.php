<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: white;
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 14px;
            padding: 0px 0px;
        }

        .content {
            padding: 30px
        }

        /* Contenedor principal sin sombras ni efectos */
        .recibo-wrapper {
            position: relative;
            margin: 0 auto;
            /* borde fino, sin sombra */
            border-radius: 12px;
            overflow: hidden;
            border: #0f172a solid 1px
        }


        .lateral-izquierdo,
        .lateral-derecho {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-size: 11px;
            letter-spacing: 6px;
            color: rgba(0, 100, 200, 0.07);
            font-weight: bold;
            text-transform: uppercase;
        }

        .lateral-izquierdo {
            left: 8px;
        }

        .lateral-derecho {
            right: 8px;
        }

        /* Contenido del recibo */
        .recibo-contenido {
            position: relative;
            z-index: 2;
            background: white;
        }


        .recibo-header p {
            font-size: 12px;
            color: #475569;
        }

        /* Cuerpo */
        .recibo-body {
            padding: 1px 30px;
        }

        .info-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .info-tabla td {
            padding: 8px 5px;
            vertical-align: top;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-tabla .etiqueta {
            font-weight: 600;
            width: 15%;
            color: #1e293b;
        }

        .info-tabla .valor {
            width: 65%;
            color: #0f172a;
        }

        .detalle-tabla {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }

        .detalle-tabla th {
            text-align: left;
            padding: 10px 5px;
            background: #f1f5f9;
            font-weight: 600;
            border-bottom: 2px solid #cbd5e1;
        }

        .detalle-tabla td {
            padding: 10px 5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .totales {
            text-align: right;
            margin-top: 20px;
        }

        .totales table {
            width: 280px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totales td {
            padding: 8px 5px;
        }

        .totales .total-final td {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #cbd5e1;
            padding-top: 12px;
        }

        .firmas {
            margin-top: 45px;
            display: table;
            width: 100%;
            border-top: 1px dashed #94a3b8;
            padding-top: 25px;
        }

        .firma {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .linea-firma {
            margin-top: 35px;
            border-top: 1px solid #cbd5e1;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            padding-top: 8px;
            font-size: 12px;
            color: #475569;
        }

        .recibo-footer {
            text-align: right;
            font-size: 10px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding: 15px;
            background: #fafcff;
        }
    </style>
</head>

<body>
    <div class="content">

        <div class="recibo-wrapper">

            {{--             <div class="lateral-izquierdo"> empresa minch empresa minch empresa minch</div>
 --}} <table border="1" style="width: 100%" style="border-collapse:collapse">
                <tr style="border-top: 0px">
                    <td rowspan="3" style="border-top: 0px;border-left:0px" align="center" width="20%"><img
                            src="{{ public_path('image/logo.png') }}" alt="" height="36"> <br>EMPRESA MINCH
                        S.R.L</td>
                    <td rowspan="3" align="center" style="color: red"><strong
                            style="font-size:16px;color:#4a627a ">RECIBO DE
                            {{ $transaction->type == 'C' ? 'EGRESO' : 'INGRESO' }}</strong> <br> Nº
                        {{ $transaction->box->formatted_last_number }} </td>
                    <td width="20%" style="border-top: 0px;border-right:0px;padding: 0 0 0 10px">
                        Codigo:P12.F00{{ $transaction->type == 'C' ? 2 : 1 }}
                    </td>
                </tr>
                <tr>
                    <td style="border-right:0px;padding: 0 0 0 10px">Revisión:0</td>
                </tr>
                <tr>
                    <td align="center" style="background:#4a627a;color:white" style="border-right:0px">PUBLICO</td>

                </tr>
            </table>
            <div class="recibo-contenido" style="margin:4px">

                <div class="recibo-body" style="margin:4px">

                    <div width="100%" style=" padding:15px 70px 15px 60%; text-align: center;">

                        <div
                            style="font-size: 14px;background:#bfd7ed;border-radius: 12px;margin:auto 0; display: inline-block;padding:4px;border:#475569 solid 1px">
                           Bs. {{ $transaction->amount }}
                        </div>
                    </div>
                    <table class="info-tabla">
                        <tr>
                            <td class="etiqueta">Se canceló a:</td>
                            <td class="valor">{{ $transaction->supplier->full_name }} </td>
                        </tr>
                        @if ($transaction->number_check)
                            <tr>
                                <td class="etiqueta">Cheque Nro:</td>
                                <td class="valor">{{ $transaction->number_check }} </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="etiqueta">La suma es: (Bs.)</td>
                            <td class="valor">{{ $transaction->calculate_label }}</td>
                        </tr>
                        <tr>
                            <td class="etiqueta" style="border-bottom:0px" >Por concepto:</td>
                            <td class="valor" style="font-size:12px; padding: 2px 0px 0px 0px; vertical-align: top; border-bottom:0px">
                                    {{ str_pad(Str::limit($transaction->description, 500, '...'), 500, ' .') }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" align="center" style="padding: 35px 14px;border:0px">
                                Potosi, {{ $transaction->date_label }}
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%; margin-top: -1px">
                        <tr style="font-size: 12px">
                            <td width="50%" align="center" style="padding: 2px 15px;border:0px">
                                <hr width="50%">
                                <span>{{ auth()->user()->name . ' ' . auth()->user()->last_name }} </span><br>
                                <span>C.I.: {{ auth()->user()->ci }}</span><br>
                                <span>Entregué Conforme</span>
                            </td>
                            <td width="50%" align="center" style="padding: 2px 15px; border:0px">
                                <hr width="50%">
                                <span>{{ $transaction->supplier->full_name }} </span><br>
                                <span>C.I.: {{ $transaction->supplier->ci }}</span><br>
                                <span>Recibí Conforme</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
        <div style="padding: 15px 0 15px 0">
            <hr>
        </div>
        <div class="recibo-wrapper" style="color: black">

            {{--             <div class="lateral-izquierdo" style="color: black"> empresa minch empresa minch empresa minch</div>
 --}} <table border="1" style="width: 100%" style="border-collapse:collapse">
                <tr>
                    <td rowspan="3" align="center" width="20%" style="border-top:0px;border-left:0px"><img
                            src="{{ public_path('image/logo.png') }}" alt="" height="36"> <br>EMPRESA MINCH
                        S.R.L</td>
                    <td rowspan="3" align="center" style="color: black"><strong
                            style="font-size:16px;color:black ">RECIBO DE
                            {{ $transaction->type == 'C' ? 'EGRESO' : 'INGRESO' }}</strong> <br> Nº
                        {{ $transaction->box->formatted_last_number }} </td>
                    <td width="20%" style="border-top:0px;border-right:0px;padding: 0 0 0 10px"> Codigo:P12.F001</td>
                </tr>
                <tr>
                    <td style="border-left:0px;padding: 0 0 0 10px">Revisión:0</td>
                </tr>
                <tr>
                    <td align="center" style="border-left:0px">PUBLICO</td>

                </tr>
            </table>
            <div class="recibo-contenido" style="margin:4px">

                <div class="recibo-body" style="margin:4px">

                    <div width="100%" style=" padding:15px 70px 15px 60%; text-align: center;">

                        <div
                            style="font-size: 14px;border-radius: 12px;margin:auto 0; display: inline-block;padding:4px;border:#475569 solid 1px">
                           Bs. {{ $transaction->amount }}</div>
                    </div>
                    <table class="info-tabla">
                        <tr>
                            <td class="etiqueta">Se canceló a:</td>
                            <td class="valor">{{ $transaction->supplier->full_name }} </td>
                        </tr>
                        @if ($transaction->number_check)
                            <tr>
                                <td class="etiqueta">Cheque Nro:</td>
                                <td class="valor">{{ $transaction->number_check }} </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="etiqueta">La suma es: (Bs.)</td>
                            <td class="valor">{{ $transaction->calculate_label }}</td>
                        </tr>
                        <tr>
                             <td class="etiqueta" style="border-bottom:0px" >Por concepto:</td>
                            <td class="valor" style="font-size:12px; padding: 2px 0px 0px 0px; vertical-align: top; border-bottom:0px">
                                   {{ str_pad(Str::limit($transaction->description, 500, '...'), 500, ' .') }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" align="center" style="padding: 35px 14px;border:0px">
                                Potosi, {{ $transaction->date_label }}
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%; margin-top: -1px">
                        <tr style="font-size: 12px">
                            <td width="50%" align="center" style="padding: 2px 15px;border:0px">
                                <hr width="50%">
                                <span>{{ auth()->user()->name . ' ' . auth()->user()->last_name }} </span><br>
                                <span>C.I.: {{ auth()->user()->ci }}</span><br>
                                <span>Entregué Conforme</span>
                            </td>
                            <td width="50%" align="center" style="padding: 2px 15px; border:0px">
                                <hr width="50%">
                                <span>{{ $transaction->supplier->full_name }} </span><br>
                                <span>C.I.: {{ $transaction->supplier->ci }} </span><br>
                                <span>Recibí Conforme</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
