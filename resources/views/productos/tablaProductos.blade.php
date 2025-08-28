@extends('layouts.plantilla')

@php
    use Carbon\Carbon;
@endphp

@section('title', 'Módulo Productos - Vista Tabla')

@section('content')
<div class="container-fluid">
    <h2 class="text-center text-dark fw-bold mb-4">Productos en Modo Tabla</h2>

    {{-- ===== Barra de Filtros ===== --}}
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 14px;">
        <div class="card-body">
            <form method="GET" action="{{ route('productos.tablaProductos') }}" class="row g-2 align-items-end">

                {{-- Texto libre --}}
                <div class="col-md-4">
                    <label class="form-label mb-1">Buscar (texto)</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" class="form-control"
                               value="{{ old('q', $q ?? '') }}"
                               placeholder="Descripción, código, lote, laboratorio o presentación…"
                               autocomplete="off">
                    </div>
                </div>

                {{-- Categoría --}}
                <div class="col-md-3">
                    <label class="form-label mb-1">Categoría</label>
                    <select name="categoria" class="form-select form-select-sm">
                        <option value="">— Todas —</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ (string)($categoriaId ?? '') === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Laboratorio --}}
                <div class="col-md-3">
                    <label class="form-label mb-1">Laboratorio</label>
                    <select name="laboratorio" class="form-select form-select-sm">
                        <option value="">— Todos —</option>
                        @foreach ($laboratorios as $lab)
                            <option value="{{ $lab }}" {{ ($laboratorio ?? '') === $lab ? 'selected' : '' }}>
                                {{ $lab }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Presentación --}}
                <div class="col-md-2">
                    <label class="form-label mb-1">Presentación</label>
                    <select name="presentacion" class="form-select form-select-sm">
                        <option value="">— Todas —</option>
                        @foreach ($presentaciones as $pres)
                            <option value="{{ $pres }}" {{ ($presentacion ?? '') === $pres ? 'selected' : '' }}>
                                {{ $pres }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botones --}}
                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-primary btn-sm rounded-pill px-3" type="submit">
                        <i class="fas fa-filter me-1"></i> Aplicar
                    </button>
                    <a href="{{ route('productos.tablaProductos') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fas fa-undo me-1"></i> Limpiar
                    </a>
                </div>
            </form>

            {{-- Chips de filtros activos --}}
            @php
                $chips = [];
                if (!empty($q ?? ''))            $chips[] = ['icon'=>'fa-search', 'label'=>'Búsqueda',     'value'=>$q];
                if (!empty($categoriaId ?? '')) {
                    $catSel = $categorias->firstWhere('id', $categoriaId);
                    if ($catSel) $chips[] = ['icon'=>'fa-tags', 'label'=>'Categoría', 'value'=>$catSel->nombre];
                }
                if (!empty($laboratorio ?? ''))  $chips[] = ['icon'=>'fa-flask', 'label'=>'Laboratorio',  'value'=>$laboratorio];
                if (!empty($presentacion ?? '')) $chips[] = ['icon'=>'fa-box',   'label'=>'Presentación', 'value'=>$presentacion];
            @endphp

            @if (count($chips))
                <div class="mt-3">
                    @foreach ($chips as $chip)
                        <span class="badge rounded-pill bg-light text-dark border me-2 mb-2 px-3 py-2">
                            <i class="fas {{ $chip['icon'] }} me-1"></i>
                            <strong>{{ $chip['label'] }}:</strong> {{ $chip['value'] }}
                        </span>
                    @endforeach
                    <a href="{{ route('productos.tablaProductos') }}" class="btn btn-link btn-sm text-decoration-none">
                        Limpiar filtros
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Leyenda de colores --}}
    <div class="mb-3 px-1">
        <div class="alert alert-info shadow-sm border-start border-4 border-primary mb-0">
            <h6 class="fw-bold mb-2">Leyenda de Colores de Vencimiento:</h6>
            <ul class="mb-0 small">
                <li><span class="badge bg-danger">Rojo</span>: Producto vencido</li>
                <li><span class="badge bg-warning">Naranja</span>: Vence en ≤ 3 meses</li>
                <li><span class="badge bg-success">Verde</span>: Vence en ≤ 6 meses</li>
                <li><span class="badge bg-primary">Azul</span>: Vence en ≤ 9 meses</li>
                <li><span class="badge bg-secondary">Gris</span>: Vence en ≥ 10 meses</li>
            </ul>
        </div>
    </div>

    {{-- Tabla de productos --}}
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Presentación</th>
                    <th>Laboratorio</th>
                    <th>Lote</th>
                    <th>Proveedor</th>
                    <th>Categorías</th>
                    <th>Cantidad</th>
                    <th>Stock Mínimo</th>
                    <th>Precio Compra</th>
                    <th>Precio Venta</th>
                    <th>Fecha Venc.</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    @php
                        $vencimiento = Carbon::parse($producto->fecha_vencimiento);
                        $meses = Carbon::now()->diffInMonths($vencimiento, false);
                        $rowClass =
                            $meses < 0
                                ? 'table-danger'
                                : ($meses <= 3
                                    ? 'table-warning'
                                    : ($meses <= 6
                                        ? 'table-success'
                                        : ($meses <= 9
                                            ? 'table-primary'
                                            : 'table-secondary')));
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $producto->codigo }}</td>
                        <td class="text-start">{{ $producto->descripcion }}</td>
                        <td>{{ $producto->presentacion }}</td>
                        <td>{{ $producto->laboratorio }}</td>
                        <td>{{ $producto->lote }}</td>
                        <td>{{ $producto->proveedor->nombre ?? '-' }}</td>
                        <td>{{ $producto->categorias->pluck('nombre')->implode(', ') ?: '-' }}</td>
                        <td>{{ $producto->cantidad }}</td>
                        <td>{{ $producto->stock_minimo }}</td>
                        <td>S/. {{ number_format($producto->precio_compra, 2) }}</td>
                        <td>S/. {{ number_format($producto->precio_venta, 2) }}</td>
                        <td>{{ $vencimiento->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $producto->estado === 'Activo' ? 'bg-success' : 'bg-danger' }}">
                                {{ $producto->estado }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-muted">No se encontraron productos con los filtros aplicados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación (conserva filtros) --}}
    <div class="mt-3">
        {{ $productos->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
