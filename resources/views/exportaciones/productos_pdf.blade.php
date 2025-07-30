<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Lista de Productos</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Presentación</th>
                <th>Laboratorio</th>
                <th>Lote</th>
                <th>Cantidad</th>
                <th>Stock Mínimo</th>
                <th>Descuento</th>
                <th>Fecha de Vencimiento</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Proveedor</th>
                <th>Categoría</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $producto)
                <tr>
                    <td>{{ $producto->codigo }}</td>
                    <td>{{ $producto->descripcion }}</td>
                    <td>{{ $producto->presentacion }}</td>
                    <td>{{ $producto->laboratorio }}</td>
                    <td>{{ $producto->lote }}</td>
                    <td>{{ $producto->cantidad }}</td>
                    <td>{{ $producto->stock_minimo }}</td>
                    <td>{{ $producto->descuento }}</td>
                    <td>{{ $producto->fecha_vencimiento }}</td>
                    <td>{{ $producto->precio_compra }}</td>
                    <td>{{ $producto->precio_venta }}</td>
                    <td>{{ optional($producto->proveedor)->nombre }}</td>
                    <td>{{ optional($producto->categoria)->nombre }}</td>
                    <td>{{ $producto->estado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
