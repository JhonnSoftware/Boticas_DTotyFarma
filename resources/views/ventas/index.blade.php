@extends('layouts.plantilla')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4">
            <!-- Panel Registro de Venta -->
            <div class="col-md-7">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header text-white fw-bold fs-5 rounded-top-4 d-flex align-items-center"
                        style="background: linear-gradient(135deg, #0A7ABF, #25A6D9);">
                        <i class="fas fa-cash-register me-2 fs-4"></i> Registro de Venta
                    </div>
                    <div class="card-body bg-white rounded-bottom-4 px-4">
                        <form id="formVenta" action="{{ route('ventas.store') }}" method="POST" class="needs-validation"
                            novalidate>
                            @csrf

                            <!-- Cliente -->
                            <div class="mb-3">
                                <label class="form-label text-primary fw-bold">Cliente <span
                                        class="text-danger">*</span></label>
                                <select name="id_cliente" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">
                                            {{ $cliente->nombre }} {{ $cliente->apellidos }} - DNI: {{ $cliente->dni }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione un cliente.
                                </div>
                            </div>

                            <!-- Serie, Número y Documento -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label text-primary fw-bold">Serie</label>
                                    <input type="text" class="form-control bg-light" value="TI001" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-primary fw-bold">Número</label>
                                    <input type="text" class="form-control bg-light" value="0000006" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-primary fw-bold">Comprobante <span
                                            class="text-danger">*</span></label>
                                    <select name="id_documento" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        @foreach ($documentos as $documento)
                                            <option value="{{ $documento->id }}">{{ $documento->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Tabla de productos -->
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered shadow-sm rounded text-center align-middle"
                                    id="tablaVenta">
                                    <thead style="background-color: #25A6D9; color: white;">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cant.</th>
                                            <th>Precio</th>
                                            <th>Desc.</th>
                                            <th>Subtotal</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <!-- Totales -->
                            <div class="mb-2"><strong>Subtotal:</strong> <span class="float-end" id="subtotal">S/
                                    0.00</span></div>
                            <div class="mb-2"><strong>IVA 0%:</strong> <span class="float-end" id="igv">S/
                                    0.00</span></div>
                            <div class="mb-3 fs-5 fw-bold text-success">
                                <strong>Total:</strong> <span class="float-end" id="total">S/ 0.00</span>
                            </div>

                            <!-- Pago -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-primary">Tipo de pago <span
                                            class="text-danger">*</span></label>
                                    <select name="id_pago" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        @foreach ($pagos as $pago)
                                            <option value="{{ $pago->id }}">{{ $pago->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-primary">Cant. Pagado</label>
                                    <input type="number" step="0.01" class="form-control" id="pagado"
                                        placeholder="S/">
                                </div>
                            </div>

                            <!-- Cambio -->
                            <div class="mb-3 text-danger fw-bold">
                                <label class="form-label">Cambio:</label>
                                <span class="float-end" id="cambio">S/ 0.00</span>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-end gap-3">
                                <button type="submit" class="btn text-white px-4" style="background-color: #6EBF49;">
                                    <i class="fas fa-save me-1"></i> Guardar
                                </button>
                                <button type="reset" class="btn btn-outline-danger px-4">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Panel Productos -->
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header text-white fw-bold fs-5 rounded-top-4 d-flex align-items-center"
                        style="background: linear-gradient(135deg, #6EBF49, #8BBF65);">
                        <i class="fas fa-box-open me-2 fs-4"></i> Lista de Productos
                    </div>
                    <div class="card-body bg-white rounded-bottom-4 px-4">
                        <input type="text" id="buscarProducto" class="form-control mb-3"
                            placeholder="Buscar producto...">

                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle" id="tablaProductos">
                                <thead class="table-light">
                                    <tr>
                                        <th>Opción</th>
                                        <th>Nombre</th>
                                        <th>Presentación</th>
                                        <th>Stock</th>
                                        <th>Imagen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productos as $producto)
                                        <tr data-nombre="{{ strtolower($producto->descripcion) }}"
                                            data-presentacion="{{ strtolower($producto->presentacion) }}"
                                            data-laboratorio="{{ strtolower($producto->laboratorio) }}"
                                            data-categoria="{{ strtolower($producto->categoria ?? '') }}">
                                            <td>
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-sm agregar-producto"
                                                    data-id="{{ $producto->id }}"
                                                    data-nombre="{{ $producto->descripcion }}"
                                                    data-precio="{{ $producto->precio_venta }}">
                                                    <i data-feather="plus-circle"></i>
                                                </button>
                                            </td>
                                            <td class="info-producto">{{ $producto->descripcion }}</td>
                                            <td>{{ $producto->presentacion }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $producto->cantidad <= 10 ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $producto->cantidad }}
                                                </span>
                                            </td>
                                            <td>
                                                <img src="{{ url($producto->foto) }}" alt="imagen"
                                                    class="rounded-circle shadow-sm" width="40" height="40">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Cerrar'
            });
        </script>
    @endif

    @if (session('imprimir'))
        <script>
            Swal.fire({
                title: '¿Deseas imprimir el voucher?',
                text: 'La venta se registró correctamente.',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Sí, imprimir',
                cancelButtonText: 'No, gracias'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open("{{ session('voucher_url') }}", '_blank');
                }
            });
        </script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Búsqueda en tiempo real
            const buscador = document.getElementById("buscarProducto");
            buscador.addEventListener("keyup", function() {
                let filtro = this.value.toLowerCase();
                let filas = document.querySelectorAll("#tablaProductos tbody tr");

                filas.forEach(fila => {
                    let texto = fila.querySelector(".info-producto")?.innerText.toLowerCase() || "";
                    fila.style.display = texto.includes(filtro) ? "" : "none";
                });
            });

            // Agregar producto a la tabla de venta
            document.addEventListener("click", function(e) {
                if (e.target.closest(".agregar-producto")) {
                    const btn = e.target.closest(".agregar-producto");
                    const id = btn.dataset.id;
                    const nombre = btn.dataset.nombre;
                    const precio = parseFloat(btn.dataset.precio).toFixed(2);

                    const tabla = document.querySelector("#tablaVenta tbody");
                    const filaExistente = tabla.querySelector(`tr[data-id='${id}']`);

                    if (filaExistente) {
                        // Ya existe el producto: sumar cantidad
                        const cantidadInput = filaExistente.querySelector(".cantidad");
                        cantidadInput.value = parseInt(cantidadInput.value) + 1;
                    } else {
                        // Agregar nueva fila
                        const fila = `
                <tr data-id="${id}">
                    <td>
                        <input type="hidden" name="productos[]" value="${id}">
                        ${nombre}
                    </td>
                    <td>
                        <input type="number" name="cantidades[]" class="form-control cantidad" value="1" min="1">
                    </td>
                    <td>
                        <input type="number" name="precios[]" class="form-control precio" value="${precio}" step="0.01">
                    </td>
                    <td>
                        <input type="number" name="descuentos[]" class="form-control descuento" value="0" step="0.01">
                    </td>
                    <td>
                        <span class="subtotal">${precio}</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm eliminar-producto">
                            <i data-feather="trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                        tabla.insertAdjacentHTML("beforeend", fila);
                        feather.replace();
                    }

                    // Desactivar el botón agregar para evitar duplicados
                    btn.disabled = true;

                    calcularTotales();
                }
            });


            // Eliminar producto de tabla
            document.addEventListener("click", function(e) {
                if (e.target.closest(".eliminar-producto")) {
                    e.target.closest("tr").remove();
                    calcularTotales();
                }
            });

            // Actualizar totales al modificar cantidad/precio/descuento
            document.addEventListener("input", function(e) {
                if (e.target.matches(".cantidad, .precio, .descuento")) {
                    const fila = e.target.closest("tr");
                    const cantidad = parseFloat(fila.querySelector(".cantidad").value) || 0;
                    const precio = parseFloat(fila.querySelector(".precio").value) || 0;
                    const descuento = parseFloat(fila.querySelector(".descuento").value) || 0;
                    const subtotal = (cantidad * precio) - descuento;
                    fila.querySelector(".subtotal").textContent = subtotal.toFixed(2);
                    calcularTotales();
                }
            });

            function calcularTotales() {
                let total = 0;
                document.querySelectorAll(".subtotal").forEach(s => {
                    total += parseFloat(s.textContent) || 0;
                });

                document.getElementById("subtotal").textContent = `S/ ${total.toFixed(2)}`;
                document.getElementById("igv").textContent = `S/ 0.00`;
                document.getElementById("total").textContent = `S/ ${total.toFixed(2)}`;
            }

            // Renderiza íconos feather al cargar la vista por primera vez
            feather.replace();
        });
    </script>

    <script>
        // Validar antes de enviar: que haya al menos un producto en la tabla
        document.getElementById("formVenta").addEventListener("submit", function(e) {
            const filas = document.querySelectorAll("#tablaVenta tbody tr");
            if (filas.length === 0) {
                e.preventDefault(); // Evita el envío del formulario
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Debes agregar al menos un producto a la venta.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Aceptar'
                });
            }
        });

        // Calcular cambio automáticamente cuando se modifique el campo "Cant. pagado"
        document.getElementById("pagado").addEventListener("input", function() {
            const totalTexto = document.getElementById("total").textContent.replace("S/", "").trim();
            const total = parseFloat(totalTexto) || 0;
            const pagado = parseFloat(this.value) || 0;
            const cambio = pagado - total;

            document.getElementById("cambio").textContent = `S/ ${cambio >= 0 ? cambio.toFixed(2) : '0.00'}`;
        });

        document.addEventListener("click", function(e) {
            if (e.target.closest(".eliminar-producto")) {
                const fila = e.target.closest("tr");
                const id = fila.getAttribute("data-id");

                // Rehabilitar botón de agregar
                const botonAgregar = document.querySelector(`.agregar-producto[data-id='${id}']`);
                if (botonAgregar) {
                    botonAgregar.disabled = false;
                }

                fila.remove();
                calcularTotales();
            }
        });
    </script>

    <script>
        (() => {
            'use strict';

            const form = document.getElementById('formVenta');

            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Validación estándar de Bootstrap
                if (!form.checkValidity()) {
                    isValid = false;
                }

                // Validar que descuento ≤ precio por fila
                document.querySelectorAll("#tablaVenta tbody tr").forEach(fila => {
                    const precioInput = fila.querySelector(".precio");
                    const descuentoInput = fila.querySelector(".descuento");

                    const precio = parseFloat(precioInput.value) || 0;
                    const descuento = parseFloat(descuentoInput.value) || 0;

                    if (descuento > precio) {
                        isValid = false;
                        descuentoInput.classList.add("is-invalid");
                        if (!descuentoInput.nextElementSibling || !descuentoInput.nextElementSibling
                            .classList.contains('invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'El descuento no puede ser mayor que el precio.';
                            descuentoInput.parentNode.appendChild(feedback);
                        }
                    } else {
                        descuentoInput.classList.remove("is-invalid");
                        const feedback = descuentoInput.parentNode.querySelector('.invalid-feedback');
                        if (feedback) feedback.remove();
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add('was-validated');
                }
            }, false);
        })();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("buscarProducto");
            const filas = document.querySelectorAll("#tablaProductos tbody tr");

            input.addEventListener("keyup", function() {
                const filtro = this.value.toLowerCase();

                filas.forEach(fila => {
                    const nombre = fila.dataset.nombre || "";
                    const presentacion = fila.dataset.presentacion || "";
                    const laboratorio = fila.dataset.laboratorio || "";
                    const categoria = fila.dataset.categoria || "";

                    const coincide = nombre.includes(filtro) ||
                        presentacion.includes(filtro) ||
                        laboratorio.includes(filtro) ||
                        categoria.includes(filtro);

                    fila.style.display = coincide ? "" : "none";
                });
            });
        });
    </script>
@endsection
