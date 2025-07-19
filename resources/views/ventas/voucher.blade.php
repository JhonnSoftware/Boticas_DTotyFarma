<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Voucher de Venta</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 280px;
            margin: auto;
            font-size: 12px;
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

        td, th {
            padding: 2px 0;
            font-size: 12px;
        }

        .right {
            text-align: right;
        }

        .small {
            font-size: 11px;
        }
    </style>
</head>
<body>

    <div class="center">
        <p class="bold">BOTICAS D'TOTY FARMA</p>
        <p>Av. Central N° 123 - Huancayo</p>
        <p>Tel: 987654321</p>
        <div class="separator"></div>
        <p class="bold">VOUCHER DE VENTA</p>
        <p>N°: {{ $venta->codigo }}</p>
        <p>Fecha: {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</p>
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
            <td class="bold">TOTAL:</td>
            <td class="right">S/ {{ number_format($venta->total, 2) }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <p class="center">¡Gracias por su compra!</p>

</body>
</html>

<script>
    window.onload = function () {
        window.print();
    };
</script>
