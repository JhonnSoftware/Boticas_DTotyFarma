@forelse ($devoluciones as $devolucion)
    <tr>
        <td>{{ $devolucion->compra->codigo }}</td>
        <td>
            {{ $devolucion->compra->proveedor->nombre ?? 'N/A' }}
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
