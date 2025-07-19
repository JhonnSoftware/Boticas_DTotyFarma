@forelse($ventas as $venta)
    <tr>
        <td class="px-2 py-4">{{ $venta->codigo }}</td>
        <td>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellidos }}</td>
        <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
        <td>S/ {{ number_format($venta->total, 2) }}</td>

        <td>{{ $venta->pago->nombre }}</td>
        <td>{{ $venta->documento->nombre }}</td>
        <td>{{ $venta->usuario->name }}</td>

        <td>
            <span
                style="background: {{ $venta->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                        color: {{ $venta->estado == 'Activo' ? '#159258' : '#780909' }};
                                                        padding: 4px; border-radius:4px;">
                {{ $venta->estado }}
            </span>
        </td>
        <td>
            @if (strtolower($venta->documento->nombre) === 'voucher')
                <a href="{{ url('storage/vouchers/voucher_' . $venta->codigo . '.pdf') }}" target="_blank"
                    class="btn btn-sm d-inline-flex justify-content-center align-items-center"
                    style="background-color: #ff4c4c; color: white; border-radius: 50%; width: 36px; height: 36px;">
                    <i class="fa fa-file-pdf"></i>
                </a>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>

    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4">No se encontraron ventas
            registradas.</td>
    </tr>
@endforelse
