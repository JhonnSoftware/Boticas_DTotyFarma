<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Devoluciones de Compras</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Reporte de Devoluciones de Compras</h2>
    <table>
        <thead>
            <tr>
                <th>Código Compra</th>
                <th>Producto</th>
                <th>Motivo</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($devoluciones as $dev)
                <tr>
                    <td>{{ $dev->compra->codigo ?? '—' }}</td>
                    <td>{{ $dev->producto->descripcion ?? '—' }}</td>
                    <td>{{ $dev->motivo }}</td>
                    <td>{{ $dev->cantidad }}</td>
                    <td>{{ $dev->fecha }}</td>
                    <td>{{ $dev->usuario->name ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
