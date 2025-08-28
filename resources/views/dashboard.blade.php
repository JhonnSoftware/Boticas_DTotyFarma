@extends('layouts.plantilla')

@section('title', 'Dashboard')

@section('content')

    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }

        .card-body {
            padding: 20px;
        }

        .metric-title {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 5px;
        }

        .metric-change {
            font-size: 14px;
            color: #28a745;
            /* Verde para positivo */
        }

        .metric-change.negative {
            color: #dc3545;
            /* Rojo para negativo */
        }

        .metric-change.zero {
            color: #6c757d;
            /* Gris para cero */
        }

        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 20%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stats-icon.purple {
            background-color: #6f42c1;
        }

        .stats-icon.blue {
            background-color: #0d6efd;
        }

        .stats-icon.green {
            background-color: #198754;
        }

        .stats-icon.red {
            background-color: #dc3545;
        }

        .stats-icon.yellow {
            background-color: #FFC107;
            /* Amarillo */
        }

        .stats-icon.orange {
            background-color: #FD7E14;
            /* Naranja */
        }

        .stats-icon.danger {
            background-color: #DC3545;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .font-semibold {
            font-weight: 500;
        }

        .font-extrabold {
            font-weight: 700;
        }

        .list-group-item {
            transition: background-color 0.2s ease;
        }

        .list-group-item:hover {
            background-color: #f1f1f1;
        }
    </style>

    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Buenos dias
                    {{ auth()->user()->name }}!</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--
                    <div class="col-5 align-self-center">
                        <div class="customize-input float-right">
                            <select
                                class="custom-select custom-select-set form-control bg-white border-0 custom-shadow custom-radius">
                                <option selected>Aug 19</option>
                                <option value="1">July 19</option>
                                <option value="2">Jun 19</option>
                            </select>
                        </div>
                    </div> -->
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon blue">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Total Usuarios</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalUsuarios }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Total Clientes</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalClientes }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon red">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Total Productos</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalProductos }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="fas fa-tags"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Total Categorías</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalCategorias }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon yellow">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Ventas del Mes</h6>
                                <h6 class="font-extrabold mb-0">S/ {{ number_format($ventasDelMes, 2) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon orange">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Compras del Mes</h6>
                                <h6 class="font-extrabold mb-0">S/ {{ number_format($comprasDelMes, 2) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon danger">
                                    <i class="fas fa-ban"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Ventas Anuladas</h6>
                                <h6 class="font-extrabold mb-0">S/ {{ number_format($ventasAnuladas, 2) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Compras Anuladas</h6>
                                <h6 class="font-extrabold mb-0">S/ {{ number_format($comprasAnuladas, 2) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">

            <div class="col-lg-6 col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: #0A7ABF;">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Ventas por Mes</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ventasChart" height="150"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: #198754;">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i> Productos más Vendidos</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="productosChart" height="150"></canvas>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: #dc3545;">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Stock Crítico</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="stockChart" height="180"></canvas>
                        <div class="mt-3 text-center">
                            <span class="badge bg-danger">Crítico: {{ $stockBajo }}</span>
                            <span class="badge bg-success">Normal: {{ $stockNormal }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: #0d6efd;">
                        <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i> Cantidad de Ventas vs Compras</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="transaccionesChart" height="300"></canvas>
                        <div class="mt-3 text-center">
                            <span class="badge bg-success">Ventas: {{ $cantidadVentas }}</span>
                            <span class="badge bg-danger">Compras: {{ $cantidadCompras }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: #6f42c1;">
                        <h5 class="mb-0"><i class="fas fa-undo-alt me-2"></i> Devoluciones Totales</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="devolucionesChart" height="200"></canvas>
                        <div class="mt-3 text-center">
                            <span class="badge bg-primary">Ventas: {{ $devolucionesVentas }}</span>
                            <span class="badge bg-warning text-dark">Compras: {{ $devolucionesCompras }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">

            <div class="col-lg-6 col-md-12">
                <div class="card shadow-sm mb-4 border-0" style="border-radius:16px; overflow:hidden;">
                    {{-- Header con gradiente a tu paleta --}}
                    <div class="card-header text-white border-0"
                        style="background:linear-gradient(135deg,#0A7ABF,#25A6D9);">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-list-ol me-2"></i> Top 5 Productos Más Vendidos
                        </h5>
                    </div>

                    <div class="card-body p-0" style="background:#F2F2F2;">
                        @php
                            $totalTop = max(1, $productosMasVendidos->sum('total_vendidos') ?? 0);
                        @endphp

                        @if ($productosMasVendidos->isEmpty())
                            <div class="p-4 text-center text-muted">No hay ventas registradas para mostrar.</div>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach ($productosMasVendidos as $detalle)
                                    @php
                                        $p = $detalle->producto ?? null;

                                        // Datos básicos seguros
                                        $desc = $p->descripcion ?? 'Producto eliminado';
                                        $cod = $p->codigo ?? 'N/A';
                                        $foto = url($p->foto ?? 'imagenes/producto_defecto.jpg');
                                        $lab = $p->laboratorio ?? '—';
                                        $pres = $p->presentacion ?? '—';
                                        $uXB = $p->unidades_por_blister ?? null;
                                        $uXC = $p->unidades_por_caja ?? null;

                                        // Stock y mínimos (unidad)
                                        $stockU = (int) ($p->cantidad ?? 0);
                                        $minU = (int) ($p->stock_minimo ?? 0);
                                        $stockBadge =
                                            $stockU === 0
                                                ? ['Agotado', 'danger']
                                                : ($minU > 0 && $stockU <= $minU
                                                    ? ['Bajo', 'warning']
                                                    : ['OK', 'success']);

                                        // Vencimiento
                                        $dias = isset($p->fecha_vencimiento)
                                            ? now()->diffInDays(\Carbon\Carbon::parse($p->fecha_vencimiento), false)
                                            : null;
                                        $vBadge = ['Vigente', 'success'];
                                        if ($dias !== null) {
                                            if ($dias < 0) {
                                                $vBadge = ['Vencido', 'danger'];
                                            } elseif ($dias <= 30) {
                                                $vBadge = ["{$dias} d", 'warning'];
                                            }
                                        }

                                        // Porcentaje del Top
                                        $vendidos = (int) $detalle->total_vendidos;
                                        $pct = round(($vendidos / $totalTop) * 100);
                                        $rank = $loop->iteration;
                                    @endphp

                                    <li class="list-group-item py-3"
                                        style="background:#fff; border:0; border-bottom:1px solid rgba(0,0,0,.06);">
                                        <div class="d-flex align-items-center">
                                            {{-- Imagen --}}
                                            <img src="{{ $foto }}" alt="Img" class="rounded me-3"
                                                width="52" height="52"
                                                style="object-fit:cover; border:1px solid rgba(0,0,0,.1);"
                                                onerror="this.src='{{ asset('imagenes/producto_defecto.jpg') }}'">

                                            <div class="flex-grow-1">
                                                {{-- Título + código + laboratorio/presentación --}}
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div>
                                                        <strong class="d-block">{{ $rank }}.
                                                            {{ $desc }}</strong>
                                                        <small class="text-muted">Código: {{ $cod }} · Lab:
                                                            {{ $lab }} · {{ $pres }}</small>

                                                        {{-- Chips de conversión (si existen) --}}
                                                        <div class="mt-1">
                                                            @if ($uXB)
                                                                <span class="badge rounded-pill text-dark"
                                                                    style="background:#E9F5FE; border:1px solid #CDE8FA;">
                                                                    <i class="fas fa-box-open me-1"
                                                                        style="color:#0A7ABF"></i>{{ $uXB }}
                                                                    u/blíster
                                                                </span>
                                                            @endif
                                                            @if ($uXC)
                                                                <span class="badge rounded-pill text-dark"
                                                                    style="background:#E9F5FE; border:1px solid #CDE8FA;">
                                                                    <i class="fas fa-cubes me-1"
                                                                        style="color:#0A7ABF"></i>{{ $uXC }}
                                                                    u/caja
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Badges estado y vencimiento --}}
                                                    <div class="text-end">
                                                        <span
                                                            class="badge bg-{{ $stockBadge[1] }} me-1">{{ $stockBadge[0] }}</span>
                                                        <span class="badge bg-{{ $vBadge[1] }}">
                                                            <i class="fas fa-calendar-alt me-1"></i>{{ $vBadge[0] }}
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- Progreso de participación dentro del Top 5 --}}
                                                <div class="mt-2">
                                                    <div class="d-flex justify-content-between small">
                                                        <span class="text-muted">{{ $vendidos }} und</span>
                                                        <span class="text-muted">{{ $pct }}%</span>
                                                    </div>
                                                    <div class="progress"
                                                        style="height:8px; border-radius:10px; background:#F2F2F2;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ $pct }}%; background: linear-gradient(90deg,#6EBF49,#8BBF65);"
                                                            aria-valuenow="{{ $pct }}" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Trofeo/medalla por ranking --}}
                                            <div class="ms-3 text-center" style="min-width:34px;">
                                                @if ($rank === 1)
                                                    <i class="fas fa-trophy" style="color:#6EBF49; font-size:20px;"></i>
                                                @elseif($rank === 2)
                                                    <i class="fas fa-medal" style="color:#25A6D9; font-size:18px;"></i>
                                                @elseif($rank === 3)
                                                    <i class="fas fa-medal" style="color:#0A7ABF; font-size:18px;"></i>
                                                @else
                                                    <span class="badge rounded-pill"
                                                        style="background:#F2F2F2; color:#0A7ABF;">#{{ $rank }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color:#dc3545;">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Top 5 Productos con Menor Stock (U / B / C)
                        </h5>
                    </div>

                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($productosStockBajoTop as $p)
                                @php
                                    $img = url($p->foto ?? 'imagenes/producto_defecto.jpg');

                                    // Stocks y mínimos
                                    $u = (int) ($p->cantidad ?? 0);
                                    $b = (int) ($p->cantidad_blister ?? 0);
                                    $c = (int) ($p->cantidad_caja ?? 0);

                                    $mu = (int) ($p->stock_minimo ?? 0);
                                    $mb = (int) ($p->stock_minimo_blister ?? 0);
                                    $mc = (int) ($p->stock_minimo_caja ?? 0);

                                    // Estados por presentación
                                    $uCritico = $mu > 0 ? $u < $mu : false;
                                    $bCritico = $mb > 0 ? $b < $mb : false;
                                    $cCritico = $mc > 0 ? $c < $mc : false;

                                    // Barra de riesgo (usa ratio_min calculado en el controlador)
                                    $ratio = (float) ($p->ratio_min ?? 9999); // 9999 = sin mínimos definidos
                                    $pct = $ratio === 9999 ? 100 : max(0, min(120, round($ratio * 100)));
                                    $barColor = $pct < 60 ? '#dc3545' : ($pct < 100 ? '#ffc107' : '#28a745');

                                    // Vencimiento (opcional)
                                    $vencTxt = null;
                                    if (!empty($p->fecha_vencimiento)) {
                                        $fv = \Carbon\Carbon::parse($p->fecha_vencimiento)->startOfDay();
                                        $hoy = now()->startOfDay();
                                        $dias = $hoy->diffInDays($fv, false); // firmado
                                        if ($dias < 0) {
                                            $vencTxt = 'Vencido';
                                        } elseif ($dias <= 30) {
                                            $vencTxt = "Vence en {$dias} d";
                                        }
                                    }

                                    $esCritico = $uCritico || $bCritico || $cCritico;
                                @endphp

                                <li class="list-group-item d-flex align-items-center gap-3">
                                    <img src="{{ $img }}" alt="Imagen" class="rounded" width="50"
                                        height="50" style="object-fit:cover;border:1px solid #ccc;">

                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <strong>{{ $loop->iteration }}. {{ $p->descripcion }}</strong>
                                            @if ($vencTxt === 'Vencido')
                                                <span class="badge bg-danger">Vencido</span>
                                            @elseif($vencTxt)
                                                <span class="badge bg-warning text-dark">{{ $vencTxt }}</span>
                                            @endif
                                        </div>

                                        <!-- Badges por presentación -->
                                        <div class="mt-1 d-flex flex-wrap gap-2">
                                            <span class="badge {{ $uCritico ? 'bg-danger' : 'bg-success' }}">
                                                U: {{ $u }}@if ($mu > 0)
                                                    / Min {{ $mu }}
                                                @endif
                                            </span>
                                            <span class="badge {{ $bCritico ? 'bg-danger' : 'bg-success' }}">
                                                B: {{ $b }}@if ($mb > 0)
                                                    / Min {{ $mb }}
                                                @endif
                                            </span>
                                            <span class="badge {{ $cCritico ? 'bg-danger' : 'bg-success' }}">
                                                C: {{ $c }}@if ($mc > 0)
                                                    / Min {{ $mc }}
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Barra de riesgo visual -->
                                        <div class="mt-2"
                                            style="height:8px;background:#e9ecef;border-radius:6px;overflow:hidden;">
                                            <div
                                                style="width: {{ $pct }}%; height:100%; background: {{ $barColor }};">
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Riesgo (peor ratio U/B/C):
                                            {{ $ratio === 9999 ? '—' : number_format($ratio, 2) }}
                                        </small>
                                    </div>

                                    <span class="badge {{ $esCritico ? 'bg-danger' : 'bg-success' }} rounded-pill">
                                        {{ $esCritico ? 'Crítico' : 'OK' }}
                                    </span>
                                </li>
                                @empty
                                    <li class="list-group-item text-center text-muted">No hay productos con bajo stock.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>



            </div>
        </div>

    @endsection

    @section('scripts')
        <script>
            const ctx = document.getElementById('ventasChart').getContext('2d');
            const ventasChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Ventas por Mes (S/.)',
                        data: {!! json_encode($data) !!},
                        borderColor: '#0A7ABF',
                        backgroundColor: 'rgba(10, 122, 191, 0.2)',
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#0A7ABF'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total S/.'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mes'
                            }
                        }
                    }
                }
            });
        </script>
        <script>
            const ctxProductos = document.getElementById('productosChart').getContext('2d');
            const productosChart = new Chart(ctxProductos, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($productosNombres) !!},
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: {!! json_encode($productosCantidades) !!},
                        backgroundColor: '#6EBF49'
                    }]
                },
                options: {
                    indexAxis: 'y', // ← para barras horizontales
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Top Productos Vendidos'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Unidades'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Productos'
                            }
                        }
                    }
                }
            });
        </script>
        <script>
            const ctxStock = document.getElementById('stockChart').getContext('2d');
            const stockChart = new Chart(ctxStock, {
                type: 'doughnut',
                data: {
                    labels: ['Stock Crítico', 'Stock Óptimo'],
                    datasets: [{
                        data: [{{ $stockBajo }}, {{ $stockNormal }}],
                        backgroundColor: ['#dc3545', '#28a745'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Distribución del Stock'
                        }
                    }
                }
            });
        </script>
        <script>
            const ctxDevoluciones = document.getElementById('devolucionesChart').getContext('2d');
            const devolucionesChart = new Chart(ctxDevoluciones, {
                type: 'doughnut',
                data: {
                    labels: ['Devoluciones de Ventas', 'Devoluciones de Compras'],
                    datasets: [{
                        data: [{{ $devolucionesVentas }}, {{ $devolucionesCompras }}],
                        backgroundColor: ['#0d6efd', '#ffc107'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Comparativa de Devoluciones'
                        }
                    }
                }
            });
        </script>
        <script>
            const ctxTransacciones = document.getElementById('transaccionesChart').getContext('2d');
            const transaccionesChart = new Chart(ctxTransacciones, {
                type: 'bar',
                data: {
                    labels: ['Ventas', 'Compras'],
                    datasets: [{
                        label: 'Cantidad de transacciones',
                        data: [{{ $cantidadVentas }}, {{ $cantidadCompras }}],
                        backgroundColor: ['#198754', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Comparativa total de registros'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad'
                            }
                        }
                    }
                }
            });
        </script>

    @endsection
