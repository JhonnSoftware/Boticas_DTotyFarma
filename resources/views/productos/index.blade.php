@extends('layouts.plantilla') <!-- Esto extiende la plantilla base -->

@section('title', 'Modulo Productos') <!-- Cambia el título de la página -->

@section('content')

    <style>
        .modal-content {
            animation: slideFadeIn 0.4s ease;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
            transform: translateY(-20px);
        }

        .modal.fade.show .modal-dialog {
            transform: translateY(0);
        }

        /* Botón vibrante */
        .btn-primary {
            background: linear-gradient(to right, #0A7ABF, #25A6D9);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .bg-gradient-warning {
            background: linear-gradient(90deg, #ffe259 0%, #ffa751 100%);
        }

        .entries-info {
            color: #6c757d;
            font-size: 14px;
        }

        .search-box {
            min-width: 200px;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fc;
            /* Un color de fondo para pruebas */
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination .page-item .page-link {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            text-align: center;
            padding: 0;
            line-height: 38px;
            border: none;
            background-color: transparent;
            color: black;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #ccc;
        }

        .pagination .page-link:focus {
            box-shadow: none;
        }

        .btnAgregarProducto:hover {
            background-color: #2275fc !important;
            color: #ffffff !important;
        }

        /* Contenedor del control */
        .select2-container .select2-selection {
            border: 1px solid #dee2e6 !important;
            border-radius: .75rem !important;
            min-height: 46px;
            background: #fff;
            padding: .25rem .25rem;
            /* espacio interno */
        }

        /* Estado foco */
        .select2-container--default.select2-container--focus .select2-selection {
            border-color: #0A7ABF !important;
            box-shadow: 0 0 0 .2rem rgba(10, 122, 191, .15);
        }

        /* Chips (selecciones) */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: #eef7ff;
            color: #0A7ABF;
            border: none;
            border-radius: 9999px;
            padding: .25rem .6rem;
            margin-top: .3rem;
            box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            margin-right: .35rem;
            color: inherit;
            opacity: .7;
        }

        /* Render y buscador */
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            gap: .25rem;
        }

        .select2-container .select2-search__field {
            margin-top: .25rem;
        }

        /* Dropdown */
        .select2-dropdown {
            border: 1px solid #e6e9ef;
            border-radius: .75rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        }

        .select2-results__option--highlighted {
            background: #0A7ABF !important;
        }
    </style>

    <!-- Botón y panel colapsable -->

    <div class="container-fluid">

        <a href="{{ route('productos.detalle', ['filtro' => 'agotados_unidad']) }}" class="text-decoration-none">
            <div class="card border-right shadow-sm hover-shadow" style="transition: 0.2s; border-radius: 20px;">
                <div class="card-body">
                    <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                            <div class="d-inline-flex align-items-center">
                                <h2 class="text-dark mb-1 font-weight-medium" style="font-size: 24px;">Ver Detalles</h2>

                                @if (($agotadosUnidad ?? 0) > 0)
                                    <span
                                        class="badge bg-warning font-12 text-dark font-weight-medium badge-pill ms-2 d-lg-block d-md-none">
                                        {{ $agotadosUnidad }} producto(s) con stock 0 (unidad)
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="ms-auto mt-md-3 mt-lg-0">
                            <span class="opacity-7 text-muted"><i data-feather="eye"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </a>


        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 20px;">
                    <div class="card-body">

                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title" style="font-size: 24px;">Lista de Productos</h4>
                        </div>
                        <div class="d-flex justify-content-end align-items-center flex-wrap gap-2 mb-3">

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch gap-2">
                                <div class="input-group">
                                    <form action="{{ route('productos.index') }}" method="GET" class="input-group">
                                        <input type="text" name="buscar" id="inputBusqueda" class="form-control"
                                            placeholder="Buscar por nombre del producto..." value="{{ request('buscar') }}"
                                            style="border-radius: 10px 0 0 10px; padding: 15px; height: auto;">
                                        <button type="submit" class="btn btn-primary"
                                            style="border-radius: 0 10px 10px 0; background: #ffffff; color: #000; border: 1px solid #eaecef; padding:15px;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="padding: 15px; border-radius: 10px;">
                                        <i class="fas fa-download me-1"></i> Exportar
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <li><a class="dropdown-item"
                                                href="{{ route('productos.exportar', ['formato' => 'pdf']) }}">
                                                <i class="far fa-file-pdf me-2 text-danger"></i>Exportar PDF</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('productos.exportar', ['formato' => 'xlsx']) }}">
                                                <i class="far fa-file-excel me-2 text-success"></i>Exportar Excel</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('productos.exportar', ['formato' => 'csv']) }}">
                                                <i class="fas fa-file-csv me-2 text-primary"></i>Exportar CSV</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('productos.exportar', ['formato' => 'txt']) }}">
                                                <i class="far fa-file-alt me-2 text-muted"></i>Exportar TXT</a></li>
                                    </ul>
                                </div>


                                <button class="btnAgregarProducto"
                                    style="white-space: nowrap; border: 1px solid #2275fc; border-radius: 10px; color: #2275fc; background: #fff; padding:15px; transition: background-color 0.3s ease, color 0.3s ease;"
                                    data-bs-toggle="modal" data-bs-target="#nuevoProducto">
                                    <i class="fas fa-plus"></i> Nuevo Producto
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr class="border-0">
                                        <th class="border-0 font-14 font-weight-medium text-black rounded-start">Codigo</th>
                                        <th class="border-0 font-14 font-weight-medium text-black px-2">Descripcion</th>
                                        <th class="border-0 font-14 font-weight-medium text-black px-2">Stock (Unid)</th>
                                                                                        <!-- <th class="border-0 font-14 font-weight-medium text-black">Presentacion</th>
                                                                                                <th class="border-0 font-14 font-weight-medium text-black">Laboratorio</th>
                                                                                                <th class="border-0 font-14 font-weight-medium text-black">Lote</th>
                                                                                                <th class="border-0 font-14 font-weight-medium text-black">Cantidad</th>
                                                                                                <th class="border-0 font-14 font-weight-medium text-black">Stock Minimo</th>
                                                                                                <th class="border-0 font-14 font-weight-medium text-black">Dscto</th>
                                                                                                <th class="border-0 font-14 font-weight-medium text-black">Caduca</th>
                                                                                                -->
                                        <th class="border-0 font-14 font-weight-medium text-black">P. Venta</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">P. Venta (Blister)</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">P. Venta (Caja)</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Foto</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Estado</th>
                                        <th class="border-0 font-14 font-weight-medium text-black rounded-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaProductos">
                                    @foreach ($productos as $producto)
                                        <tr>
                                            <td class="border-top-0 px-2 py-4">
                                                <div class="d-flex no-block align-items-center">
                                                    <div class="mr-3">
                                                        <img src="{{ url('imagenes/producto_icon1.png') }}" alt="user"
                                                            class="rounded-circle" width="45" height="45" />
                                                    </div>
                                                    <div class="">
                                                        <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                            {{ $producto->codigo }}</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->descripcion }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->cantidad }}</td>
                                            <!--
                                                                                                <td class="border-top-0 text-dark px-2 py-4">{{ $producto->laboratorio }}</td>
                                                                                                <td class="border-top-0 text-dark px-2 py-4">{{ $producto->lote }}</td>
                                                                                                <td class="border-top-0 text-dark px-2 py-4">{{ $producto->cantidad }}</td>
                                                                                                <td class="border-top-0 text-dark px-2 py-4">{{ $producto->stock_minimo }}
                                                                                                </td>
                                                                                                <td class="border-top-0 text-dark px-2 py-4">{{ $producto->descuento }}</td>
                                                                                                <td class="border-top-0 text-dark px-2 py-4">
                                                                                                    {{ $producto->fecha_vencimiento }}
                                                                                                </td>
                                                                                                -->
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->precio_venta }}
                                            </td>
                                            <td class="border-top-0 text-dark px-2 py-4">
                                                {{ $producto->precio_venta_blister }}
                                            </td>
                                            <td class="border-top-0 text-dark px-2 py-4">
                                                {{ $producto->precio_venta_caja }}
                                            </td>
                                            <td>
                                                <img src="{{ url($producto->foto) }}" alt="Foto del producto"
                                                    width="70">

                                            </td>
                                            <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
                                                <span
                                                    style="background: {{ $producto->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                            color: {{ $producto->estado == 'Activo' ? '#159258' : '#780909' }};
                                                            padding: 4px; border-radius:4px;">
                                                    {{ $producto->estado }}
                                                </span>
                                            </td>
                                            <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
                                                <!--
                                                                            <a href="#" class="text-primary me-2"><i
                                                                                    data-feather="eye"></i></a> -->

                                                <a href="#" class="text-success btn-edit"
                                                    data-id="{{ $producto->id }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditar">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                @if ($producto->estado === 'Activo')
                                                    <form action="{{ route('productos.desactivar', $producto->id) }}"
                                                        method="POST" style="display:inline;"
                                                        id="form-desactivar-{{ $producto->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <a href="#" class="text-danger"
                                                            onclick="event.preventDefault(); confirmarDesactivacion({{ $producto->id }});">
                                                            <i data-feather="trash-2"></i>
                                                        </a>
                                                    </form>
                                                @else
                                                    <form action="{{ route('productos.activar', $producto->id) }}"
                                                        method="POST" style="display:inline;"
                                                        id="form-activar-{{ $producto->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <a href="#" class="text-success"
                                                            onclick="event.preventDefault(); confirmarActivacion({{ $producto->id }});">
                                                            <i data-feather="refresh-ccw"></i>
                                                        </a>
                                                    </form>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                            {{-- 🚀 Modal único, al final del blade --}}
                            <div class="modal fade modal-edit-product" id="modalEditar" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content" id="modalEditarContent">
                                        <div class="p-4 text-center text-muted">Cargando…</div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center" id="pagination">
                                        <!-- Los elementos del paginador se generarán con JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nuevoProducto" tabindex="-1" aria-labelledby="nuevoProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content shadow-lg rounded-4 border-0">

                <!-- Encabezado -->
                <div class="modal-header bg-success text-white rounded-top-4 py-3">
                    <h5 class="modal-title fw-semibold" id="nuevoProductoLabel">
                        <i class="bi bi-capsule-pill me-2"></i>Registrar nuevo producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body p-4">
                    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data"
                        class="needs-validation" novalidate>
                        @csrf

                        <div class="row g-4">

                            {{-- 1) Información para venta rápida --}}

                            <!-- Descripción -->
                            <div class="col-md-8">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <input type="text" class="form-control rounded-3" id="descripcion" name="descripcion"
                                    required>
                                <div class="invalid-feedback">Por favor ingrese la descripción del producto.</div>
                            </div>
                            <!-- Código -->
                            <div class="col-md-4">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" class="form-control rounded-3" id="codigo" name="codigo"
                                    value="{{ $nuevoCodigo }}" readonly>
                            </div>

                            {{-- 2) Clasificación / Catálogos --}}

                            <!-- Categoría -->
                            <div class="col-md-4">
                                <label class="form-label mb-1">Categorías</label>
                                <select class="form-select select2-categorias" name="categorias[]" id="id_categoria"
                                    multiple required data-placeholder="Seleccionar categorias..">
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}"
                                            @if (collect(old('categorias'))->contains($categoria->id)) selected @endif>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Seleccione al menos una categoría.</div>
                            </div>

                            <!-- Clase -->
                            <div class="col-md-4">
                                <label for="id_clase" class="form-label">Clase terapéutica</label>
                                <select class="form-select rounded-3" id="id_clase" name="id_clase">
                                    <option value="">Seleccione clase</option>
                                    @foreach ($clases as $cl)
                                        <option value="{{ $cl->id }}">{{ $cl->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Genérico -->
                            <div class="col-md-4">
                                <label for="id_generico" class="form-label">Genérico / Principio activo</label>
                                <select class="form-select rounded-3" id="id_generico" name="id_generico">
                                    <option value="">Seleccione genérico</option>
                                    @foreach ($genericos as $ge)
                                        <option value="{{ $ge->id }}">{{ $ge->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3) Presentación comercial --}}

                            <!-- Presentación -->
                            <div class="col-md-6">
                                <label for="presentacion" class="form-label">Presentación</label>
                                <input type="text" class="form-control rounded-3" id="presentacion"
                                    name="presentacion" required>
                                <div class="invalid-feedback">Por favor ingrese la presentación del producto.</div>
                            </div>
                            <!-- Laboratorio -->
                            <div class="col-md-6">
                                <label for="laboratorio" class="form-label">Laboratorio</label>
                                <input type="text" class="form-control rounded-3" id="laboratorio" name="laboratorio"
                                    required>
                                <div class="invalid-feedback">Por favor ingrese el laboratorio del producto.</div>
                            </div>

                            <!-- Lote -->
                            <div class="col-md-6">
                                <label for="lote" class="form-label">Lote</label>
                                <input type="number" class="form-control rounded-3" id="lote" name="lote"
                                    required>
                                <div class="invalid-feedback">Por favor ingrese el lote.</div>
                            </div>
                            <!-- Fecha de vencimiento -->
                            <div class="col-md-6">
                                <label for="fecha_vencimiento" class="form-label">Fecha de vencimiento</label>
                                <input type="date" class="form-control rounded-3" id="fecha_vencimiento"
                                    name="fecha_vencimiento" required>
                                <div class="invalid-feedback">Por favor ingrese la fecha de vencimiento.</div>
                            </div>

                            <!-- Ratios de conversión (opcionales) -->
                            <div class="col-md-6">
                                <label for="unidades_por_blister" class="form-label">Unidades por blíster</label>
                                <input type="number" class="form-control rounded-3 ratio-field"
                                    id="unidades_por_blister" name="unidades_por_blister" min="0"
                                    value="{{ old('unidades_por_blister') }}" placeholder="Ej: 10 (0 = sin definir)">
                                <div class="form-text">Déjalo en 0 o vacío si no lo sabes. (Se guardará como <strong>sin
                                        definir</strong>)</div>
                            </div>

                            <div class="col-md-6">
                                <label for="unidades_por_caja" class="form-label">Unidades por caja</label>
                                <input type="number" class="form-control rounded-3 ratio-field" id="unidades_por_caja"
                                    name="unidades_por_caja" min="0" value="{{ old('unidades_por_caja') }}"
                                    placeholder="Ej: 100 (0 = sin definir)">
                                <div class="form-text">Déjalo en 0 o vacío si no lo sabes. (Se guardará como <strong>sin
                                        definir</strong>)</div>
                            </div>


                            <div class="col-md-4">
                                <label for="cantidad" class="form-label">Cantidad (en unidades)</label>
                                <input type="number" class="form-control rounded-3" id="cantidad" name="cantidad"
                                    min="0" required>
                                <div class="invalid-feedback">Ingrese la cantidad inicial (unidades).</div>
                            </div>
                            <!-- NUEVO: Stock en blíster -->
                            <div class="col-md-4">
                                <label for="cantidad_blister" class="form-label">Cantidad (en blíster)</label>
                                <input type="number" class="form-control rounded-3" id="cantidad_blister"
                                    name="cantidad_blister" min="0" value="{{ old('cantidad_blister') }}">
                                <div class="form-text">Dejar vacío si no aplica.</div>
                            </div>

                            <!-- NUEVO: Stock en caja -->
                            <div class="col-md-4">
                                <label for="cantidad_caja" class="form-label">Cantidad (en caja)</label>
                                <input type="number" class="form-control rounded-3" id="cantidad_caja"
                                    name="cantidad_caja" min="0" value="{{ old('cantidad_caja') }}">
                                <div class="form-text">Dejar vacío si no aplica.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="stock_minimo" class="form-label">Stock mínimo (en unidades)</label>
                                <input type="number" class="form-control rounded-3" id="stock_minimo"
                                    name="stock_minimo" min="0" required>
                                <div class="invalid-feedback">Ingrese el stock mínimo.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="stock_minimo_blister" class="form-label">Stock mínimo (en blister)</label>
                                <input type="number" class="form-control rounded-3" id="stock_minimo_blister"
                                    name="stock_minimo_blister" min="0">
                                <div class="form-text">Dejar vacío si no aplica.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="stock_minimo_caja" class="form-label">Stock mínimo (en caja)</label>
                                <input type="number" class="form-control rounded-3" id="stock_minimo_caja"
                                    name="stock_minimo_caja" min="0">
                                <div class="form-text">Dejar vacío si no aplica.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="descuento" class="form-label">Descuento por unidad (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100"
                                        class="form-control rounded-start-3" id="descuento" name="descuento"
                                        value="{{ old('descuento', 0) }}" required>
                                    <span class="input-group-text rounded-end-3">%</span>
                                </div>
                                <div class="invalid-feedback">Ingrese un porcentaje entre 0 y 100.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="descuento_blister" class="form-label">Descuento por blíster (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100"
                                        class="form-control rounded-start-3" id="descuento_blister"
                                        name="descuento_blister" value="{{ old('descuento_blister') }}">
                                    <span class="input-group-text rounded-end-3">%</span>
                                </div>
                                <div class="form-text">Dejar vacío si no aplica.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="descuento_caja" class="form-label">Descuento por caja (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100"
                                        class="form-control rounded-start-3" id="descuento_caja" name="descuento_caja"
                                        value="{{ old('descuento_caja') }}">
                                    <span class="input-group-text rounded-end-3">%</span>
                                </div>
                                <div class="form-text">Dejar vacío si no aplica.</div>
                            </div>

                            {{-- 6) Precios de venta (lo que usarás en caja) --}}

                            <div class="col-md-4">
                                <label for="precio_venta" class="form-label">Venta (unidad) S/</label>
                                <input type="number" step="0.01" class="form-control rounded-3" id="precio_venta"
                                    name="precio_venta" min="0" required>
                                <div class="invalid-feedback">Ingrese el precio de venta por unidad.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="precio_venta_blister" class="form-label">Venta (blíster) S/</label>
                                <input type="number" step="0.01" class="form-control rounded-3"
                                    id="precio_venta_blister" name="precio_venta_blister" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="precio_venta_caja" class="form-label">Venta (caja) S/</label>
                                <input type="number" step="0.01" class="form-control rounded-3"
                                    id="precio_venta_caja" name="precio_venta_caja" min="0">
                            </div>

                            {{-- 8) Precios de compra (para costos y kardex) --}}

                            <div class="col-md-4">
                                <label for="precio_compra" class="form-label">Compra (unidad) S/</label>
                                <input type="number" step="0.01" class="form-control rounded-3" id="precio_compra"
                                    name="precio_compra" min="0" required>
                                <div class="invalid-feedback">Ingrese el precio de compra por unidad.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="precio_compra_blister" class="form-label">Compra (blíster) S/</label>
                                <input type="number" step="0.01" class="form-control rounded-3"
                                    id="precio_compra_blister" name="precio_compra_blister" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="precio_compra_caja" class="form-label">Compra (caja) S/</label>
                                <input type="number" step="0.01" class="form-control rounded-3"
                                    id="precio_compra_caja" name="precio_compra_caja" min="0">
                            </div>

                            {{-- 9) Proveedor --}}

                            <div class="col-md-6">
                                <label for="id_proveedor" class="form-label">Proveedor</label>
                                <select class="form-select rounded-3" id="id_proveedor" name="id_proveedor" required>
                                    <option value="" disabled selected>Seleccione proveedor</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Por favor seleccione el proveedor.</div>
                            </div>

                            {{-- 10) Imagen --}}
                            <div class="col-md-6">
                                <label for="foto" class="form-label">Foto del producto</label>
                                <input type="file" class="form-control rounded-3" id="foto" name="foto"
                                    accept=".jpg,.jpeg,.png,.webp">
                            </div>

                            {{-- Estado --}}
                            <input type="hidden" name="estado" value="Activo">
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer mt-4 px-0">
                            <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success rounded-3">
                                <i class="bi bi-save me-1"></i>Guardar producto
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    @if ($productosStockBajo->count() > 0)
        <!-- Modal estilizado de advertencia -->
        <div class="modal fade" id="modalStockBajo" tabindex="-1" aria-labelledby="stockBajoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                    <!-- Encabezado con degradado azul -->
                    <div class="modal-header text-white py-4"
                        style="background: linear-gradient(135deg, #0A7ABF, #25A6D9);">
                        <h5 class="modal-title fw-bold d-flex align-items-center mb-0" id="stockBajoLabel">
                            <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                            Productos con Stock Bajo
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>

                    <!-- Cuerpo del modal -->
                    <div class="modal-body px-4 py-4"
                        style="background-color: #F2F2F2; max-height: 60vh; overflow-y: auto;">
                        <p class="text-secondary mb-4">
                            Se han identificado productos con stock por debajo del mínimo permitido. Revisa los siguientes
                            elementos:
                        </p>

                        <div class="row g-3">
                            @foreach ($productosStockBajo as $producto)
                                <div class="col-12">
                                    <div class="alert border-0 shadow-sm rounded-4 d-flex justify-content-between align-items-center p-3"
                                        style="background-color: #ffffff; border-left: 6px solid #6EBF49;">
                                        <div>
                                            <h6 class="mb-1 fw-semibold" style="color: #6EBF49;">
                                                <i class="fas fa-capsules me-1"></i>{{ $producto->descripcion }}
                                            </h6>
                                            <small class="text-muted">
                                                Stock actual: <strong>{{ $producto->cantidad }}</strong> | Mínimo
                                                requerido:
                                                <strong>{{ $producto->stock_minimo }}</strong>
                                            </small>
                                        </div>
                                        <span class="badge px-3 py-2 rounded-pill shadow-sm"
                                            style="background-color: #D9534F; color: white;">
                                            ⚠ Bajo Stock
                                        </span>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botón -->
                    <div class="px-4 pb-4 text-end bg-white mt-3">
                        <a href="{{ route('productos.detalle') }}"
                            class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-eye me-1"></i> Ver todos los productos
                        </a>
                    </div>

                </div>
            </div>
        </div>
    @endif


