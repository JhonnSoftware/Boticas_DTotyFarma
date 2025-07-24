@extends('layouts.plantilla')

@section('titulo', 'Registrar Compra')

@section('content')

    <style>
        .table-primary {
            background-color: #0A7ABF !important;
            color: #fff;
        }

        .modal-header.bg-primary {
            background-color: #0A7ABF !important;
        }

        .modal-footer .btn-outline-danger {
            border-color: #BF4A4A;
            color: #BF4A4A;
        }

        .modal-footer .btn-outline-danger:hover {
            background-color: #BF4A4A;
            color: #fff;
        }
    </style>

    <div class="container-fluid py-4">
        <form action="{{ route('compras.store') }}" method="POST" id="formCompra" class="needs-validation" novalidate
            enctype="multipart/form-data">
            @csrf

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header"
                    style="background-color: #0A7ABF; color: white; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <h5 class="mb-0"><i class="fas fa-cart-plus me-2"></i> Registrar Nueva Compra</h5>
                </div>

                <div class="card-body bg-white rounded-bottom-4 px-4 py-3" style="background-color: #F2F2F2;">
                    <!-- Datos principales -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary">Proveedor</label>
                            <select name="id_proveedor" class="form-select shadow-sm border-0" required>
                                <option value="">Seleccione proveedor</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary">Documento</label>
                            <select name="id_documento" class="form-select shadow-sm border-0" required>
                                <option value="">Seleccione documento</option>
                                @foreach ($documentos as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary">Tipo de Pago</label>
                            <select name="id_pago" class="form-select shadow-sm border-0" required>
                                <option value="">Seleccione pago</option>
                                @foreach ($tipopagos as $pago)
                                    <option value="{{ $pago->id }}">{{ $pago->nombre }}</option>
                                @endforeach
                            </select>
                        </div>


                    </div>

                    <div class="row mb-3 align-items-end">
                        <!-- Botón para abrir modal -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">Agregar Producto</label><br>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#modalProductos">
                                <i class="fas fa-search me-1"></i> Buscar Producto
                            </button>
                        </div>

                        <!-- Campo archivo factura -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">Factura del proveedor (PDF/JPG/PNG)</label>
                            <input type="file" name="archivo_factura" class="form-control shadow-sm border-0"
                                accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>



                    <!-- Tabla productos -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle" id="tablaCompra">
                            <thead style="background-color: #25A6D9; color: white;">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Totales -->
                    <div class="row justify-content-end mb-4">
                        <div class="col-md-4">
                            <div class="mb-2"><strong>Subtotal:</strong> <span class="float-end" id="subtotal">S/
                                    0.00</span></div>
                            <div class="mb-2"><strong>IGV (18%):</strong> <span class="float-end" id="igv">S/
                                    0.00</span></div>
                            <div class="fs-5 fw-bold" style="color: #6EBF49;">
                                <strong>Total:</strong> <span class="float-end" id="total">S/ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="text-end">
                        <button type="submit" class="btn" style="background-color: #6EBF49; color: white;">
                            <i class="fas fa-save me-1"></i> Guardar
                        </button>
                        <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de productos -->
    <div class="modal fade" id="modalProductos" tabindex="-1" aria-labelledby="modalProductosLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <!-- Encabezado -->
                <div class="modal-header rounded-top-4" style="background-color: #0A7ABF; color: white;">
                    <h5 class="modal-title fw-bold" id="modalProductosLabel">
                        <i class="fas fa-boxes me-2"></i> Seleccionar Producto
                    </h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body bg-light rounded-bottom-4">
                    <div class="mb-3">
                        <input type="text" id="buscadorProducto" class="form-control shadow-sm rounded-3"
                            placeholder="🔍 Buscar producto por nombre...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center align-middle rounded-3 overflow-hidden">
                            <thead style="background-color: #25A6D9; color: white;">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Precio Compra</th>
                                    <th>Stock</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productos as $producto)
                                    <tr>
                                        <td class="fw-semibold">{{ $producto->descripcion }}</td>
                                        <td>S/ {{ number_format($producto->precio_compra, 2) }}</td>
                                        <td><span class="badge bg-success">{{ $producto->cantidad }}</span></td>
                                        <td>
                                            <button type="button" class="btn btn-sm text-white seleccionar-producto"
                                                style="background-color: #6EBF49;" data-id="{{ $producto->id }}"
                                                data-nombre="{{ $producto->descripcion }}"
                                                data-precio="{{ $producto->precio_compra }}">
                                                <i class="fas fa-plus"></i> Agregar
                                            </button>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pie -->
                <div class="modal-footer bg-white border-top-0">
                    <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
        // Elementos base
        const tabla = document.querySelector("#tablaCompra tbody");

        // Calcular totales
        function calcularTotales() {
            let total = 0;
            document.querySelectorAll(".subtotal").forEach(span => {
                total += parseFloat(span.textContent.replace("S/", "")) || 0;
            });
            const igv = total * 0.18;
            const subtotal = total - igv;
            document.getElementById("subtotal").textContent = `S/ ${subtotal.toFixed(2)}`;
            document.getElementById("igv").textContent = `S/ ${igv.toFixed(2)}`;
            document.getElementById("total").textContent = `S/ ${total.toFixed(2)}`;
        }

        // Escucha eventos generales
        document.addEventListener("click", function(e) {
            // Agregar producto desde el modal
            const btn = e.target.closest(".seleccionar-producto");
            if (btn) {
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre;
                const precio = parseFloat(btn.dataset.precio || 0).toFixed(2);

                if (!id || document.querySelector(`#tablaCompra tbody tr[data-id="${id}"]`)) return;

                const fila = `
                <tr data-id="${id}">
                    <td>
                        <input type="hidden" name="productos[${id}][id_producto]" value="${id}">
                        ${nombre}
                    </td>
                    <td>
                        <input type="number" name="productos[${id}][cantidad]" class="form-control cantidad" value="1" min="1">
                    </td>
                    <td>
                        <input type="number" name="productos[${id}][precio_unitario]" class="form-control precio" value="${precio}" step="0.01">
                    </td>
                    <td><span class="subtotal">S/ ${precio}</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger eliminar-producto">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>`;
                tabla.insertAdjacentHTML("beforeend", fila);
                calcularTotales();

                // Cierra el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalProductos"));
                modal.hide();
                return;
            }

            // Eliminar producto de la tabla
            const botonEliminar = e.target.closest(".eliminar-producto");
            if (botonEliminar) {
                const fila = botonEliminar.closest("tr");
                if (fila) {
                    fila.remove();
                    calcularTotales();
                }
            }
        });

        // Detectar cambios en cantidad o precio
        document.addEventListener("input", function(e) {
            if (e.target.classList.contains("cantidad") || e.target.classList.contains("precio")) {
                const fila = e.target.closest("tr");
                const cantidad = parseFloat(fila.querySelector(".cantidad").value) || 0;
                const precio = parseFloat(fila.querySelector(".precio").value) || 0;
                const subtotal = cantidad * precio;
                fila.querySelector(".subtotal").textContent = `S/ ${subtotal.toFixed(2)}`;
                calcularTotales();
            }
        });

        // Filtro de búsqueda en tiempo real en el modal
        document.getElementById("buscadorProducto").addEventListener("input", function() {
            const filtro = this.value.toLowerCase();
            const filas = document.querySelectorAll("#modalProductos tbody tr");

            filas.forEach(fila => {
                const nombreProducto = fila.children[0].textContent.toLowerCase();
                fila.style.display = nombreProducto.includes(filtro) ? "" : "none";
            });
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

    @if (session('success'))
        <script>
            Swal.fire({
                title: '¡Compra registrada!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Aceptar',
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif


@endsection
