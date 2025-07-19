@extends('layouts.plantilla')

@php
    use Carbon\Carbon;
@endphp

@section('title', 'Módulo Detalle de Productos')

@section('content')

    @php
        $productosStockBajo = $productos->filter(fn($p) => $p->cantidad < $p->stock_minimo);
    @endphp

    <style>
        .bg-silver {
            background-color: #B0B0B0 !important;
        }
    </style>

    <div class="container-fluid">
        <h2 class="text-center text-dark fw-bold mb-4">Detalle de Productos</h2>

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

        {{-- Botón de cambio de vista --}}
        <div class="d-flex justify-content-end px-3 mb-3">
            <button id="toggleViewBtn" class="btn btn-outline-secondary btn-sm rounded-pill shadow-sm">
                <i class="fas fa-sync-alt me-1"></i> Cambiar a vista de Tabla
            </button>
        </div>

        {{-- Filtros --}}
        <div class="d-flex flex-wrap gap-2 px-3 mb-4 btn-group" role="group">
            <button class="btn btn-outline-dark btn-sm rounded-pill filter-btn" data-filter="all">
                <i class="fas fa-layer-group me-1"></i> Todos
            </button>
            <button class="btn btn-outline-danger btn-sm rounded-pill filter-btn" data-filter="vencido">
                <i class="fas fa-times-circle me-1"></i> Vencidos
            </button>
            <button class="btn btn-outline-warning btn-sm rounded-pill filter-btn" data-filter="3meses">
                <i class="fas fa-hourglass-start me-1"></i> ≤ 3 meses
            </button>
            <button class="btn btn-outline-success btn-sm rounded-pill filter-btn" data-filter="6meses">
                <i class="fas fa-hourglass-half me-1"></i> ≤ 6 meses
            </button>
            <button class="btn btn-outline-primary btn-sm rounded-pill filter-btn" data-filter="9meses">
                <i class="fas fa-hourglass-end me-1"></i> ≤ 9 meses
            </button>
            <button class="btn btn-outline-secondary btn-sm rounded-pill filter-btn" data-filter="10mas">
                <i class="fas fa-calendar-check me-1"></i> ≥ 10 meses
            </button>
            <button class="btn btn-outline-secondary btn-sm rounded-pill filter-btn" data-filter="bajo">
                <i class="fas fa-exclamation-triangle me-1"></i> Stock Bajo
            </button>
        </div>

        {{-- Vista en Tarjetas --}}
        <div id="tarjeta-view" class="row g-4 justify-content-center">
            @foreach ($productos as $producto)
                @php
                    $vencimiento = Carbon::parse($producto->fecha_vencimiento);
                    $meses = Carbon::now()->diffInMonths($vencimiento, false);
                    $filtros = [];
                    $bgColorCard = '';
                    $textColor = 'text-dark';

                    if ($meses < 0) {
                        $bgColorCard = 'bg-danger';
                        $textColor = 'text-white';
                        $filtros[] = 'vencido';
                    } elseif ($meses <= 3) {
                        $bgColorCard = 'bg-warning';
                        $textColor = 'text-white';
                        $filtros[] = '3meses';
                    } elseif ($meses <= 6) {
                        $bgColorCard = 'bg-success';
                        $textColor = 'text-white';
                        $filtros[] = '6meses';
                    } elseif ($meses <= 9) {
                        $bgColorCard = 'bg-primary';
                        $textColor = 'text-white';
                        $filtros[] = '9meses';
                    } else {
                        $bgColorCard = 'bg-silver';
                        $filtros[] = '10mas';
                    }

                    if ($producto->cantidad < $producto->stock_minimo) {
                        $filtros[] = 'bajo';
                    }

                    $filtro = implode(' ', $filtros);
                @endphp

                <div class="col-xl-4 col-lg-5 col-md-6 producto-card" data-filtro="{{ $filtro }}">
                    <div class="card shadow-sm {{ $bgColorCard }} {{ $textColor }}" style="border-radius: 16px;">
                        <div
                            class="card-header border-0 d-flex justify-content-between align-items-center px-4 pt-4 bg-transparent">
                            <div>
                                <h6 class="fw-bold mb-0">Código</h6>
                                <h5 class="mb-0">{{ $producto->codigo }}</h5>
                            </div>
                            <span class="badge rounded-pill bg-light text-dark px-3 py-2">
                                {{ $producto->cantidad }} unidades
                            </span>
                        </div>
                        <div class="card-body px-4 py-2">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="fw-semibold mb-2">{{ $producto->descripcion }}</h6>
                                    <div class="small">
                                        <p class="mb-1"><strong>Presentación:</strong> {{ $producto->presentacion }}</p>
                                        <p class="mb-1"><strong>Categoría:</strong>
                                            {{ $producto->categoria->nombre ?? '-' }}</p>
                                        <p class="mb-1"><strong>Laboratorio:</strong> {{ $producto->laboratorio }}</p>
                                        <p class="mb-1"><strong>Proveedor:</strong>
                                            {{ $producto->proveedor->nombre ?? '-' }}</p>
                                        <p class="mb-1"><strong>Vence:</strong> {{ $vencimiento->format('d/m/Y') }}</p>
                                        <p class="mb-1"><strong>Stock mínimo:</strong> {{ $producto->stock_minimo }}</p>
                                        <p class="mb-1"><strong>Descuento:</strong> {{ $producto->descuento }}%</p>
                                        <p class="mb-1"><strong>Compra:</strong> S/.
                                            {{ number_format($producto->precio_compra, 2) }}</p>
                                        <p class="mb-1"><strong>Venta:</strong> S/.
                                            {{ number_format($producto->precio_venta, 2) }}</p>
                                        <p class="mb-0"><strong>Estado:</strong>
                                            <span
                                                class="badge {{ $producto->estado === 'Activo' ? 'bg-light text-success' : 'bg-light text-danger' }}">
                                                {{ $producto->estado }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="ms-3 mt-2">
                                    <img src="{{ url($producto->foto) }}"
                                        style="width: 70px; height: 70px; object-fit: cover;" alt="Foto del producto"
                                        class="rounded-circle border">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-end px-4 pb-4">
                            <a href="#" class="btn btn-sm btn-light rounded-pill px-3 me-2">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <a href="#" class="btn btn-sm btn-light rounded-pill px-3">
                                <i class="fas fa-trash-alt me-1"></i>Eliminar
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
        {{-- Vista en Tabla --}}
        <div id="tabla-view" class="table-responsive px-3" style="display: none;">
            <table class="table table-hover table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Presentación</th>
                        <th>Proveedor</th>
                        <th>Categoría</th>
                        <th>Laboratorio</th>
                        <th>Stock</th>
                        <th>Stock Mínimo</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $producto)
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
                            $isStockBajo = $producto->cantidad < $producto->stock_minimo;
                        @endphp
                        <tr class="{{ $rowClass }} {{ $isStockBajo ? 'stock-bajo-row' : '' }}">
                            <td>{{ $producto->codigo }}</td>
                            <td>{{ $producto->descripcion }}</td>
                            <td>{{ $producto->presentacion }}</td>
                            <td>{{ $producto->proveedor->nombre ?? '-' }}</td>
                            <td>{{ $producto->categoria->nombre ?? '-' }}</td>
                            <td>{{ $producto->laboratorio }}</td>
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
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modal de stock bajo --}}
        @if ($productosStockBajo->count() > 0)
            <!-- Modal estilizado de advertencia -->
            <div class="modal fade" id="modalStockBajo" tabindex="-1" aria-labelledby="modalStockBajoLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                        <!-- Encabezado con degradado de advertencia -->
                        <div class="modal-header text-white py-4"
                            style="background: linear-gradient(135deg, #0A7ABF, #25A6D9);">
                            <h5 class="modal-title fw-bold d-flex align-items-center mb-0" id="modalStockBajoLabel">
                                <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                                Productos con Stock Bajo
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>

                        <!-- Cuerpo con fondo neutro -->
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
                                            style="background-color: #f12711; color: white;">Stock:
                                            {{ $p->cantidad }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Footer con botones estilizados -->
                        <div class="modal-footer justify-content-between px-4 py-3" style="background-color: #F2F2F2;">
                            <button type="button" class="btn" data-bs-dismiss="modal"
                                style="background-color: #8BBF65; color: white;">
                                <i class="fas fa-times me-1"></i> Cerrar
                            </button>
                            <button type="button" class="btn" data-filter="bajo" data-bs-dismiss="modal"
                                style="background-color: #0A7ABF; color: white;">
                                <i class="fas fa-filter me-1"></i> Ver solo stock bajo
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        @endif

    </div>


@endsection

@section('scripts')

    <!-- ✅ Bootstrap JS para que los modales funcionen -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Filtros
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const filtro = btn.getAttribute('data-filter');

                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                document.querySelectorAll('.producto-card').forEach(card => {
                    const filtros = card.dataset.filtro.split(" ");
                    card.style.display = (filtro === 'all' || filtros.includes(filtro)) ? 'block' :
                        'none';
                });

                document.querySelectorAll('#tabla-view tbody tr').forEach(row => {
                    if (filtro === 'all') {
                        row.style.display = '';
                    } else if (filtro === 'bajo') {
                        row.style.display = row.classList.contains('stock-bajo-row') ? '' : 'none';
                    } else {
                        row.style.display = '';
                    }
                });
            });
        });

        // Vista tarjeta / tabla
        document.getElementById('toggleViewBtn').addEventListener('click', () => {
            const tarjetaView = document.getElementById('tarjeta-view');
            const tablaView = document.getElementById('tabla-view');
            const btn = document.getElementById('toggleViewBtn');

            if (tarjetaView.style.display === 'none') {
                tarjetaView.style.display = 'flex';
                tablaView.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Cambiar a vista de Tabla';
            } else {
                tarjetaView.style.display = 'none';
                tablaView.style.display = 'block';
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Cambiar a vista de Tarjetas';
            }
        });

        // Mostrar modal automáticamente si hay productos con stock bajo
        @if ($productosStockBajo->count() > 0)
            window.addEventListener('DOMContentLoaded', () => {
                const modalStock = new bootstrap.Modal(document.getElementById('modalStockBajo'));
                modalStock.show();
            });
        @endif
    </script>
@endsection
