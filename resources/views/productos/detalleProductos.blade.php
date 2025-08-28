@extends('layouts.plantilla')

@php
    use Carbon\Carbon;
    $fActual = request('filter', 'all');
    $qActual = request('q', '');
    $perPageSel = (int) request('perPage', $perPage ?? 10);
    $urlCon = function (array $extra = []) {
        return request()->fullUrlWithQuery(array_merge(['page' => 1], $extra));
    };
@endphp

@section('title', 'Módulo Detalle de Productos')

@section('content')

    <style>
        .bg-silver {
            background-color: #B0B0B0 !important;
        }

        .card-soft {
            border-radius: 16px;
            overflow: hidden;
            border: 0;
        }

        .chip {
            display: inline-block;
            padding: .25rem .6rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, .18);
            margin: 0 .25rem .25rem 0;
        }

        .price-tile {
            background: rgba(255, 255, 255, .12);
            border-radius: .75rem;
            padding: .45rem .6rem;
        }

        .divider {
            height: 1px;
            background: rgba(255, 255, 255, .2);
            margin: .5rem 0 .75rem;
        }

        .foto-prod {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
        }

        .ribbon {
            position: absolute;
            top: .75rem;
            right: .75rem;
            padding: .25rem .6rem;
            border-radius: .5rem;
            background: rgba(255, 255, 255, .18);
        }

        .icono {
            width: 1rem;
            text-align: center;
            display: inline-block;
        }

        /* Paginación estilo pill (aplica al render bootstrap-5) */
        .pagination .page-link {
            border: 0;
            border-radius: 9999px !important;
            padding: .45rem .8rem;
            transition: all .2s ease;
            color: #007bff;
        }

        .pagination .page-item.active .page-link {
            background: #0d6efd;
            color: #fff;
            box-shadow: 0 6px 16px rgba(13, 110, 253, .25);
        }

        .pagination .page-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
            background: rgba(13, 110, 253, .1);
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            background-color: transparent !important;
            color: #bbb !important;
            border: none !important;
        }

        @media (min-width: 1200px) {
            #modalEditarProducto .modal-dialog.modal-xl {
                max-width: 1240px;
            }
        }

        /* === RESET DURO solo dentro del paginador inferior === */
        .footer-pager .pagination {
            margin: 0;
            gap: .25rem;
        }

        /* Quita pseudoelementos/íconos enormes inyectados por CSS global */
        .footer-pager .page-link::before,
        .footer-pager .page-link::after {
            content: none !important;
            display: none !important;
        }

        /* Normaliza tamaño y padding */
        .footer-pager .page-link {
            padding: .32rem .6rem !important;
            /* pequeño */
            font-size: .875rem !important;
            /* ~14px */
            line-height: 1.2 !important;
            border-radius: .5rem !important;
            border: 1px solid #dee2e6 !important;
            color: #0d6efd !important;
            background: #fff !important;
            box-shadow: none !important;
            transform: none !important;
            width: auto !important;
            height: auto !important;
        }

        /* Estados */
        .footer-pager .page-item.active .page-link {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }

        .footer-pager .page-item.disabled .page-link {
            color: #adb5bd !important;
            background: #fff !important;
            border-color: #dee2e6 !important;
        }

        .footer-pager .page-link:hover {
            background: #eef5ff !important;
            color: #0b5ed7 !important;
            text-decoration: none !important;
        }

        /* Por si hay SVG/íconos internos con tamaños locos */
        .footer-pager .page-link i,
        .footer-pager .page-link svg {
            font-size: 1em !important;
            width: 1em !important;
            height: 1em !important;
            transform: none !important;
        }

        /* === Searchbar bonito === */
        .search-wrap {
            max-width: 920px;
            /* opcional: limita el ancho para que respire */
        }

        .searchbar {
            border: 1px solid #e6e9ef;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 6px 18px rgba(13, 110, 253, .06);
            transition: box-shadow .2s ease, border-color .2s ease, transform .15s ease;
        }

        .searchbar:focus-within {
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, .12);
            transform: translateY(-1px);
        }

        /* icono a la izquierda */
        .searchbar .input-group-text {
            background: transparent;
            border: 0;
            padding: .85rem .9rem;
            color: #6c757d;
        }

        /* input más alto y sin borde */
        .searchbar .form-control {
            border: 0 !important;
            padding: .9rem 1rem;
            font-size: 0.95rem;
            box-shadow: none !important;
        }

        /* botón limpiar dentro del grupo */
        .searchbar .btn-clear {
            border: 0;
            padding: .55rem .9rem;
            background: #f6f8fb;
        }

        .searchbar .btn-clear:hover {
            background: #eef2f8;
        }

        /* botón buscar primario (si lo usas) */
        .searchbar .btn-search {
            padding: .55rem 1rem;
        }
    </style>

    <div class="container-fluid">
        <h2 class="text-center text-dark fw-bold mb-4">Detalle de Productos</h2>

        <a href="{{ route('productos.tablaProductos') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm hover-shadow" style="transition: all 0.2s ease-in-out; border-radius: 20px;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="text-dark mb-0 fw-bold" style="font-size: 22px;">
                            <i class="fas fa-table me-2 text-primary"></i> Ver en Modo Tabla
                        </h2>
                    </div>
                </div>
            </div>
        </a>

        {{-- Leyenda de colores --}}
        <div class="mb-4 px-3">
            <div class="alert alert-info shadow-sm border-start border-4 border-primary">
                <h6 class="fw-bold mb-2">Leyenda de Colores de Vencimiento:</h6>
                <ul class="mb-0 small">
                    <li><span class="badge bg-danger">Rojo</span>: Producto vencido</li>
                    <li><span class="badge bg-warning">Naranja</span>: Vence en ≤ 3 meses</li>
                    <li><span class="badge bg-success">Verde</span>: Vence en ≤ 6 meses</li>
                    <li><span class="badge bg-primary">Azul</span>: Vence en ≤ 9 meses</li>
                    <li><span class="badge bg-silver text-dark">Plomo</span>: Vence en ≥ 10 meses</li>
                </ul>
            </div>
        </div>

        {{-- Buscador (GET global) --}}
        <div class="px-3 mb-3 d-flex justify-content-center">
            <form method="GET" class="search-wrap w-100">
                <input type="hidden" name="filter" value="{{ $fActual }}">
                <input type="hidden" name="perPage" value="{{ $perPageSel }}">

                <div class="input-group searchbar">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>

                    <input type="text" name="q" class="form-control" value="{{ $qActual }}"
                        placeholder="Buscar por descripción, presentación, laboratorio o lote…" autocomplete="off" />

                    {{-- (Opcional) Botón Buscar explícito; puedes quitarlo si no lo quieres --}}
                    <button class="btn btn-primary btn-search" type="submit">
                        Buscar
                    </button>

                    {{-- Botón limpiar dentro del grupo (mantiene tus parámetros) --}}
                    <a class="btn btn-clear"
                        href="{{ request()->url() }}?filter={{ $fActual }}&perPage={{ $perPageSel }}"
                        title="Limpiar">
                        <i class="fas fa-times"></i>
                    </a>

                </div>
            </form>
        </div>

        {{-- Filtros (enlaces GET globales) --}}
        <div class="d-flex flex-wrap gap-2 px-3 mb-4 btn-group" role="group">
            <a href="{{ $urlCon(['filter' => 'bajo', 'q' => $qActual, 'perPage' => $perPageSel]) }}"
                class="btn btn-outline-danger btn-sm rounded-pill {{ $fActual === 'bajo' ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle me-1"></i> Stock Bajo
            </a>
            <a href="{{ $urlCon(['filter' => 'all', 'q' => $qActual, 'perPage' => $perPageSel]) }}"
                class="btn btn-outline-dark btn-sm rounded-pill {{ $fActual === 'all' ? 'active' : '' }}">
                <i class="fas fa-layer-group me-1"></i> Todos
            </a>
            <a href="{{ $urlCon(['filter' => '3meses', 'q' => $qActual, 'perPage' => $perPageSel]) }}"
                class="btn btn-outline-warning btn-sm rounded-pill {{ $fActual === '3meses' ? 'active' : '' }}">
                <i class="fas fa-hourglass-start me-1"></i> ≤ 3 meses
            </a>
            <a href="{{ $urlCon(['filter' => '6meses', 'q' => $qActual, 'perPage' => $perPageSel]) }}"
                class="btn btn-outline-success btn-sm rounded-pill {{ $fActual === '6meses' ? 'active' : '' }}">
                <i class="fas fa-hourglass-half me-1"></i> ≤ 6 meses
            </a>
            <a href="{{ $urlCon(['filter' => '9meses', 'q' => $qActual, 'perPage' => $perPageSel]) }}"
                class="btn btn-outline-primary btn-sm rounded-pill {{ $fActual === '9meses' ? 'active' : '' }}">
                <i class="fas fa-hourglass-end me-1"></i> ≤ 9 meses
            </a>
            <a href="{{ $urlCon(['filter' => '10mas', 'q' => $qActual, 'perPage' => $perPageSel]) }}"
                class="btn btn-outline-secondary btn-sm rounded-pill {{ $fActual === '10mas' ? 'active' : '' }}">
                <i class="fas fa-calendar-check me-1"></i> ≥ 10 meses
            </a>
            <a href="{{ $urlCon(['filter' => 'vencido', 'q' => $qActual, 'perPage' => $perPageSel]) }}"
                class="btn btn-outline-danger btn-sm rounded-pill {{ $fActual === 'vencido' ? 'active' : '' }}">
                <i class="fas fa-times-circle me-1"></i> Vencidos
            </a>
        </div>

        {{-- Vista en Tarjetas --}}
        <div id="tarjeta-view" class="row g-4 justify-content-center">
            @foreach ($productos as $producto)
                @php
                    $vencimiento = Carbon::parse($producto->fecha_vencimiento);
                    $meses = Carbon::now()->diffInMonths($vencimiento, false);
                    $bgColorCard = '';
                    $textColor = 'text-dark';
                    if ($meses < 0) {
                        $bgColorCard = 'bg-danger';
                        $textColor = 'text-white';
                    } elseif ($meses <= 3) {
                        $bgColorCard = 'bg-warning';
                        $textColor = 'text-white';
                    } elseif ($meses <= 6) {
                        $bgColorCard = 'bg-success';
                        $textColor = 'text-white';
                    } elseif ($meses <= 9) {
                        $bgColorCard = 'bg-primary';
                        $textColor = 'text-white';
                    } else {
                        $bgColorCard = 'bg-silver';
                    }
                @endphp

                <div class="col-xl-4 col-lg-5 col-md-6">
                    <div class="card card-soft shadow-sm {{ $bgColorCard }} {{ $textColor }} position-relative">

                        {{-- ====== Prep de datos ====== --}}
                        @php
                            $vencimiento = \Carbon\Carbon::parse($producto->fecha_vencimiento);
                            $dias = now()->diffInDays($vencimiento, false);

                            $u = (int) ($producto->cantidad ?? 0);
                            $b = $producto->cantidad_blister;
                            $c = $producto->cantidad_caja;

                            $minU = (int) ($producto->stock_minimo ?? 0);
                            $minB = (int) ($producto->stock_minimo_blister ?? 0);
                            $minC = (int) ($producto->stock_minimo_caja ?? 0);

                            $estadoU =
                                $u === 0
                                    ? ['Agotado', 'danger']
                                    : ($minU > 0 && $u <= $minU
                                        ? ['Bajo', 'warning']
                                        : ['OK', 'success']);
                            $estadoB = is_null($b)
                                ? ['N/A', 'secondary']
                                : ($b === 0
                                    ? ['Agotado', 'danger']
                                    : ($minB > 0 && $b <= $minB
                                        ? ['Bajo', 'warning']
                                        : ['OK', 'success']));
                            $estadoC = is_null($c)
                                ? ['N/A', 'secondary']
                                : ($c === 0
                                    ? ['Agotado', 'danger']
                                    : ($minC > 0 && $c <= $minC
                                        ? ['Bajo', 'warning']
                                        : ['OK', 'success']));

                            $hayCero = $u === 0 || (!is_null($b) && $b === 0) || (!is_null($c) && $c === 0);
                            $hayBajo =
                                !$hayCero &&
                                (($minU > 0 && $u <= $minU) ||
                                    (!is_null($b) && $minB > 0 && $b <= $minB) ||
                                    (!is_null($c) && $minC > 0 && $c <= $minC));
                            $headerBadgeClass = $hayCero
                                ? 'bg-danger text-white'
                                : ($hayBajo
                                    ? 'bg-warning text-dark'
                                    : 'bg-light text-dark');

                            $uXB_val = $producto->unidades_por_blister;
                            $uXC_val = $producto->unidades_por_caja;
                            $txt_uXB = $uXB_val ? $uXB_val . ' u/blíster' : '—';
                            $txt_uXC = $uXC_val ? $uXC_val . ' u/caja' : '—';

                            $ventaU = $producto->precio_venta;
                            $ventaB = $producto->precio_venta_blister;
                            $ventaC = $producto->precio_venta_caja;

                            $descU = (float) ($producto->descuento ?? 0);
                            $descB = (float) ($producto->descuento_blister ?? 0);
                            $descC = (float) ($producto->descuento_caja ?? 0);

                            $ventaUFinal = $ventaU !== null ? $ventaU * (1 - $descU / 100) : null;
                            $ventaBFinal = $ventaB !== null ? $ventaB * (1 - $descB / 100) : null;
                            $ventaCFinal = $ventaC !== null ? $ventaC * (1 - $descC / 100) : null;

                            $compraU = $producto->precio_compra;
                            $compraB = $producto->precio_compra_blister;
                            $compraC = $producto->precio_compra_caja;

                            if ($dias < 0) {
                                $vBadge = ['Vencido', 'danger'];
                            } elseif ($dias <= 30) {
                                $vBadge = ["Vence en {$dias} d", 'warning'];
                            } else {
                                $vBadge = ['Vigente', 'success'];
                            }

                            $cats = $producto->categorias->pluck('nombre');
                            $clase = optional($producto->clase)->nombre ?: '-';
                            $generico = optional($producto->generico)->nombre ?: '-';
                        @endphp

                        {{-- ====== Overlay: estado vencimiento ====== --}}
                        <span class="badge position-absolute top-0 end-0 m-3 bg-{{ $vBadge[1] }}">
                            <i class="bi bi-calendar-event me-1"></i>{{ $vBadge[0] }}
                        </span>

                        {{-- ====== Header ====== --}}
                        <div
                            class="card-header border-0 d-flex justify-content-between align-items-center px-4 pt-4 bg-transparent">
                            <div>
                                <div class="small opacity-75">Código</div>
                                <h5 class="mb-0 fw-bold">{{ $producto->codigo }}</h5>
                            </div>

                            <span class="badge rounded-pill {{ $headerBadgeClass }} px-3 py-2">
                                U: {{ $u }} · Bl: {{ is_null($b) ? '—' : $b }} · Cj:
                                {{ is_null($c) ? '—' : $c }}
                            </span>
                        </div>

                        {{-- ====== Body ====== --}}
                        <div class="card-body px-4 pb-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-grow-1">
                                    <h6 class="fw-semibold mb-2">{{ $producto->descripcion }}</h6>

                                    {{-- Chips de categorías --}}
                                    <div class="mb-2">
                                        @forelse($cats as $c)
                                            <span class="chip small">{{ $c }}</span>
                                        @empty
                                            <span class="chip small">Sin categoría</span>
                                        @endforelse
                                    </div>

                                    {{-- Detalles rápidos --}}
                                    <div class="small">
                                        <div class="mb-1"><strong>Presentación:</strong> {{ $producto->presentacion }}
                                        </div>
                                        <div class="mb-1"><strong>Laboratorio:</strong> {{ $producto->laboratorio }}
                                        </div>
                                        <div class="mb-1"><strong>Proveedor:</strong>
                                            {{ optional($producto->proveedor)->nombre ?? '-' }}</div>
                                        <div class="mb-1"><strong>Lote:</strong> {{ $producto->lote }}</div>
                                        <div class="mb-1"><strong>Vence:</strong> {{ $vencimiento->format('d/m/Y') }}
                                        </div>
                                        <div class="mb-1">
                                            <strong>Conversión:</strong> {{ $txt_uXB }}
                                            <span class="opacity-75">· (= {{ $txt_uXC }})</span>
                                        </div>
                                    </div>

                                    <div class="divider"></div>

                                    {{-- Stock vs mínimo por presentación --}}
                                    <div class="row g-2 align-items-center mb-2 small">
                                        <div class="col-4">
                                            <div><strong>Unidad</strong></div>
                                            <span class="badge bg-{{ $estadoU[1] }}">{{ $estadoU[0] }}</span>
                                            <div class="opacity-75">Min: {{ $minU }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div><strong>Blíster</strong></div>
                                            <span class="badge bg-{{ $estadoB[1] }}">{{ $estadoB[0] }}</span>
                                            <div class="opacity-75">Min: {{ is_null($b) ? '—' : $minB }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div><strong>Caja</strong></div>
                                            <span class="badge bg-{{ $estadoC[1] }}">{{ $estadoC[0] }}</span>
                                            <div class="opacity-75">Min: {{ is_null($c) ? '—' : $minC }}</div>
                                        </div>
                                    </div>

                                    <div class="divider"></div>

                                    {{-- Precios de VENTA (con descuento si aplica) --}}
                                    <div class="small fw-semibold mb-2">Venta</div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-4">
                                            <div class="price-tile text-center small">
                                                Unidad<br>
                                                @if ($ventaU !== null)
                                                    @if ($descU > 0)
                                                        <span class="text-decoration-line-through">S/.
                                                            {{ number_format($ventaU, 2) }}</span><br>
                                                        <strong>S/. {{ number_format($ventaUFinal, 2) }}</strong>
                                                        <div class="opacity-75">
                                                            (-{{ rtrim(rtrim(number_format($descU, 2), '0'), '.') }}%)
                                                        </div>
                                                    @else
                                                        <strong>S/. {{ number_format($ventaU, 2) }}</strong>
                                                    @endif
                                                @else
                                                    <strong>—</strong>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="price-tile text-center small">
                                                Blíster<br>
                                                @if (!is_null($ventaB))
                                                    @if ($descB > 0)
                                                        <span class="text-decoration-line-through">S/.
                                                            {{ number_format($ventaB, 2) }}</span><br>
                                                        <strong>S/. {{ number_format($ventaBFinal, 2) }}</strong>
                                                        <div class="opacity-75">
                                                            (-{{ rtrim(rtrim(number_format($descB, 2), '0'), '.') }}%)
                                                        </div>
                                                    @else
                                                        <strong>S/. {{ number_format($ventaB, 2) }}</strong>
                                                    @endif
                                                @else
                                                    <strong>—</strong>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="price-tile text-center small">
                                                Caja<br>
                                                @if (!is_null($ventaC))
                                                    @if ($descC > 0)
                                                        <span class="text-decoration-line-through">S/.
                                                            {{ number_format($ventaC, 2) }}</span><br>
                                                        <strong>S/. {{ number_format($ventaCFinal, 2) }}</strong>
                                                        <div class="opacity-75">
                                                            (-{{ rtrim(rtrim(number_format($descC, 2), '0'), '.') }}%)
                                                        </div>
                                                    @else
                                                        <strong>S/. {{ number_format($ventaC, 2) }}</strong>
                                                    @endif
                                                @else
                                                    <strong>—</strong>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Precios de COMPRA --}}
                                    <div class="small fw-semibold mb-2">Compra</div>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <div class="price-tile text-center small">Unidad<br><strong>
                                                    {{ $compraU !== null ? 'S/. ' . number_format($compraU, 2) : '—' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="price-tile text-center small">Blíster<br><strong>
                                                    {{ $compraB !== null ? 'S/. ' . number_format($compraB, 2) : '—' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="price-tile text-center small">Caja<br><strong>
                                                    {{ $compraC !== null ? 'S/. ' . number_format($compraC, 2) : '—' }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <strong>Estado:</strong>
                                        <span
                                            class="badge {{ $producto->estado === 'Activo' ? 'bg-light text-success' : 'bg-light text-danger' }}">
                                            {{ $producto->estado }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Foto --}}
                                <div class="mt-1">
                                    <img src="{{ url($producto->foto) }}" alt="Foto del producto" class="foto-prod"
                                        loading="lazy" onerror="this.src='{{ asset('imagenes/producto_defecto.jpg') }}'">
                                </div>
                            </div>
                        </div>

                        {{-- ====== Footer ====== --}}
                        <div class="card-footer bg-transparent border-0 text-end px-4 pb-4">
                            <a href="#" class="btn btn-sm btn-light rounded-pill px-3 me-2 btn-editar-producto"
                                data-id="{{ $producto->id }}">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>

                            @if ($producto->estado === 'Activo')
                                <form action="{{ route('productos.desactivar', $producto->id) }}" method="POST"
                                    style="display:inline;" id="form-desactivar-card-{{ $producto->id }}">
                                    @csrf @method('PUT')
                                    <a href="#" class="btn btn-sm btn-light rounded-pill px-3"
                                        onclick="event.preventDefault(); confirmarDesactivacion({{ $producto->id }});">
                                        <i class="fas fa-trash-alt me-1"></i>Eliminar
                                    </a>
                                </form>
                            @else
                                <form action="{{ route('productos.activar', $producto->id) }}" method="POST"
                                    style="display:inline;" id="form-activar-card-{{ $producto->id }}">
                                    @csrf @method('PUT')
                                    <a href="#" class="btn btn-sm btn-success rounded-pill px-3"
                                        onclick="event.preventDefault(); confirmarActivacion({{ $producto->id }});">
                                        <i class="fas fa-refresh me-1"></i>Reactivar
                                    </a>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Barra inferior: perPage + resumen + paginación --}}
        @if ($productos instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="footer-pager d-flex flex-wrap gap-3 justify-content-between align-items-center mt-4 px-3">
                <form method="GET" id="perPageForm" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="q" value="{{ $qActual }}">
                    <input type="hidden" name="filter" value="{{ $fActual }}">
                    <label class="small text-muted mb-0">Por página</label>
                    <select name="perPage" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach ([6, 9, 10, 12, 15, 20, 30] as $n)
                            <option value="{{ $n }}" {{ (int) $perPageSel === $n ? 'selected' : '' }}>
                                {{ $n }}</option>
                        @endforeach
                    </select>
                </form>

                @if (method_exists($productos, 'total'))
                    <div class="small text-muted me-2">
                        Mostrando
                        <strong>{{ $productos->firstItem() }}</strong>–<strong>{{ $productos->lastItem() }}</strong>
                        de <strong>{{ $productos->total() }}</strong> productos
                    </div>
                @endif

                <div class="ms-auto">
                    {{ $productos->onEachSide(1)->links() }}
                </div>
            </div>
        @endif


        {{-- Modal de stock bajo --}}
        @if ($productosStockBajo->count() > 0)
            <div class="modal fade" id="modalStockBajo" tabindex="-1" aria-labelledby="modalStockBajoLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header text-white py-4"
                            style="background: linear-gradient(135deg, #0A7ABF, #25A6D9);">
                            <h5 class="modal-title fw-bold d-flex align-items-center mb-0" id="modalStockBajoLabel">
                                <i class="fas fa-exclamation-circle me-2 fs-4"></i> Productos con Stock Bajo
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body px-4 py-4" style="background-color: #F2F2F2;">
                            <p class="mb-3 text-dark">Se detectaron los siguientes productos con stock por debajo del
                                mínimo:</p>
                            <ul class="list-group">
                                @foreach ($productosStockBajo as $p)
                                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 rounded-3 shadow-sm mb-2"
                                        style="background-color: #ffffff;">
                                        <div>
                                            <strong class="text-dark">{{ $p->codigo }}</strong> – <span
                                                class="text-muted">{{ $p->descripcion }}</span>
                                        </div>
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background-color: #f12711; color: white;">
                                            Stock: {{ $p->cantidad }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="modal-footer justify-content-between px-4 py-3" style="background-color: #F2F2F2;">
                            <button type="button" class="btn" data-bs-dismiss="modal"
                                style="background-color: #8BBF65; color: white;">
                                <i class="fas fa-times me-1"></i> Cerrar
                            </button>
                            <a href="{{ $urlCon(['filter' => 'bajo', 'q' => $qActual, 'perPage' => $perPageSel]) }}"
                                class="btn" style="background-color: #0A7ABF; color: white;" data-bs-dismiss="modal">
                                <i class="fas fa-filter me-1"></i> Ver solo stock bajo
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        @endif

        <!-- Modal global de edición -->
        <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Editar producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" id="modalEditarBody">
                        <div class="p-5 text-center">
                            <div class="spinner-border" role="status" aria-hidden="true"></div>
                            <div class="mt-2">Cargando formulario…</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const m = document.getElementById('modalEditarProducto');
            if (m && m.parentElement !== document.body) {
                document.body.appendChild(m);
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Click en botón Editar (delegado)
            document.body.addEventListener('click', async (ev) => {
                const btn = ev.target.closest('.btn-editar-producto');
                if (!btn) return;

                ev.preventDefault();
                const id = btn.getAttribute('data-id');
                const modalEl = document.getElementById('modalEditarProducto');
                const modalBody = modalEl.querySelector('#modalEditarBody');

                modalBody.innerHTML = `
                    <div class="p-5 text-center">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <div class="mt-2">Cargando formulario…</div>
                    </div>
                `;

                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();

                try {
                    const resp = await fetch(`{{ url('productos') }}/${id}/edit-partial`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        cache: 'no-store'
                    });
                    if (!resp.ok) throw new Error(`Error ${resp.status} al cargar el formulario`);

                    const html = await resp.text();
                    modalBody.innerHTML = html;

                    if (window.jQuery && jQuery().select2) {
                        $('.select2-categorias-edit').select2({
                            dropdownParent: $('#modalEditarProducto'),
                            width: '100%',
                        });
                    }
                } catch (err) {
                    console.error(err);
                    modalBody.innerHTML = `
                        <div class="p-5 text-center">
                            <div class="text-danger mb-2"><i class="fas fa-triangle-exclamation"></i> No se pudo cargar el formulario.</div>
                            <div class="small text-muted">${err.message}</div>
                            <button class="btn btn-outline-secondary mt-3" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    `;
                }
            });

            @if ($productosStockBajo->count() > 0)
                const modalStock = new bootstrap.Modal(document.getElementById('modalStockBajo'));
                modalStock.show();
            @endif
        });

        function confirmarDesactivacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El producto será marcado como inactivo.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, desactivar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-desactivar-card-' + id) || document.getElementById(
                        'form-desactivar-' + id);
                    if (form) form.submit();
                }
            });
        }

        function confirmarActivacion(id) {
            Swal.fire({
                title: '¿Reingresar producto?',
                text: "El producto será marcado como activo nuevamente.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, reingresar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-activar-card-' + id) || document.getElementById(
                        'form-activar-' + id);
                    if (form) form.submit();
                }
            });
        }
    </script>
@endsection
