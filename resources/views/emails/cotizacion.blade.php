<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 32px; }
        h1 { color: #002745; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #002745; color: #fff; padding: 8px 12px; text-align: left; font-size: 12px; text-transform: uppercase; }
        td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
        .total { font-weight: bold; font-size: 15px; }
        .footer { margin-top: 32px; font-size: 12px; color: #888; border-top: 1px solid #e5e7eb; padding-top: 16px; }
    </style>
</head>
<body>
    <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('logo_azul.png'))) }}"
        style="height: 45px; width: auto; margin-bottom: 16px;" /><br>
    <h1>Cotización {{ $cotizacion->folio }}</h1>

    <p>Estimado {{ $cotizacion->medico?->nombre_completo ?? 'Dr./Dra.' }},</p>
    <p>Adjunto encontrará la cotización con los estudios moleculares solicitados.</p>

    <table>
        <thead>
            <tr>
                <th>Estudio</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->estudios as $renglon)
            <tr>
                <td>{{ $renglon->estudio?->nombre ?? '—' }}</td>
                <td>{{ $renglon->cantidad }}</td>
                <td>${{ number_format($renglon->precio_unitario, 2) }} MXN</td>
                <td>${{ number_format($renglon->subtotal, 2) }} MXN</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $subtotalConDescuento = $cotizacion->subtotal - ($cotizacion->subtotal * ($cotizacion->descuento / 100));
        $iva = $subtotalConDescuento * 0.16;
        $totalConIva = $subtotalConDescuento + $iva;
    @endphp

    @if ($cotizacion->descuento > 0)
    <p>Descuento aplicado: {{ $cotizacion->descuento }}%</p>
    @endif

    <p>Subtotal: ${{ number_format($cotizacion->subtotal, 2) }} MXN</p>
    <p>IVA (16%): ${{ number_format($iva, 2) }} MXN</p>
    <p class="total">Total: ${{ number_format($totalConIva, 2) }} MXN</p>

    @if ($cotizacion->valida_hasta)
    <p>Cotización válida hasta el {{ $cotizacion->valida_hasta->format('d/m/Y') }}.</p>
    @endif

    @if(!empty($incluirDatosBancarios))
    <table style="width:100%; border-collapse: collapse; margin-top: 20px; border: 1px solid #e5e7eb;">
        <tr>
            <td style="background:#002745; color:#fff; padding:10px 12px; font-weight:bold; font-size:13px; text-transform:uppercase;">
                Datos bancarios
            </td>
        </tr>
    </table>
    <table style="width:100%; border-collapse: collapse; margin-bottom: 16px;">
        <tr><td style="padding:6px 12px; color:#002745; font-weight:bold; width:40%;">Titular</td><td style="padding:6px 12px; font-weight:bold;">GENETIC LAB CONSULTANTS SA DE CV</td></tr>
        <tr><td style="padding:6px 12px; color:#002745; font-weight:bold;">Banco</td><td style="padding:6px 12px; font-weight:bold;">BanBajío</td></tr>
        <tr><td style="padding:6px 12px; color:#002745; font-weight:bold;">Cuenta CLABE</td><td style="padding:6px 12px; font-weight:bold;">030180900048083765</td></tr>
        <tr><td style="padding:6px 12px; color:#002745; font-weight:bold;">Núm. de cuenta</td><td style="padding:6px 12px; font-weight:bold;">0508832140201</td></tr>
    </table>
    <p style="font-size:11px; color:#666;">Favor de compartir su comprobante de pago una vez realizada la transferencia.</p>
    @endif

    <div class="footer">
        Agradecemos su interés. Si tiene alguna pregunta, no dude en contactarnos.<br>
        <strong>GeneticLab</strong>
    </div>
</body>
</html>