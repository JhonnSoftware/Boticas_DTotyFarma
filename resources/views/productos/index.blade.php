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
    </style>

    <!-- Botón y panel colapsable -->

    <div class="container-fluid">

        <a href="{{ route('productos.detalle') }}" class="text-decoration-none">
            <div class="card border-right shadow-sm hover-shadow" style="transition: 0.2s; border-radius: 20px;">
                <div class="card-body">
                    <div class="d-flex d-lg-flex d-md-block align-items-center">
                        <div>
                            <div class="d-inline-flex align-items-center">
                                <h2 class="text-dark mb-1 font-weight-medium" style="font-size: 24px;">Ver Detalles</h2>
                                <span
                                    class="badge bg-warning font-12 text-dark font-weight-medium badge-pill ms-2 d-lg-block d-md-none">
                                    2 productos con stock bajo
                                </span>
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="entries-info">
                                Showing 10 entries
                            </div>

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
                                        <th class="border-0 font-14 font-weight-medium text-black">Presentacion</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Laboratorio</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Lote</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Cantidad</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Stock Minimo</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Dscto</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Caduca</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">P. Venta</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">P. Compra</th>
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
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->presentacion }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->laboratorio }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->lote }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->cantidad }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->stock_minimo }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->descuento }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->fecha_vencimiento }}
                                            </td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->precio_compra }}
                                            </td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $producto->precio_venta }}</td>
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
                                                <a href="#" class="text-primary me-2"><i data-feather="eye"></i></a>

                                                <a href="#" class="text-success" data-bs-toggle="modal"
                                                    data-bs-target="#editarProducto{{ $producto->id }}">
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

                                        <!-- Modal de edición -->
                                        <div class="modal fade" id="editarProducto{{ $producto->id }}" tabindex="-1"
                                            aria-labelledby="editarProductoLabel{{ $producto->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg"> <!-- Tamaño grande para que todo entre -->
                                                <div class="modal-content" style="border-radius: 20px;">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="editarProductoLabel{{ $producto->id }}">Editar Producto
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Cerrar"></button>
                                                    </div>

                                                    <form action="{{ route('productos.actualizar', $producto->id) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="row g-4">
                                                                <!-- Código -->
                                                                <div class="col-md-6">
                                                                    <label for="codigo{{ $producto->id }}"
                                                                        class="form-label">Código</label>
                                                                    <input type="text" class="form-control rounded-3"
                                                                        id="codigo{{ $producto->id }}" name="codigo"
                                                                        value="{{ $producto->codigo }}" required>
                                                                </div>

                                                                <!-- Descripción -->
                                                                <div class="col-md-6">
                                                                    <label for="descripcion{{ $producto->id }}"
                                                                        class="form-label">Descripción</label>
                                                                    <input type="text" class="form-control rounded-3"
                                                                        id="descripcion{{ $producto->id }}"
                                                                        name="descripcion"
                                                                        value="{{ $producto->descripcion }}" required>
                                                                </div>

                                                                <!-- Presentación -->
                                                                <div class="col-md-6">
                                                                    <label for="presentacion{{ $producto->id }}"
                                                                        class="form-label">Presentación</label>
                                                                    <input type="text" class="form-control rounded-3"
                                                                        id="presentacion{{ $producto->id }}"
                                                                        name="presentacion"
                                                                        value="{{ $producto->presentacion }}" required>
                                                                </div>

                                                                <!-- Laboratorio -->
                                                                <div class="col-md-6">
                                                                    <label for="laboratorio{{ $producto->id }}"
                                                                        class="form-label">Laboratorio</label>
                                                                    <input type="text" class="form-control rounded-3"
                                                                        id="laboratorio{{ $producto->id }}"
                                                                        name="laboratorio"
                                                                        value="{{ $producto->laboratorio }}" required>
                                                                </div>

                                                                <!-- Lote -->
                                                                <div class="col-md-4">
                                                                    <label for="lote{{ $producto->id }}"
                                                                        class="form-label">Lote</label>
                                                                    <input type="number" class="form-control rounded-3"
                                                                        id="lote{{ $producto->id }}" name="lote"
                                                                        value="{{ $producto->lote }}" required>
                                                                </div>

                                                                <!-- Cantidad -->
                                                                <div class="col-md-4">
                                                                    <label for="cantidad{{ $producto->id }}"
                                                                        class="form-label">Cantidad</label>
                                                                    <input type="number" class="form-control rounded-3"
                                                                        id="cantidad{{ $producto->id }}" name="cantidad"
                                                                        min="0" value="{{ $producto->cantidad }}"
                                                                        required>
                                                                </div>

                                                                <!-- Stock mínimo -->
                                                                <div class="col-md-4">
                                                                    <label for="stock_minimo{{ $producto->id }}"
                                                                        class="form-label">Stock mínimo</label>
                                                                    <input type="number" class="form-control rounded-3"
                                                                        id="stock_minimo{{ $producto->id }}"
                                                                        name="stock_minimo" min="0"
                                                                        value="{{ $producto->stock_minimo }}" required>
                                                                </div>

                                                                <!-- Descuento -->
                                                                <div class="col-md-6">
                                                                    <label for="descuento{{ $producto->id }}"
                                                                        class="form-label">Descuento (%)</label>
                                                                    <input type="number" step="0.01"
                                                                        class="form-control rounded-3"
                                                                        id="descuento{{ $producto->id }}"
                                                                        name="descuento" min="0"
                                                                        value="{{ $producto->descuento }}" required>
                                                                </div>

                                                                <!-- Fecha de vencimiento -->
                                                                <div class="col-md-6">
                                                                    <label for="fecha_vencimiento{{ $producto->id }}"
                                                                        class="form-label">Fecha de vencimiento</label>
                                                                    <input type="date" class="form-control rounded-3"
                                                                        id="fecha_vencimiento{{ $producto->id }}"
                                                                        name="fecha_vencimiento"
                                                                        value="{{ $producto->fecha_vencimiento }}"
                                                                        required>
                                                                </div>

                                                                <!-- Precio compra -->
                                                                <div class="col-md-6">
                                                                    <label for="precio_compra{{ $producto->id }}"
                                                                        class="form-label">Precio de compra (S/)</label>
                                                                    <input type="number" step="0.01"
                                                                        class="form-control rounded-3"
                                                                        id="precio_compra{{ $producto->id }}"
                                                                        name="precio_compra" min="0"
                                                                        value="{{ $producto->precio_compra }}" required>
                                                                </div>

                                                                <!-- Precio venta -->
                                                                <div class="col-md-6">
                                                                    <label for="precio_venta{{ $producto->id }}"
                                                                        class="form-label">Precio de venta (S/)</label>
                                                                    <input type="number" step="0.01"
                                                                        class="form-control rounded-3"
                                                                        id="precio_venta{{ $producto->id }}"
                                                                        name="precio_venta" min="0"
                                                                        value="{{ $producto->precio_venta }}" required>
                                                                </div>

                                                                <!-- Proveedor -->
                                                                <div class="col-md-6">
                                                                    <label for="id_proveedor{{ $producto->id }}"
                                                                        class="form-label">Proveedor</label>
                                                                    <select class="form-select rounded-3"
                                                                        id="id_proveedor{{ $producto->id }}"
                                                                        name="id_proveedor" required>
                                                                        <option value="" disabled>Seleccione
                                                                            proveedor</option>
                                                                        @foreach ($proveedores as $proveedor)
                                                                            <option value="{{ $proveedor->id }}"
                                                                                {{ $producto->id_proveedor == $proveedor->id ? 'selected' : '' }}>
                                                                                {{ $proveedor->nombre }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <!-- Categoría -->
                                                                <div class="col-md-6">
                                                                    <label for="id_categoria{{ $producto->id }}"
                                                                        class="form-label">Categoría</label>
                                                                    <select class="form-select rounded-3"
                                                                        id="id_categoria{{ $producto->id }}"
                                                                        name="id_categoria" required>
                                                                        <option value="" disabled>Seleccione
                                                                            categoría</option>
                                                                        @foreach ($categorias as $categoria)
                                                                            <option value="{{ $categoria->id }}"
                                                                                {{ $producto->id_categoria == $categoria->id ? 'selected' : '' }}>
                                                                                {{ $categoria->nombre }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <!-- Foto -->
                                                                <div class="col-12">
                                                                    <label for="foto{{ $producto->id }}"
                                                                        class="form-label">Foto del producto</label>
                                                                    <input type="file" class="form-control rounded-3"
                                                                        id="foto{{ $producto->id }}" name="foto"
                                                                        accept=".jpg,.jpeg,.png,.webp">
                                                                    @if ($producto->foto)
                                                                        <small class="text-muted">Actual:
                                                                            {{ $producto->foto }}</small>
                                                                    @endif
                                                                </div>

                                                                <!-- Estado oculto -->
                                                                <input type="hidden" name="estado" value="Activo">
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Guardar
                                                                Cambios</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>

                            </table>
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
                            <!-- Código -->
                            <div class="col-md-6">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" class="form-control rounded-3" id="codigo" name="codigo"
                                    value="{{ $nuevoCodigo }}" readonly>
                            </div>

                            <!-- Descripción -->
                            <div class="col-md-6">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <input type="text" class="form-control rounded-3" id="descripcion" name="descripcion"
                                    required>
                                <div class="invalid-feedback">
                                    Por favor ingrese la descripción del producto.
                                </div>
                            </div>

                            <!-- Presentación -->
                            <div class="col-md-6">
                                <label for="presentacion" class="form-label">Presentación</label>
                                <input type="text" class="form-control rounded-3" id="presentacion"
                                    name="presentacion" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese la presentacion del producto.
                                </div>
                            </div>

                            <!-- Laboratorio -->
                            <div class="col-md-6">
                                <label for="laboratorio" class="form-label">Laboratorio</label>
                                <input type="text" class="form-control rounded-3" id="laboratorio" name="laboratorio"
                                    required>
                                <div class="invalid-feedback">
                                    Por favor ingrese la laboratorio del producto.
                                </div>
                            </div>

                            <!-- Lote -->
                            <div class="col-md-4">
                                <label for="lote" class="form-label">Lote</label>
                                <input type="number" class="form-control rounded-3" id="lote" name="lote"
                                    required>
                                <div class="invalid-feedback">
                                    Por favor ingrese el lote del producto.
                                </div>
                            </div>

                            <!-- Cantidad -->
                            <div class="col-md-4">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <input type="number" class="form-control rounded-3" id="cantidad" name="cantidad"
                                    min="0" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese la cantidad del producto.
                                </div>
                            </div>

                            <!-- Stock mínimo -->
                            <div class="col-md-4">
                                <label for="stock_minimo" class="form-label">Stock mínimo</label>
                                <input type="number" class="form-control rounded-3" id="stock_minimo"
                                    name="stock_minimo" min="0" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese el stock minimo del producto.
                                </div>
                            </div>

                            <!-- Descuento -->
                            <div class="col-md-6">
                                <label for="descuento" class="form-label">Descuento (%)</label>
                                <input type="number" step="0.01" class="form-control rounded-3" id="descuento"
                                    name="descuento" min="0" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese el descuento del producto.
                                </div>
                            </div>

                            <!-- Fecha de vencimiento -->
                            <div class="col-md-6">
                                <label for="fecha_vencimiento" class="form-label">Fecha de vencimiento</label>
                                <input type="date" class="form-control rounded-3" id="fecha_vencimiento"
                                    name="fecha_vencimiento" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese la fecha de vencimiento del producto.
                                </div>
                            </div>

                            <!-- Precio compra -->
                            <div class="col-md-6">
                                <label for="precio_compra" class="form-label">Precio de compra (S/)</label>
                                <input type="number" step="0.01" class="form-control rounded-3" id="precio_compra"
                                    name="precio_compra" min="0" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese el precio de compra del producto.
                                </div>
                            </div>

                            <!-- Precio venta -->
                            <div class="col-md-6">
                                <label for="precio_venta" class="form-label">Precio de venta (S/)</label>
                                <input type="number" step="0.01" class="form-control rounded-3" id="precio_venta"
                                    name="precio_venta" min="0" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese el precio de venta del producto.
                                </div>
                            </div>

                            <!-- Proveedor -->
                            <div class="col-md-6">
                                <label for="id_proveedor" class="form-label">Proveedor</label>
                                <select class="form-select rounded-3" id="id_proveedor" name="id_proveedor" required>
                                    <option value="" disabled selected>Seleccione proveedor</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Por favor ingrese el proveedor del producto.
                                </div>
                            </div>

                            <!-- Categoría -->
                            <div class="col-md-6">
                                <label for="id_categoria" class="form-label">Categoría</label>
                                <select class="form-select rounded-3" id="id_categoria" name="id_categoria" required>
                                    <option value="" disabled selected>Seleccione categoría</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Por favor ingrese la categoria del producto.
                                </div>
                            </div>

                            <!-- Imagen -->
                            <div class="col-12">
                                <label for="foto" class="form-label">Foto del producto</label>
                                <input type="file" class="form-control rounded-3" id="foto" name="foto"
                                    accept=".jpg,.jpeg,.png,.webp">
                            </div>

                            <!-- Estado -->
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

    @if (session('success'))
        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endif

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

    <script>
        $(document).ready(function() {
            $('#inputBusqueda').on('keyup', function() {
                var buscar = $(this).val();

                $.ajax({
                    url: "{{ route('productos.buscar') }}",
                    type: "GET",
                    data: {
                        buscar: buscar
                    },
                    success: function(data) {
                        $('#tablaProductos').html(data);
                        feather.replace();
                    }
                });
            });
        });
    </script>

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

    @if ($productosStockBajo->count() > 0)
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const modal = new bootstrap.Modal(document.getElementById('modalStockBajo'));
                modal.show();
            });
        </script>
    @endif


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rowsPerPage = 10;
            const table = document.querySelector('.table');
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');
            const pageCount = Math.ceil(rows.length / rowsPerPage);
            const pagination = document.getElementById('pagination');

            let currentPage = 1;

            // Función para mostrar las filas de la página actual
            function showPage(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });

                updatePaginationButtons(page);
            }

            // Función para actualizar los botones del paginador
            function updatePaginationButtons(page) {
                pagination.innerHTML = '';
                currentPage = page;

                // Botón Anterior
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML =
                    `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (page > 1) showPage(page - 1);
                });
                pagination.appendChild(prevLi);

                // Botones de páginas
                const maxVisiblePages = 5; // Máximo de botones de página a mostrar
                let startPage, endPage;

                if (pageCount <= maxVisiblePages) {
                    startPage = 1;
                    endPage = pageCount;
                } else {
                    const maxPagesBeforeCurrent = Math.floor(maxVisiblePages / 2);
                    const maxPagesAfterCurrent = Math.ceil(maxVisiblePages / 2) - 1;

                    if (page <= maxPagesBeforeCurrent) {
                        startPage = 1;
                        endPage = maxVisiblePages;
                    } else if (page + maxPagesAfterCurrent >= pageCount) {
                        startPage = pageCount - maxVisiblePages + 1;
                        endPage = pageCount;
                    } else {
                        startPage = page - maxPagesBeforeCurrent;
                        endPage = page + maxPagesAfterCurrent;
                    }
                }

                // Botón primera página si es necesario
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
                        const dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<span class="page-link">...</span>`;
                        pagination.appendChild(dotsLi);
                    }
                }

                // Botones de páginas numeradas
                for (let i = startPage; i <= endPage; i++) {
                    const pageLi = document.createElement('li');
                    pageLi.className = `page-item ${i === page ? 'active' : ''}`;
                    pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    pageLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        showPage(i);
                    });
                    pagination.appendChild(pageLi);
                }

                // Botón última página si es necesario
                if (endPage < pageCount) {
                    if (endPage < pageCount - 1) {
                        const dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<span class="page-link">...</span>`;
                        pagination.appendChild(dotsLi);
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

                // Botón Siguiente
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

            // Inicializar paginación
            showPage(1);
        });
    </script>

    <script>
        // Bootstrap 5: Validación personalizada
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
