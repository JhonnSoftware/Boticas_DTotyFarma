@forelse ($cajas as $caja)
    <tr>
        <td class="py-3">S/ {{ number_format($caja->monto_apertura, 2) }}</td>
        <td class="py-3">
            {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i') }}
        </td>
        <td class="py-3">
            {{ $caja->monto_cierre !== null ? 'S/ ' . number_format($caja->monto_cierre, 2) : '-' }}
        </td>
        <td class="py-3">
            {{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i') : '-' }}
        </td>
        <td class="py-3">
            <span
                style="background: {{ $caja->estado == 'abierta' ? '#6ff073' : '#d4d4d4' }};
                                                        color: {{ $caja->estado == 'abierta' ? '#159258' : '#555' }};
                                                        padding: 4px 8px;
                                                        border-radius: 4px;
                                                        font-size: 14px;">
                {{ ucfirst($caja->estado) }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="py-3 text-muted">No se encontraron registros de cajas.
        </td>
    </tr>
@endforelse
