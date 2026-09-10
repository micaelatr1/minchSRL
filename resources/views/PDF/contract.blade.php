<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato {{ $contract->code }}</title>
    <style>
        @page { margin: 1cm 1cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1e2a3a;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .header-table .logo-cell {
            width: 18%;
            text-align: center;
            font-size: 9px;
        }
        .header-table .title-cell {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1e466e;
        }
        .header-table .meta-cell {
            width: 120px;
            text-align: center;
            font-size: 12px;
        }
        .header-table .meta-cell .code-label {
            font-weight: bold;
        }
        .header-table .meta-cell .revision {
            padding: 2px 0;
        }
        .header-table .meta-cell .public-badge {
            background: #2c3e50;
            color: white;
            padding: 2px 10px;
            font-weight: bold;
            font-size: 11px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .info-table td {
            border: 1px solid #000;
            padding: 7px 10px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: 600;
            width: 120px;
            background: #f0f4f9;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .data-table thead th {
            background-color: #81cc6a;
            font-weight: 700;
            text-align: left;
            font-size: 9pt;
            color: #1f3b4c;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #fafcfd;
        }
        .data-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        .data-table tbody td:nth-child(3) {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .data-table tbody td:first-child {
            text-align: center;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .summary-table td {
            border: 2px solid #000;
            padding: 10px 15px;
            text-align: center;
            font-weight: 600;
            font-size: 12pt;
        }
        .summary-table .label-cell {
            font-size: 8pt;
            font-weight: 400;
            color: #4a627a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: none;
            padding-bottom: 2px;
        }
        .summary-table .value-cell {
            border-top: none;
            padding-top: 2px;
        }
        .summary-table .total {
            color: #1e466e;
        }
        .summary-table .paid {
            color: #16a34a;
        }
        .summary-table .remaining {
            color: #dc2626;
        }
        .report-footer {
            margin-top: 25px;
            font-size: 8pt;
            text-align: right;
            border-top: 1px solid #cfdee9;
            padding-top: 10px;
            color: #4f6f8f;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            font-size: 9pt;
            font-weight: 700;
            border-radius: 4px;
        }
        .badge-success {
            background: #16a34a;
            color: white;
        }
        .badge-info {
            background: #2563eb;
            color: white;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="logo-cell" rowspan="3">
                <img src="{{ public_path('image/logo.png') }}" alt="" height="35">
                <br><strong>EMPRESA MINCH S.R.L</strong>
            </td>
            <td class="title-cell" rowspan="3">
                CONTRATO<br>
                {{ $contract->code }}
            </td>
            <td class="meta-cell">
                <span class="code-label">Código: P12.F08</span>
            </td>
        </tr>
        <tr>
            <td class="meta-cell">
                <span class="revision">Revisión: 0</span>
            </td>
        </tr>
        <tr>
            <td class="meta-cell">
                <span class="public-badge">PUBLICO</span>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="label">{{ $contract->type === 'supplier' ? 'Proveedor' : 'Cliente' }}</td>
            <td>{{ $contract->person->full_name }} &middot; CI: {{ $contract->person->ci }}</td>
        </tr>
        <tr>
            <td class="label">Estado</td>
            <td>
                <span class="badge {{ $contract->status === 'completed' ? 'badge-success' : 'badge-info' }}">
                    {{ $contract->status === 'completed' ? 'COMPLETADO' : 'EN PROGRESO' }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label">Descripción</td>
            <td>{{ $contract->description ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Vigencia</td>
            <td>{{ $contract->start_date->format('d/m/Y') }} @if ($contract->end_date) &mdash; {{ $contract->end_date->format('d/m/Y') }} @endif</td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td width="33.33%">
                <div class="label-cell">Total</div>
                <div class="value-cell total">Bs {{ number_format((float) $contract->total_amount, 2, ',', '.') }}</div>
            </td>
            <td width="33.33%">
                <div class="label-cell">Pagado</div>
                <div class="value-cell paid">Bs {{ number_format($contract->paid_amount, 2, ',', '.') }}</div>
            </td>
            <td width="33.34%">
                <div class="label-cell">Saldo Pendiente</div>
                <div class="value-cell remaining">Bs {{ number_format($contract->remaining_amount, 2, ',', '.') }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="border-top: none; padding-top: 0;">
                <div class="label-cell" style="text-align: center;">Progreso de Pago</div>
                <div style="text-align: center; font-weight: 700; font-size: 14pt; color: {{ $contract->progress >= 100 ? '#16a34a' : '#2563eb' }};">
                    {{ $contract->progress }}%
                </div>
            </td>
        </tr>
    </table>

    <h3 style="margin-top: 20px; font-size: 12pt; color: #1e466e;">Pagos Realizados</h3>
    @if ($contract->movements->isEmpty())
        <p style="text-align: center; color: #64748b; padding: 20px 0;">No se registraron pagos para este contrato.</p>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Tipo</th>
                    <th>Doc. Ref.</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contract->movements as $movement)
                    @php
                        $type = $movement->box ? 'Caja chica' : ($movement->transaction ? 'Banco' : 'Directo');
                        $ref = $movement->box ? $movement->box->number_label : ($movement->transaction ? $movement->transaction->number_label : '—');
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($movement->date)->format('d/m/Y') }}</td>
                        <td>Bs {{ number_format((float) $movement->amount, 2, ',', '.') }}</td>
                        <td>{{ $type }}</td>
                        <td>{{ $ref }}</td>
                        <td>{{ $movement->description ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="report-footer">
        <span>Informe generado automáticamente</span>
    </div>
</body>
</html>
