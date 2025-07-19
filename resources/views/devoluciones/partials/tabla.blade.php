@forelse ($devoluciones as $devolucion)
    <tr>
        <td>{{ $devolucion->venta->codigo }}</td>
        <td>
            {{ $devolucion->venta->cliente->nombre ?? 'N/A' }}
            {{ $devolucion->venta->cliente->apellidos ?? '' }}
        </td>
        <td>{{ $devolucion->producto->descripcion ?? '—' }}</td>
        <td>{{ $devolucion->cantidad }}</td>
        <td>{{ $devolucion->motivo }}</td>
        <td>{{ \Carbon\Carbon::parse($devolucion->fecha)->format('d/m/Y H:i') }}</td>
        <td>{{ $devolucion->usuario->name }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">No se encontraron devoluciones.</td>
    </tr>
@endforelse