@endsection

@section('scripts')

    {{-- Toast de éxito --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    {{-- ===== Helper robusto para abrir modales (BS5 5.0/5.1/5.2+ y fallback BS4) ===== --}}
    <script>
        function openModalById(id) {
            const el = document.getElementById(id);
            if (!el) {
                console.error('No existe #' + id);
                return false;
            }

            // Bootstrap 5 (namespace window.bootstrap)
            if (window.bootstrap && typeof bootstrap.Modal === 'function') {
                try {
                    if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                        bootstrap.Modal.getOrCreateInstance(el).show(); // 5.2+
                        return true;
                    }
                    if (typeof bootstrap.Modal.getInstance === 'function') {
                        (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show(); // 5.0/5.1
                        return true;
                    }
                    // Si no existen los estáticos pero sí el constructor
                    new bootstrap.Modal(el).show();
                    return true;
                } catch (e) {
                    console.warn('Fallo API BS5, intentando fallback jQuery…', e);
                }
            }

            // Fallback Bootstrap 4 (plugin jQuery)
            if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                jQuery('#' + id).modal('show');
                return true;
            }

            console.error('No hay API válida de Bootstrap para abrir el modal.');
            return false;
        }
    </script>

    {{-- ===== Confirmaciones activar/desactivar ===== --}}
    <script>
        function confirmarDesactivacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El producto será marcado como inactivo",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, desactivar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-desactivar-' + id).submit();
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
                    document.getElementById('form-activar-' + id).submit();
                }
            });
        }
    </script>

    {{-- ===== Abrir modal de edición + cargar parcial por AJAX ===== --}}
    <script>
        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault();

            const id = $(this).data('id');
            const $content = $('#modalEditarContent');

            // Loader
            $content.html('<div class="p-4 text-center text-muted">Cargando…</div>');

            // Abre el modal (robusto a la versión)
            if (!openModalById('modalEditar')) return;

            // Carga la vista parcial del formulario
            $.get("{{ route('productos.edit-partial', ['id' => '__ID__']) }}".replace('__ID__', id))
                .done(function(html) {
                    $content.html(html);

                    if (window.feather) feather.replace();

                    // Re‑inicializar Select2 dentro del modal
                    const $modal = $('#modalEditar');
                    $modal.find('.select2-categorias-edit').each(function() {
                        const $el = $(this);
                        if ($el.data('select2')) return;
                        $el.select2({
                            width: '100%',
                            closeOnSelect: false,
                            allowClear: true,
                            dropdownParent: $modal
                        });
                    });

                    // Validación Bootstrap 5 en el form del modal (una sola vez)
                    const form = $content.find('#formEditarProducto')[0];
                    if (form && !form.dataset.bound) {
                        form.dataset.bound = '1';
                        form.addEventListener('submit', function(ev) {
                            if (!form.checkValidity()) {
                                ev.preventDefault();
                                ev.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        });
                    }
                })
                .fail(function(xhr) {
                    $content.html(
                        '<div class="p-4 text-danger text-center">Error cargando el formulario (' +
                        (xhr.status || '??') + ').</div>'
                    );
                });
        });
    </script>

    {{-- ===== Select2 en "Nuevo producto" ===== --}}
    <script>
        $(function() {
            const $sel = $('.select2-categorias');
            if ($sel.length) {
                $sel.select2({
                    width: '100%',
                    placeholder: $sel.data('placeholder') || 'Seleccionar categorías…',
                    allowClear: true,
                    closeOnSelect: false,
                    dropdownAutoWidth: true
                    // Si el select está dentro del modal de "Nuevo producto", podrías habilitar:
                    // dropdownParent: $('#nuevoProducto')
                });
            }
        });
    </script>

    {{-- ===== Select2 cuando se muestre cualquier modal (seguro contra re‑init) ===== --}}
    <script>
        $(document).on('shown.bs.modal', '.modal', function() {
            const $modal = $(this);
            $modal.find('.select2-categorias-edit').each(function() {
                const $el = $(this);
                if ($el.data('select2')) return;
                $el.select2({
                    width: '100%',
                    placeholder: $el.data('placeholder'),
                    allowClear: true,
                    closeOnSelect: false,
                    dropdownParent: $modal
                });
            });
        });
    </script>

    {{-- Mostrar modal de stock bajo si aplica --}}
    @if ($productosStockBajo->count() > 0)
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                openModalById('modalStockBajo');
            });
        </script>
    @endif

    {{-- ===== Paginación client‑side simple sobre las filas ya renderizadas ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rowsPerPage = 10;
            const table = document.querySelector('.table');
            if (!table) return;

            const tbody = table.querySelector('tbody');
            const rows = tbody ? tbody.querySelectorAll('tr') : [];
            const pageCount = Math.ceil(rows.length / rowsPerPage);
            const pagination = document.getElementById('pagination');
            if (!pagination) return;

            function showPage(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });
                updatePaginationButtons(page);
            }

            function updatePaginationButtons(page) {
                pagination.innerHTML = '';

                // Prev
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML =
                    `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (page > 1) showPage(page - 1);
                });
                pagination.appendChild(prevLi);

                // rango visible
                const maxVisiblePages = 5;
                let startPage, endPage;
                if (pageCount <= maxVisiblePages) {
                    startPage = 1;
                    endPage = pageCount;
                } else {
                    const before = Math.floor(maxVisiblePages / 2);
                    const after = Math.ceil(maxVisiblePages / 2) - 1;

                    if (page <= before) {
                        startPage = 1;
                        endPage = maxVisiblePages;
                    } else if (page + after >= pageCount) {
                        startPage = pageCount - maxVisiblePages + 1;
                        endPage = pageCount;
                    } else {
                        startPage = page - before;
                        endPage = page + after;
                    }
                }

                if (startPage > 1) {
                    const firstLi = document.createElement('li');
                    firstLi.className = 'page-item';
                    firstLi.innerHTML = `<a class="page-link" href="#">1</a>`;
                    firstLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        showPage(1);
                    });
                    pagination.appendChild(firstLi);

                    if (startPage > 2) {
                        const dots = document.createElement('li');
                        dots.className = 'page-item disabled';
                        dots.innerHTML = `<span class="page-link">...</span>`;
                        pagination.appendChild(dots);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const li = document.createElement('li');
                    li.className = `page-item ${i === page ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.addEventListener('click', (e) => {
                        e.preventDefault();
                        showPage(i);
                    });
                    pagination.appendChild(li);
                }

                if (endPage < pageCount) {
                    if (endPage < pageCount - 1) {
                        const dots = document.createElement('li');
                        dots.className = 'page-item disabled';
                        dots.innerHTML = `<span class="page-link">...</span>`;
                        pagination.appendChild(dots);
                    }
                    const lastLi = document.createElement('li');
                    lastLi.className = 'page-item';
                    lastLi.innerHTML = `<a class="page-link" href="#">${pageCount}</a>`;
                    lastLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        showPage(pageCount);
                    });
                    pagination.appendChild(lastLi);
                }

                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${page === pageCount ? 'disabled' : ''}`;
                nextLi.innerHTML =
                    `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
                nextLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (page < pageCount) showPage(page + 1);
                });
                pagination.appendChild(nextLi);
            }

            if (rows.length) showPage(1);
        });
    </script>

    {{-- ===== Validación Bootstrap 5 ===== --}}
    <script>
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>

@endsection
