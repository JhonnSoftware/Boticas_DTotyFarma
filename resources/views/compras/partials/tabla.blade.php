@forelse($compras as $compra)
    <tr>
        <td class="px-2 py-4">{{ $compra->codigo }}</td>
        <td>{{ $compra->proveedor->nombre }}</td>
        <td>{{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y H:i') }}</td>
        <td>S/ {{ number_format($compra->total, 2) }}</td>
        <td>{{ $compra->pago->nombre }}</td>
        <td>{{ $compra->documento->nombre }}</td>
        <td>{{ $compra->usuario->name }}</td>
        <td>
            <span
                style="background: {{ $compra->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                        color: {{ $compra->estado == 'Activo' ? '#159258' : '#780909' }};
                                                        padding: 4px; border-radius:4px;">
                {{ $compra->estado }}
            </span>
        </td>
        <td>
            <div class="d-flex gap-2">
                {{-- Ver factura --}}
                @if ($compra->archivo_factura)
                    <a href="{{ url('storage/orden_compra/' . $compra->archivo_factura) }}" target="_blank"
                        class="btn btn-sm d-inline-flex justify-content-center align-items-center"
                        style="background-color: #0A7ABF; color: white; border-radius: 50%; width: 36px; height: 36px;">
                        <i class="fa fa-file-pdf"></i>
                    </a>
                @endif

                {{-- Devolver compra --}}
                @if ($compra->estado === 'Activo' && $compra->detalles->count() > 0)
                    <button type="button" class="btn btn-sm btn-warning btn-devolver" data-id="{{ $compra->id }}"
                        title="Devolver compra"
                        style="background-color: #ffc107; color: #212529; border-radius: 50%; width: 36px; height: 36px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: 0.3s;">
                        <i class="fas fa-undo-alt"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center py-4">No se encontraron compras
            registradas.</td>
    </tr>
@endforelse
