<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 24px; }
        .header { margin-bottom: 24px; }
        .logo { font-size: 22px; font-weight: bold; color: #002745; }
        .logo span { color: #4a90d9; }
        .fecha { text-align: right; color: #666; font-size: 11px; margin-top: -20px; }
        .destinatario { margin-bottom: 20px; }
        .destinatario strong { display: block; font-size: 13px; color: #002745; text-transform: uppercase; }
        .intro { margin-bottom: 20px; color: #444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background-color: #002745; color: #fff; }
        thead th { padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        tbody tr { border-bottom: 1px solid #e5e7eb; }
        tbody td { padding: 8px 10px; }
        .text-right { text-align: right; }
        .totales { width: 260px; margin-left: auto; margin-top: 8px; }
        .totales td { padding: 4px 8px; font-size: 12px; }
        .totales .total-row { font-weight: bold; font-size: 13px; border-top: 1px solid #002745; }
        .notas { margin-top: 20px; padding: 10px; background: #f9fafb; border-left: 3px solid #002745; font-size: 11px; }
        .footer { margin-top: 40px; font-size: 11px; color: #888; border-top: 1px solid #e5e7eb; padding-top: 12px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">Genetic<span>Lab</span></div>
        <div class="fecha">Ciudad de México a {{ now()->isoFormat('D [de] MMMM [de] YYYY') }}</div>
    </div>

    <div class="destinatario">
        <strong>{{ $cotizacion->medico?->nombre_completo ?? '' }}</strong>
        {{ $cotizacion->medico?->especialidad ?? '' }}<br>
        {{ $cotizacion->hospital?->nombre ?? '' }}
    </div>

    <p class="intro">
        De acuerdo con su amable solicitud hacemos llegar nuestra propuesta para los estudios moleculares requeridos.
    </p>

    <table>
        <thead>
            <tr>
                <th>Estudio</th>
                <th>Espécimen</th>
                <th>Tiempo de respuesta</th>
                <th class="text-right">Importe unitario</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->estudios as $renglon)
            <tr>
                <td>
                    <strong>{{ $renglon->estudio?->nombre ?? '—' }}</strong>
                </td>
                <td>{{ $renglon->estudio?->especimen ?? '—' }}</td>
                <td>{{ $renglon->estudio?->tiempo_respuesta ?? '—' }}</td>
                <td class="text-right">${{ number_format($renglon->precio_unitario, 2) }} MXN<br><small>Más IVA</small></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totales">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">${{ number_format($cotizacion->subtotal, 2) }}</td>
        </tr>
        @if ($cotizacion->descuento > 0)
        <tr>
            <td>Descuento ({{ $cotizacion->descuento }}%)</td>
            <td class="text-right">-${{ number_format($cotizacion->subtotal * ($cotizacion->descuento / 100), 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>Total</td>
            <td class="text-right">${{ number_format($cotizacion->total, 2) }} MXN</td>
        </tr>
    </table>

    @if ($cotizacion->notas)
    <div class="notas">
        <strong>Condiciones y observaciones:</strong><br>
        {{ $cotizacion->notas }}<br>
        Al importe final se le agregará IVA.
    </div>
    @endif

    @if ($cotizacion->valida_hasta)
    <p style="font-size:11px; color:#666; margin-top:12px;">
        Cotización válida hasta el {{ $cotizacion->valida_hasta->format('d/m/Y') }}.
    </p>
    @endif

    <div class="footer">
        Agradecemos su interés en nuestra propuesta. Si tiene alguna pregunta o necesita más información,
        no dude en ponerse en contacto con nosotros.<br><br>
        Cordialmente<br>
        <strong>GeneticLab</strong>
    </div>

</body>
</html>