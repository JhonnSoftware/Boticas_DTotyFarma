<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Devoluciones de Ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Reporte de Devoluciones de Ventas</h2>

    <table>
        <thead>
            <tr>
                <th>Código Venta</th>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Motivo</th>
                <th>Fecha</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devoluciones as $d)
                <tr>
                    <td>{{ $d->venta->codigo }}</td>
                    <td>{{ $d->venta->cliente->nombre }} {{ $d->venta->cliente->apellidos }}</td>
                    <td>{{ $d->producto->descripcion }}</td>
                    <td>{{ $d->cantidad }}</td>
                    <td>{{ $d->motivo }}</td>
                    <td>{{ \Carbon\Carbon::parse($d->fecha)->format('d/m/Y H:i') }}</td>
                    <td>{{ $d->usuario->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
