@extends('layouts.plantilla')

@section('title', 'Movimientos de Inventario')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-center text-primary">Movimientos de Inventario</h2>
            <p class="text-center text-muted">Historial de entradas y salidas de productos</p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card shadow rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Origen</th>
                            <th>Cantidad</th>
                            <th>Stock Anterior</th>
                            <th>Stock Actual</th>
                            <th>Documento Ref.</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movimientos as $mov)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y H:i') }}</td>
                                <td>{{ $mov->producto->descripcion ?? 'Sin producto' }}</td>
                                <td>
                                    <span class="badge {{ $mov->cantidad >= 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $mov->tipo_movimiento }}
                                    </span>
                                </td>
                                <td>{{ $mov->origen ?? 'N/A' }}</td>
                                <td class="{{ $mov->cantidad >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $mov->cantidad > 0 ? '+' : '' }}{{ $mov->cantidad }}
                                </td>
                                <td>{{ $mov->stock_anterior }}</td>
                                <td>{{ $mov->stock_actual }}</td>
                                <td>{{ $mov->documento_ref ?? '—' }}</td>
                                <td>{{ $mov->observacion ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    No se han registrado movimientos aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
