@extends('layouts.plantilla')

@section('content')
    <style>
        <style>tr.stock-zero {
            background: #fdecea !important;
        }

        tr.stock-zero td {
            color: #e81328;
        }

        tr.stock-zero img {
            filter: grayscale(100%);
            opacity: .8;
        }
    </style>


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
                                            <th>Presentación</th>
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
                                        @php
                                            $totalUnidades = (int) ($producto->cantidad ?? 0);

                                            $upb = $producto->unidades_por_blister
                                                ? (int) $producto->unidades_por_blister
                                                : null; // unidades x blister
                                            $uxc = $producto->unidades_por_caja
                                                ? (int) $producto->unidades_por_caja
                                                : null; // unidades x caja

                                            // Cajas disponibles y unidades sueltas SIN romper cajas
                                            if ($uxc && $uxc > 0) {
                                                $cajas = intdiv($totalUnidades, $uxc);
                                                $sueltas = $totalUnidades % $uxc;
                                            } else {
                                                $cajas = null; // si no hay ratio de caja, no mostramos cajas
                                                $sueltas = $totalUnidades; // todo es suelto
                                            }

                                            // Blísteres disponibles SOLO desde sueltas (no “romper” cajas)
                                            if ($upb && $upb > 0) {
                                                $blisters = intdiv($sueltas, $upb);
                                            } else {
                                                $blisters = null; // no maneja blister
                                            }

                                            $dispU = $sueltas > 0;
                                            $dispB = !is_null($blisters) && $blisters > 0;
                                            $dispC = !is_null($cajas) && $cajas > 0;
                                            $sinStockTotal = !$dispU && !$dispB && !$dispC;
                                        @endphp


                                        <tr class="{{ $totalUnidades == 0 ? 'stock-zero' : '' }}"
                                            data-nombre="{{ strtolower($producto->descripcion) }}"
                                            data-presentacion="{{ strtolower($producto->presentacion) }}"
                                            data-laboratorio="{{ strtolower($producto->laboratorio) }}"
                                            data-categoria="{{ strtolower($producto->categorias->pluck('nombre')->implode(', ') ?? '') }}">

                                            <td>
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-sm agregar-producto"
                                                    data-id="{{ $producto->id }}"
                                                    data-nombre="{{ $producto->descripcion }}"
                                                    data-precio-u="{{ $producto->precio_venta ?? '' }}"
                                                    data-precio-b="{{ $producto->precio_venta_blister ?? '' }}"
                                                    data-precio-c="{{ $producto->precio_venta_caja ?? '' }}"
                                                    data-descpct-u="{{ $producto->descuento ?? 0 }}"
                                                    data-descpct-b="{{ $producto->descuento_blister ?? 0 }}"
                                                    data-descpct-c="{{ $producto->descuento_caja ?? 0 }}"
                                                    {{-- ⬅️ AQUÍ el cambio: unidades totales, no sueltas --}}
                                                    data-stock-u="{{ (int) ($producto->cantidad ?? 0) }}"
                                                    {{-- Blíster y caja puedes dejarlos como los tenías --}} data-stock-b="{{ $dispB ? $blisters : '' }}"
                                                    data-stock-c="{{ $dispC ? $cajas : '' }}"
                                                    {{ $sinStockTotal ? 'disabled' : '' }}>
                                                    <i data-feather="plus-circle"></i>
                                                </button>

                                            </td>

                                            <td class="info-producto">{{ $producto->descripcion }}</td>
                                            <td>{{ $producto->presentacion }}</td>

                                            <td>
                                                {{-- Mostrar “cajas + sueltas” para que sea claro --}}
                                                @if ($uxc)
                                                    <div class="small">
                                                        <span
                                                            class="badge {{ $cajas > 0 ? 'bg-success' : 'bg-secondary' }}">Cj:
                                                            {{ $cajas }}</span>
                                                        <span
                                                            class="badge {{ $sueltas > 0 ? 'bg-success' : 'bg-danger' }}">U:
                                                            {{ $sueltas }}</span>
                                                        @if (!is_null($blisters))
                                                            <span
                                                                class="badge {{ $blisters > 0 ? 'bg-success' : 'bg-secondary' }}">Bl:
                                                                {{ $blisters }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="small">
                                                        <span
                                                            class="badge {{ $sueltas > 0 ? 'bg-success' : 'bg-danger' }}">U:
                                                            {{ $sueltas }}</span>
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <img src="{{ url($producto->foto) }}" alt="imagen" width="40"
                                                    height="40"
                                                    onerror="this.src='{{ asset('imagenes/producto_defecto.jpg') }}'">
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

            // ===== Helpers por presentación =====
            function getPrecio(btn, pres) {
                if (pres === 'unidad') return parseFloat(btn.dataset.precioU || '0') || 0;
                if (pres === 'blister') return parseFloat(btn.dataset.precioB || '0') || 0;
                if (pres === 'caja') return parseFloat(btn.dataset.precioC || '0') || 0;
                return 0;
            }

            function getDescPct(btn, pres) {
                if (pres === 'unidad') return parseFloat(btn.dataset.descpctU || '0') || 0;
                if (pres === 'blister') return parseFloat(btn.dataset.descpctB || '0') || 0;
                if (pres === 'caja') return parseFloat(btn.dataset.descpctC || '0') || 0;
                return 0;
            }

            function getStock(btn, pres) {
                if (pres === 'unidad') return parseInt(btn.dataset.stockU || '0', 10) || 0;
                if (pres === 'blister') return parseInt(btn.dataset.stockB || '0', 10) || 0;
                if (pres === 'caja') return parseInt(btn.dataset.stockC || '0', 10) || 0;
                return 0;
            }

            function primeraPresentacionDisponible(btn) {
                if (getStock(btn, 'unidad') > 0) return 'unidad';
                if (getStock(btn, 'blister') > 0) return 'blister';
                if (getStock(btn, 'caja') > 0) return 'caja';
                return 'unidad';
            }

            // ===== Agregar producto a la venta =====
            document.addEventListener("click", function(e) {
                if (!e.target.closest(".agregar-producto")) return;

                const btn = e.target.closest(".agregar-producto");
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre;
                const tabla = document.querySelector("#tablaVenta tbody");
                const filaExistente = tabla.querySelector(`tr[data-id='${id}']`);

                const allZero = getStock(btn, 'unidad') === 0 && getStock(btn, 'blister') === 0 && getStock(
                    btn, 'caja') === 0;
                if (allZero) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sin stock',
                        text: 'Este producto está agotado en todas las presentaciones.'
                    });
                    return;
                }

                // Si ya está, solo aumenta cantidad respetando stock de la presentación seleccionada
                if (filaExistente) {
                    const cantidadInput = filaExistente.querySelector(".cantidad");
                    const pres = filaExistente.querySelector(".presentacion").value;
                    const stock = getStock(btn, pres);
                    const nueva = (parseInt(cantidadInput.value || "0", 10) + 1);
                    if (nueva > stock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock insuficiente',
                            text: `Solo hay ${stock} en stock (${pres}).`
                        });
                        return;
                    }
                    cantidadInput.value = nueva;
                    cantidadInput.dispatchEvent(new Event("input", {
                        bubbles: true
                    }));
                    return;
                }

                // Nueva fila
                const presDefault = primeraPresentacionDisponible(btn);
                const precioUnit = getPrecio(btn, presDefault);
                const descPct = getDescPct(btn, presDefault);
                const cantidadIni = 1;
                const descLinea = cantidadIni * precioUnit * (descPct / 100);
                const subtotal = (cantidadIni * precioUnit) - descLinea;

                const opts = [];
                opts.push(
                    `<option value="unidad" ${getStock(btn,'unidad')>0?'':'disabled'} ${presDefault==='unidad'?'selected':''}>Unidad</option>`
                );
                opts.push(
                    `<option value="blister" ${getStock(btn,'blister')>0?'':'disabled'} ${presDefault==='blister'?'selected':''}>Blíster</option>`
                );
                opts.push(
                    `<option value="caja" ${getStock(btn,'caja')>0?'':'disabled'} ${presDefault==='caja'?'selected':''}>Caja</option>`
                );

                const fila = `
      <tr data-id="${id}">
        <td>
          <input type="hidden" name="productos[]" value="${id}">
          ${nombre}
        </td>

        <td>
          <select name="unidades_venta[]" class="form-select form-select-sm presentacion">
            ${opts.join('')}
          </select>
          <div class="form-text small text-muted stock-pres">Stock: ${getStock(btn, presDefault)}</div>
        </td>

        <td>
          <input type="number" name="cantidades[]" class="form-control cantidad" value="${cantidadIni}" min="1" step="1" inputmode="numeric" pattern="\\d*">
        </td>

        <td>
          <input type="number" name="precios[]" class="form-control precio" value="${precioUnit.toFixed(2)}" step="0.01" readonly>
        </td>

        <td>
          <!-- Descuento MONTO de la línea (lo que espera el backend en descuentos[]) -->
          <input type="number" name="descuentos[]" class="form-control descuento-linea" value="${descLinea.toFixed(2)}" step="0.01" readonly>
          <div class="form-text small text-muted">(-${descPct}%)</div>
        </td>

        <td><span class="subtotal">${subtotal.toFixed(2)}</span></td>

        <td>
          <button type="button" class="btn btn-danger btn-sm eliminar-producto">
            <i data-feather="trash"></i>
          </button>
        </td>
      </tr>
    `;
                tabla.insertAdjacentHTML("beforeend", fila);
                feather.replace();

                // Evita duplicados: mismo producto no puede añadirse dos veces
                btn.disabled = true;

                window.calcularTotales();
            });

            // Recalcular cuando cambia presentación o cantidad
            document.addEventListener("input", function(e) {
                if (e.target.matches(".cantidad")) {
                    const fila = e.target.closest("tr");
                    recalcFila(fila);
                }
            });
            document.addEventListener("change", function(e) {
                if (e.target.matches(".presentacion")) {
                    const fila = e.target.closest("tr");
                    recalcFila(fila);
                }
            });

            function recalcFila(fila) {
                const id = fila.getAttribute("data-id");
                const btn = document.querySelector(`.agregar-producto[data-id='${id}']`);
                const pres = fila.querySelector(".presentacion").value;

                const stock = getStock(btn, pres);
                const qtyInp = fila.querySelector(".cantidad");
                let qty = parseInt(qtyInp.value || "0", 10) || 0;

                if (qty < 1) qty = 1;
                if (qty > stock) {
                    qty = stock;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock insuficiente',
                        text: `Disponible: ${stock} (${pres}).`
                    });
                }
                qtyInp.value = qty;

                const precioUnit = getPrecio(btn, pres);
                const descPct = getDescPct(btn, pres);
                const descLinea = qty * precioUnit * (descPct / 100);
                const subtotal = (qty * precioUnit) - descLinea;

                fila.querySelector(".precio").value = precioUnit.toFixed(2);
                fila.querySelector(".descuento-linea").value = descLinea.toFixed(2);
                fila.querySelector(".subtotal").textContent = subtotal.toFixed(2);
                fila.querySelector(".stock-pres").textContent = `Stock: ${stock}`;

                window.calcularTotales();
            }

            // Eliminar producto
            document.addEventListener("click", function(e) {
                if (!e.target.closest(".eliminar-producto")) return;
                const fila = e.target.closest("tr");
                const id = fila.getAttribute("data-id");
                fila.remove();

                // Rehabilita el botón en la lista de productos
                const botonAgregar = document.querySelector(`.agregar-producto[data-id='${id}']`);
                if (botonAgregar) botonAgregar.disabled = false;

                window.calcularTotales();
            });

            // Función global de totales para usarla desde otros scripts también
            window.calcularTotales = function() {
                let total = 0;
                document.querySelectorAll(".subtotal").forEach(s => {
                    total += parseFloat(s.textContent) || 0;
                });
                document.getElementById("subtotal").textContent = `S/ ${total.toFixed(2)}`;
                document.getElementById("igv").textContent = `S/ 0.00`;
                document.getElementById("total").textContent = `S/ ${total.toFixed(2)}`;
            };

            // Validación numérica en cantidades (solo enteros >=1)
            document.addEventListener("keydown", function(e) {
                const el = e.target;
                if (el.classList && el.classList.contains("cantidad")) {
                    const invalid = ["-", "+", "e", "E", ".", ","];
                    if (invalid.includes(e.key)) e.preventDefault();
                }
            });

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
                // Validar que descuento ≤ precio por fila
                document.querySelectorAll("#tablaVenta tbody tr").forEach(fila => {
                    const precioInput = fila.querySelector(".precio");
                    const descuentoInput = fila.querySelector(".descuento-linea");

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
