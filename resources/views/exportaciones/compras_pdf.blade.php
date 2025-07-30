<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Historial de Compras</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Historial de Compras</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Proveedor</th>
                <th>Documento</th>
                <th>Pago</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($compras as $c)
                <tr>
                    <td>{{ $c->codigo }}</td>
                    <td>{{ $c->proveedor->nombre ?? '' }}</td>
                    <td>{{ $c->documento->nombre ?? '' }}</td>
                    <td>{{ $c->pago->nombre ?? '' }}</td>
                    <td>S/ {{ number_format($c->total, 2) }}</td>
                    <td>{{ $c->estado }}</td>
                    <td>{{ $c->fecha }}</td>
                    <td>{{ $c->usuario->name ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
