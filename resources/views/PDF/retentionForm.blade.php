<html>

<head>
</head>

<body>

    <table border="1" style="width: 100%">
        <tr>
            <td rowspan="3" width="25%" align="center" style="padding-top:10px;font-size:12px;vertical-align: top;">
                <img src="{{ public_path('image/logo.png') }}"
                            alt="" height="40">
                            <br>
                            <br><strong>EMPRESA MINCH S.R.L</strong> 
            </td>
            <td colspan="2" rowspan="3" width="55%" align="center" style="padding: 1px;">
                <strong style="font-size: 18px;">RECIBO OFICIAL DE RETENCIONES<br>
                    <span style="font-size: 14px;">{{ $retention->type == 'S' ? 'SERVICIOS' : 'BIENES' }}</span><br>
                    <span style="font-size: 14px;">(Expresado en Bolivianos)</span></strong>
            </td>
            <td width="20%" style="padding: 1px 1px;" align="center">
                <strong style="font-size: 14px;">Código: P09.F11</strong>
            </td>
        </tr>
        <tr>
            <td style="padding: 1px 1px; border-collapse: collapse" align="center">
                <strong style="font-size: 14px;">Revisión: {{ $retention->status }}</strong><br>
            </td>
        </tr>
        <tr>
            <td align="center"
                style="padding: 1px 1px;background:#2c3e50;color:white; border-collapse: collapse">
                <strong>EXTERNA</strong>
            </td>
        </tr>
    </table>

    {{-- DATOS DEL BENEFICIARIO --}}
    <table border="1" style="width: 100%; margin-top: -1px;">
        <tr>
            <td style="padding: 8px 10px;border:0px">
                <table border="0" style="width: 100%;font-size: 14px;">
                    <tr>
                        <style>
                            p{
                               
                            }
                        </style>
                        <td width="78%" align="right" style="padding:20px 80px 10px 0px"><strong
                                style="font-size: 18px;  font-weight: bold">Bs.
                                {{ number_format((float) $retention->calculate_total, 2, ',', '.') }}</strong> </td>
                        <td align="center" style="font-size:16px; position: relative; vertical-align: top">
                            <div style="position: absolute;right:90px;padding-top:5px">
                                <strong>Nº {{ $retention->date_code }}</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 10px;">
                <table border="0" style="width: 100%;font-size: 14px;">

                    <tr>
                        <td width="20%"><strong>Cancelado a:</strong> </td>
                        <td>{{ $retention->supplier->full_name }} </td>
                    </tr>
                    <tr>
                        <td> <strong>La suma de:</strong></td>
                        <td>{{ $retention->calculate_label }}..............Bolivianos.</td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 0px;vertical-align: top;"> <strong>Por concepto de:</strong></td>
                        <td style="padding: 20px 0px;">{{ $retention->description }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- RETENCIONES IMPOSITIVAS --}}
    <table border="1" style="width: 100%; margin-top: -1px;">
        <tr>
            <td colspan="3" style="padding: 8px 12px; border: 0px; font-size:14px">
                <strong> <u>RETENCIONES IMPOSITIVAS:</u> </strong>
            </td>
        </tr>
        @foreach ($retention->discounts as $discount)
            <tr>
                <td style="padding: 8px 8px 8px 100px; border: 0px;">
                    Impuesto Form. {{ $discount->taxe->number }}
                </td>
                <td align="right" style="padding: 8px 50px; border: 0px;">
                    {{ $discount->taxe->applied_discount }}%
                </td>
                <td align="right" style="padding: 8px 100px 8px 8px; border: 0px;">
                    {{ number_format((float) $discount->amount, 2, ',', '.') }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td colspan="2" style="padding: 8px 8px 8px 100px; border: 0px;">
                <strong>Total Impuestos:</strong>
            </td>
            <td align="right" style="padding: 8px 100px 8px 8px; border: 0px;">
                <hr>
                {{ number_format((float) $retention->discounts->sum('amount'), 2, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 8px 8px 8px 100px; border: 0px;">
                <strong>LIQUIDO A PAGAR:</strong>
            </td>
            <td align="right" style="padding: 8px 100px 8px 8px; border: 0px;">
                <hr>
                {{ number_format((float) $retention->amount, 2, ',', '.') }}
            </td>
        </tr>
    </table>

    {{-- CONFORMIDAD --}}


    {{-- FIRMAS --}}
    <table border="1" style="width: 100%; margin-top: -1px;">
        <tr>
            <td colspan="2" style="border: 0px;padding: 8px 10px">
                <table border="0" style="width: 100%;font-size: 14px;">
                    <tr>
                        <td width="20%" style="border: 0px;padding:10px 0px;vertical-align: top;"><strong>CONFORMIDAD:</strong> </td>
                        <td style="border: 0px; padding:10px 0px">Declaro mi conformidad con la retencion impositiva
                            como el haber recibido de forma integra el saldo económico pactado entre ambas partes </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="font-size: 12px">
            <td width="50%" align="center" style="padding: 60px 15px 10px;border:0px">
                <hr width="50%">
                <span>Nombre:{{ $retention->supplier->full_name }}</span><br>
                <span>C.I.: {{ $retention->supplier->ci }}</span><br>
                <strong>Entregué Conforme</strong>
            </td>
            <td width="50%" align="center" style="padding: 60px 15px 10px; border:0px">
                <hr width="50%">
                <span>Nombre: {{$retention->user->name.' '.$retention->user->last_name}}</span><br>
                <span>C.I.: {{$retention->user->ci}}</span><br>
                <strong>Recibí Conforme</strong>
            </td>
        </tr>
    </table>
    <table border="1" style="width: 100%; margin-top: -1px;">
        <tr>
            <td width="50%" align="center" style="padding: 30px 14px;border:0px">
                <strong> Lugar y Fecha: </strong>Potosi, {{ $retention->date_label }}
            </td>
        </tr>
    </table>
</body>

</html>
