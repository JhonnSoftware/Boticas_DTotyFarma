<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Proveedores</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Lista de Proveedores</h2>
    <table>
        <thead>
            <tr>
                <th>RUC</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Contacto</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($proveedores as $p)
                <tr>
                    <td>{{ $p->ruc }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->telefono }}</td>
                    <td>{{ $p->correo }}</td>
                    <td>{{ $p->direccion }}</td>
                    <td>{{ $p->contacto }}</td>
                    <td>{{ $p->estado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
