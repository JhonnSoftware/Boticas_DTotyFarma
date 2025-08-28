<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Voucher de Venta</title>
    <link rel="icon" href="{{ asset('imagenes/logo_empresa.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('imagenes/logo_empresa.png') }}" type="image/png">

    <style>
        @page {
            margin: 0cm;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0;
            padding: 5mm;
            font-size: 11px;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .separator {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 2px 0;
            font-size: 11px;
        }

        .right {
            text-align: right;
        }

        .small {
            font-size: 10px;
        }
    </style>

</head>

<body>

    <div class="center">
        <p class="bold">BOTICAS D'TOTY FARMA</p>
        <p>AV 7 DE JUNIO 361 LOS FICUS</p>
        <p>SANTA ANITA - LIMA - LIMA</p>
        <div class="separator"></div>
        <p class="bold">VOUCHER DE VENTA</p>
        <p>N°: {{ $venta->codigo }}</p>
        <p>Fecha Emision: {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</p>
        <div class="separator"></div>
    </div>

    <p><span class="bold">Cliente:</span> {{ $venta->cliente->nombre }} {{ $venta->cliente->apellidos }}</p>
    <p><span class="bold">DNI:</span> {{ $venta->cliente->dni }}</p>

    <div class="separator"></div>

    <table>
        <thead>
            <tr>
                <th>CANT</th>
                <th>DESCRIPCIÓN</th>
                <th class="right">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>{{ $detalle->producto->descripcion }}</td>
                    <td class="right">S/ {{ number_format($detalle->sub_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <table>
        <tr>
            <td class="bold">Subtotal:</td>
            <td class="right">S/ {{ number_format($venta->total + $venta->descuento_total, 2) }}</td>
        </tr>
        <tr>
            <td class="bold">Descuento:</td>
            <td class="right">S/ {{ number_format($venta->descuento_total, 2) }}</td>
        </tr>
        <tr>
            <td class="bold right" colspan="2">TOTAL: S/ {{ number_format($venta->total, 2) }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <p>
        <span class="bold">CAJA:</span> {{ $venta->caja_id ?? '1' }}
        <span class="bold" style="margin-left: 10px;">USU:</span> {{ strtoupper(auth()->user()->name) }}
        <span class="bold" style="margin-left: 10px;">IMP:</span> {{ strtoupper(auth()->user()->name) }}
    </p>

    <div class="separator"></div>

    <p class="center bold">*NO ACEPTAMOS CAMBIOS - DEVOLUCIONES*</p>


</body>

</html>

<script>
    window.open("{{ session('voucher_url') }}", '_blank');
</script>
