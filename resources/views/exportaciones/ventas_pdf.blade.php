<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Historial de Ventas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Historial de Ventas</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Documento</th>
                <th>Pago</th>
                <th>Total</th>
                <th>IGV</th>
                <th>Descuento</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
                <tr>
                    <td>{{ $venta->codigo }}</td>
                    <td>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellidos }}</td>
                    <td>{{ $venta->documento->nombre }}</td>
                    <td>{{ $venta->pago->nombre }}</td>
                    <td>S/ {{ number_format($venta->total, 2) }}</td>
                    <td>S/ {{ number_format($venta->igv, 2) }}</td>
                    <td>S/ {{ number_format($venta->descuento_total, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                    <td>{{ $venta->estado }}</td>
                    <td>{{ $venta->usuario->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
