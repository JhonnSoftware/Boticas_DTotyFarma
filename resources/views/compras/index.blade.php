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

        /* === Ajustar ancho máximo del modal de productos === */
        #modalProductos .modal-dialog.modal-xl {
            max-width: 85% !important;
            /* ocupa el 95% del ancho de la pantalla */
        }

        /* Toast éxito estilo verde suave (igual que Ventas) */
        .colored-toast.swal2-popup {
            background: #f0fdf4;
            /* verde muy claro */
            color: #14532d;
            /* texto verde oscuro */
            border: 1px solid #bbf7d0;
            box-shadow: 0 10px 25px rgba(22, 163, 74, .15);
        }

        .colored-toast .swal2-title {
            font-weight: 600;
            letter-spacing: .2px;
        }

        .colored-toast .swal2-timer-progress-bar {
            background: #86efac;
        }

        .boton-agregar-producto {
            background: linear-gradient(135deg, #6EBF49, #8BBF65);
            border: none;
            border-radius: .6rem;
            color: #fff;
        }

        /* Fila enfocada por navegación con teclado (modal de COMPRAS) */
        #modalProductos tbody tr.row-active {
            outline: 2px solid #0A7ABF;
            box-shadow: inset 0 0 0 9999px rgba(10, 122, 191, 0.08);
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

                            <div class="d-flex flex-wrap gap-2">
                                {{-- Botón: Buscar (abre modal) --}}
                                <button type="button" class="btn btn-success fw-bold px-4 shadow-sm boton-agregar-producto"
                                    data-bs-toggle="modal" data-bs-target="#modalProductos">
                                    <i class="fas fa-plus-circle me-2"></i> Buscar Producto
                                </button>

                                {{-- Agregar (abre el modal de registro rápido) --}}
                                <button type="button" class="btn btn-outline-success fw-bold px-4 shadow-sm"
                                    data-bs-toggle="modal" data-bs-target="#nuevoProducto">
                                    <i class="fas fa-plus me-2"></i> Agregar Producto
                                </button>
                            </div>
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
                                    <th>Lote</th>
                                    <th>Laboratorio</th>
                                    <th>F. Venc.</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
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
                                    <th>Lote</th>
                                    <th>Laboratorio</th>
                                    <th>F. Venc.</th>
                                    <th>P. Compra U</th>
                                    <th>P. Compra B</th>
                                    <th>P. Compra C</th>
                                    <th>Stock U</th>
                                    <th>Stock B</th>
                                    <th>Stock C</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productos as $producto)
                                    <tr>
                                        <!-- Datos base -->
                                        <td class="fw-semibold">{{ $producto->descripcion }}</td>
                                        <td>{{ $producto->lote ?? '—' }}</td>
                                        <td>{{ $producto->laboratorio ?? '—' }}</td>
                                        <td>
                                            @if (!empty($producto->fecha_vencimiento))
                                                {{ \Carbon\Carbon::parse($producto->fecha_vencimiento)->format('d/m/Y') }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <!-- Precio Compra -->
                                        <td>{{ $producto->precio_compra !== null ? 'S/ ' . number_format($producto->precio_compra, 2) : '—' }}
                                        </td>
                                        <td>{{ $producto->precio_compra_blister !== null ? 'S/ ' . number_format($producto->precio_compra_blister, 2) : '—' }}
                                        </td>
                                        <td>{{ $producto->precio_compra_caja !== null ? 'S/ ' . number_format($producto->precio_compra_caja, 2) : '—' }}
                                        </td>

                                        <!-- Stock -->
                                        <td>
                                            @if (($producto->cantidad ?? 0) === 0)
                                                <span class="badge bg-danger">0</span>
                                            @elseif (($producto->cantidad ?? 0) <= 10)
                                                <span class="badge bg-warning text-dark">{{ $producto->cantidad }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $producto->cantidad }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (($producto->cantidad_blister ?? 0) === 0)
                                                <span class="badge bg-danger">0</span>
                                            @elseif (($producto->cantidad_blister ?? 0) <= 5)
                                                <span
                                                    class="badge bg-warning text-dark">{{ $producto->cantidad_blister }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $producto->cantidad_blister }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (($producto->cantidad_caja ?? 0) === 0)
                                                <span class="badge bg-danger">0</span>
                                            @elseif (($producto->cantidad_caja ?? 0) <= 3)
                                                <span
                                                    class="badge bg-warning text-dark">{{ $producto->cantidad_caja }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $producto->cantidad_caja }}</span>
                                            @endif
                                        </td>

                                        <!-- Botón Agregar -->
                                        <td>
                                            <button type="button" class="btn btn-sm text-white seleccionar-producto"
                                                style="background-color:#6EBF49;" data-id="{{ $producto->id }}"
                                                data-nombre="{{ $producto->descripcion }}"
                                                data-precio-unidad="{{ $producto->precio_compra ?? '' }}"
                                                data-precio-blister="{{ $producto->precio_compra_blister ?? '' }}"
                                                data-precio-caja="{{ $producto->precio_compra_caja ?? '' }}"
                                                data-lote="{{ $producto->lote }}"
                                                data-laboratorio="{{ $producto->laboratorio }}"
                                                data-vencimiento="{{ $producto->fecha_vencimiento }}">
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

    @include('productos.partials.modal_registrar', [
        'categorias' => $categorias ?? collect(),
        'clases' => $clases ?? collect(),
        'genericos' => $genericos ?? collect(),
        'proveedores' => $proveedores ?? collect(),
        'nuevoCodigo' => $nuevoCodigo ?? null,
    ])
@endsection

@section('scripts')
    <script>
        // Instancia global para toasts de éxito (idéntico a Ventas)
        const ToastSuccess = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2200,
            timerProgressBar: true,
            iconColor: '#16a34a',
            customClass: {
                popup: 'colored-toast'
            },
            didOpen: (t) => {
                t.addEventListener('mouseenter', Swal.stopTimer);
                t.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        function showAddedToast(msg) {
            ToastSuccess.fire({
                icon: 'success',
                title: msg
            });
        }

        function showInfoToast(msg) {
            ToastSuccess.fire({
                icon: 'info',
                title: msg
            });
        }
    </script>

    <script>
        // ====== Base ======
        const tabla = document.querySelector("#tablaCompra tbody");

        function toMoney(n) {
            return `S/ ${(Number(n)||0).toFixed(2)}`;
        }

        // ====== Totales (total= suma subtotales; IGV=18% del total; subtotal=total-IGV) ======
        function calcularTotales() {
            let total = 0;
            document.querySelectorAll(".subtotal-fila").forEach(span => {
                total += parseFloat(span.dataset.raw || "0");
            });
            const igv = total * 0.18;
            const subtotal = total - igv;
            document.getElementById("subtotal").textContent = toMoney(subtotal);
            document.getElementById("igv").textContent = toMoney(igv);
            document.getElementById("total").textContent = toMoney(total);
        }

        // ====== Helpers ======
        function intOr0(v) {
            const x = parseInt((v ?? '').toString().trim(), 10);
            return isNaN(x) ? 0 : x;
        }

        function qtyValidOrEmpty(v) {
            if (v === '' || v === null || v === undefined) return true;
            return /^\d+$/.test(v) && parseInt(v, 10) >= 0;
        }

        function refreshGuardarState() {
            const btn = document.querySelector("#formCompra button[type='submit']");
            if (btn) btn.disabled = false; // siempre habilitado
        }


        function recalcFila(tr) {
            const pU = parseFloat(tr.dataset.pUnidad || "0");
            const pB = tr.dataset.pBlister === '' ? null : parseFloat(tr.dataset.pBlister);
            const pC = tr.dataset.pCaja === '' ? null : parseFloat(tr.dataset.pCaja);

            const nU = intOr0(tr.querySelector(".qty-unidad")?.value);
            const nB = intOr0(tr.querySelector(".qty-blister")?.value);
            const nC = intOr0(tr.querySelector(".qty-caja")?.value);

            const subU = (pU || 0) * nU;
            const subB = (pB || 0) * nB;
            const subC = (pC || 0) * nC;

            const totalFila = subU + subB + subC;

            // mostrar desglose
            const cellBreak = tr.querySelector(".breakdown");
            cellBreak.innerHTML = `
                <div class="small lh-sm">
                    <div>U: ${toMoney(subU)}</div>
                    <div>B: ${toMoney(subB)}</div>
                    <div>C: ${toMoney(subC)}</div>
                </div>
            `;

            const cellTotal = tr.querySelector(".subtotal-fila");
            cellTotal.textContent = toMoney(totalFila);
            cellTotal.dataset.raw = String(totalFila);

            calcularTotales();
            refreshGuardarState();
        }

        // util: normaliza la fecha para <input type="date">
        function toInputDate(v) {
            // si viene 'YYYY-MM-DD' sirve directo; si viene vacío, devuelve ''.
            if (!v) return '';
            // intenta parsear por seguridad
            const d = new Date(v);
            if (isNaN(d.getTime())) return '';
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }


        // ====== Clicks ======
        document.addEventListener("click", function(e) {
            // Agregar producto desde el modal
            const btn = e.target.closest(".seleccionar-producto");
            if (btn) {
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre;

                // precios
                const pU = parseFloat(btn.dataset.precioUnidad ?? btn.dataset.precio ?? 0);
                const pB = (btn.dataset.precioBlister === undefined || btn.dataset.precioBlister === '' || btn
                        .dataset.precioBlister === null) ?
                    null : parseFloat(btn.dataset.precioBlister);
                const pC = (btn.dataset.precioCaja === undefined || btn.dataset.precioCaja === '' || btn.dataset
                        .precioCaja === null) ?
                    null : parseFloat(btn.dataset.precioCaja);

                // datos visuales
                const lote = btn.dataset.lote || '';
                const lab = btn.dataset.laboratorio || '';
                const vencRaw = btn.dataset.vencimiento || '';
                const venc = vencRaw ? new Date(vencRaw).toLocaleDateString('es-PE') : '—';

                if (!id || document.querySelector(`#tablaCompra tbody tr[data-id="${id}"]`)) return;

                // Inputs habilitados solo si hay precio
                const enableU = isFinite(pU);
                const enableB = (pB !== null && isFinite(pB));
                const enableC = (pC !== null && isFinite(pC));

                const fila = `
                    <tr data-id="${id}" data-p-unidad="${isFinite(pU)?pU:''}" data-p-blister="${enableB?pB:''}" data-p-caja="${enableC?pC:''}">
                        <td>
                        <input type="hidden" name="productos[${id}][id_producto]" value="${id}">
                        ${nombre}
                        </td>

                        <!-- Lote editable -->
                        <td style="min-width:160px">
                        <input type="text" class="form-control form-control-sm" 
                                name="productos[${id}][lote]" value="${(lote||'')}" placeholder="Lote">
                        </td>

                        <!-- Laboratorio editable -->
                        <td style="min-width:200px">
                        <input type="text" class="form-control form-control-sm" 
                                name="productos[${id}][laboratorio]" value="${(lab||'')}" placeholder="Laboratorio">
                        </td>

                        <!-- F. Venc. editable -->
                        <td style="min-width:170px">
                        <input type="date" class="form-control form-control-sm"
                                name="productos[${id}][fecha_vencimiento]" value="${toInputDate(vencRaw)}">
                        </td>

                        <td style="min-width:220px">
                        <!-- (resto igual: cantidades U/B/C) -->
                        <div class="d-grid gap-1">
                            <div class="input-group input-group-sm">
                            <span class="input-group-text">U</span>
                            <input type="number" class="form-control qty-unidad" min="0" value="0" ${enableU? '' : 'disabled'}>
                            <span class="input-group-text price-u">${enableU ? toMoney(pU) : '—'}</span>
                            <input type="hidden" name="productos[${id}][precio_unidad]" value="${enableU ? pU.toFixed(2) : ''}">
                            </div>
                            <div class="input-group input-group-sm">
                            <span class="input-group-text">B</span>
                            <input type="number" class="form-control qty-blister" min="0" value="0" ${enableB? '' : 'disabled'}>
                            <span class="input-group-text price-b">${enableB ? toMoney(pB) : '—'}</span>
                            <input type="hidden" name="productos[${id}][precio_blister]" value="${enableB ? pB.toFixed(2) : ''}">
                            </div>
                            <div class="input-group input-group-sm">
                            <span class="input-group-text">C</span>
                            <input type="number" class="form-control qty-caja" min="0" value="0" ${enableC? '' : 'disabled'}>
                            <span class="input-group-text price-c">${enableC ? toMoney(pC) : '—'}</span>
                            <input type="hidden" name="productos[${id}][precio_caja]" value="${enableC ? pC.toFixed(2) : ''}">
                            </div>
                        </div>

                        <input type="hidden" name="productos[${id}][cantidad_unidad]"  class="hid-unidad"  value="0">
                        <input type="hidden" name="productos[${id}][cantidad_blister]" class="hid-blister" value="0">
                        <input type="hidden" name="productos[${id}][cantidad_caja]"    class="hid-caja"    value="0">
                        </td>

                        <td class="breakdown align-middle"></td>
                        <td class="subtotal-fila align-middle" data-raw="0">S/ 0.00</td>

                        <td class="align-middle">
                        <button type="button" class="btn btn-sm btn-danger eliminar-producto">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        </td>
                    </tr>
                    `;

                tabla.insertAdjacentHTML("beforeend", fila);

                // Recalc inicial
                const tr = tabla.querySelector(`tr[data-id="${id}"]`);
                recalcFila(tr);

                // Cierra modal
                // Cerrar modal de forma segura (sin reventar si 'bootstrap' no está global)
                (function() {
                    const modalEl = document.getElementById("modalProductos");
                    try {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            // Usa instancia existente o crea una y ocúltala
                            const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            inst.hide();
                        } else {
                            // Fallback: intenta cerrar con el botón o quitando clases
                            const closeBtn = modalEl?.querySelector('[data-bs-dismiss="modal"]');
                            closeBtn && closeBtn.click();
                            modalEl?.classList.remove('show');
                            document.body.classList.remove('modal-open');
                            const backdrop = document.querySelector('.modal-backdrop');
                            backdrop && backdrop.remove();
                        }
                    } catch (e) {
                        // No bloquees el flujo si falla
                        console.warn('No se pudo cerrar el modal:', e);
                    }
                })();

                // ✅ Toast: producto agregado (siempre)
                showAddedToast(`"${nombre}" agregado a la compra`);
                return;

            }

            // Eliminar fila
            const del = e.target.closest(".eliminar-producto");
            if (del) {
                const tr = del.closest("tr");
                tr?.remove();
                calcularTotales();
                refreshGuardarState();
            }
        });

        // ====== Entrada de cantidades ======
        document.addEventListener("input", function(e) {
            const el = e.target;
            if (!el.classList) return;

            if (el.classList.contains("qty-unidad") || el.classList.contains("qty-blister") || el.classList
                .contains("qty-caja")) {
                // Validar entero >=0
                let v = el.value.trim();
                if (!qtyValidOrEmpty(v)) {
                    el.classList.add("is-invalid");
                } else {
                    el.classList.remove("is-invalid");
                }

                // sincronizar al hidden correspondiente
                const tr = el.closest("tr");
                if (el.classList.contains("qty-unidad")) tr.querySelector(".hid-unidad").value = String(intOr0(v));
                if (el.classList.contains("qty-blister")) tr.querySelector(".hid-blister").value = String(intOr0(
                    v));
                if (el.classList.contains("qty-caja")) tr.querySelector(".hid-caja").value = String(intOr0(v));

                recalcFila(tr);
            }
        });

        // Bloquear teclas no numéricas en qty
        document.addEventListener("keydown", function(e) {
            const el = e.target;
            if (el.classList && (el.classList.contains("qty-unidad") || el.classList.contains("qty-blister") || el
                    .classList.contains("qty-caja"))) {
                const invalid = ["-", "+", "e", "E", ".", ","];
                if (invalid.includes(e.key)) e.preventDefault();
            }
        });

        // Normalizar al salir
        document.addEventListener("blur", function(e) {
            const el = e.target;
            if (el.classList && (el.classList.contains("qty-unidad") || el.classList.contains("qty-blister") || el
                    .classList.contains("qty-caja"))) {
                let v = el.value.trim();
                if (!qtyValidOrEmpty(v)) v = "0";
                el.value = String(intOr0(v));
                // disparar input para recalc
                el.dispatchEvent(new Event("input", {
                    bubbles: true
                }));
            }
        }, true);

        // ====== Submit: validar que cada fila tenga al menos una cantidad > 0 ======
        document.getElementById("formCompra").addEventListener("submit", function(e) {
            let ok = true,
                firstBad = null;

            const filas = document.querySelectorAll("#tablaCompra tbody tr");
            if (filas.length === 0) {
                ok = false;
            }

            filas.forEach(tr => {
                const qU = tr.querySelector(".qty-unidad")?.value ?? '';
                const qB = tr.querySelector(".qty-blister")?.value ?? '';
                const qC = tr.querySelector(".qty-caja")?.value ?? '';

                // validaciones básicas
                if (!(qtyValidOrEmpty(qU) && qtyValidOrEmpty(qB) && qtyValidOrEmpty(qC))) {
                    ok = false;
                    if (!firstBad) firstBad = tr.querySelector(".qty-unidad") || tr.querySelector(
                        ".qty-blister") || tr.querySelector(".qty-caja");
                }

                const nU = intOr0(qU),
                    nB = intOr0(qB),
                    nC = intOr0(qC);
                if ((nU + nB + nC) === 0) {
                    ok = false;
                    if (!firstBad) firstBad = tr.querySelector(".qty-unidad");
                }
            });

            if (!ok) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Datos incompletos',
                    text: filas.length === 0 ? 'Agrega al menos un producto.' :
                        'Cada producto debe tener al menos una cantidad > 0 (U, B o C) y las cantidades deben ser enteros ≥ 0.'
                });
                firstBad && firstBad.focus();
                refreshGuardarState();
            }
        });

        // ====== Filtro del modal ======
        (function() {
            const buscador = document.getElementById("buscadorProducto");
            if (!buscador) return;
            buscador.addEventListener("input", function() {
                const filtro = this.value.toLowerCase();
                const filas = document.querySelectorAll("#modalProductos tbody tr");
                filas.forEach(fila => {
                    const nombre = fila.children[0].textContent.toLowerCase();
                    fila.style.display = nombre.includes(filtro) ? "" : "none";
                });
            });
        })();
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const $modal = $('#nuevoProducto');

            function initCats() {
                const $cats = $('#categorias_select');
                if ($cats.length && typeof $cats.select2 === 'function') {
                    $cats.select2({
                        width: '100%',
                        dropdownParent: $modal,
                        placeholder: $cats.data('placeholder') || 'Seleccionar categorías...',
                        allowClear: true,
                        closeOnSelect: false
                    });
                }
            }

            // Al abrir: inicializa y fuerza a empezar sin selección
            $modal.on('shown.bs.modal', function() {
                initCats();
                const $cats = $('#categorias_select');
                // Si no vienes de un "old()", arrancar vacío:
                if (!@json(old('categorias', false))) {
                    $cats.val(null).trigger('change');
                }
            });

            // Al cerrar: limpia y destruye para evitar duplicados
            $modal.on('hidden.bs.modal', function() {
                const $cats = $('#categorias_select');
                if ($cats.length && $cats.data('select2')) {
                    $cats.val(null).trigger('change');
                    $cats.select2('destroy');
                }
                const form = this.querySelector('form');
                if (form) {
                    form.reset();
                    form.classList.remove('was-validated');
                }
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // === Navegación por teclado en el modal de productos (COMPRAS) ===
            const modalEl = document.getElementById('modalProductos');
            const filterInput = document.getElementById('buscadorProducto');
            const rowSelector = '#modalProductos tbody tr';
            const btnSelector = '.seleccionar-producto'; // <- botón de tu modal de COMPRAS
            let selIndex = -1;

            function visibleRows() {
                if (!modalEl) return [];
                return Array.from(modalEl.querySelectorAll(rowSelector))
                    .filter(r => getComputedStyle(r).display !== 'none');
            }

            function firstEnabledRowIndex(rows) {
                return rows.findIndex(r => {
                    const btn = r.querySelector(btnSelector);
                    return btn && !btn.disabled;
                });
            }

            function setActive(index) {
                const rows = visibleRows();
                rows.forEach(r => r.classList.remove('row-active'));

                if (!rows.length) {
                    selIndex = -1;
                    return;
                }

                // wrap-around
                if (index < 0) index = rows.length - 1;
                if (index >= rows.length) index = 0;

                selIndex = index;
                const row = rows[selIndex];
                row.classList.add('row-active');
                row.scrollIntoView({
                    block: 'nearest'
                });
            }

            function selectFirst() {
                const rows = visibleRows();
                if (!rows.length) {
                    selIndex = -1;
                    return;
                }
                const idx = firstEnabledRowIndex(rows);
                setActive(idx >= 0 ? idx : 0);
            }

            // Al abrir: enfoca el buscador y selecciona la primera fila visible/habilitada
            modalEl?.addEventListener('shown.bs.modal', () => {
                if (filterInput) {
                    filterInput.focus();
                    filterInput.select?.();
                }
                // pequeño delay para respetar filtros iniciales
                setTimeout(selectFirst, 0);
            });

            // Al cerrar: limpia el estado visual
            modalEl?.addEventListener('hidden.bs.modal', () => {
                selIndex = -1;
                visibleRows().forEach(r => r.classList.remove('row-active'));
            });

            // Cuando escribes en el buscador (tu otro script ya filtra filas),
            // aquí solo re-anclamos la selección a la primera visible/habilitada:
            filterInput?.addEventListener('input', () => {
                // espera a que el otro filtro oculte/muestre filas
                setTimeout(selectFirst, 0);
            });

            // Navegación con teclas dentro del modal:
            // ↑/←: arriba | ↓/→: abajo | Enter: "seleccionar-producto"
            modalEl?.addEventListener('keydown', (e) => {
                const rows = visibleRows();
                if (!rows.length) return;

                // Si prefieres que ↑/↓ no interfieran cuando el foco está en el input de búsqueda,
                // descomenta esta línea:
                // if (e.target === filterInput) return;

                if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
                    e.preventDefault();
                    setActive(selIndex + 1);
                } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                    e.preventDefault();
                    setActive(selIndex - 1);
                } else if (e.key === 'Enter') {
                    if (selIndex < 0 || !rows[selIndex]) return;
                    const btn = rows[selIndex].querySelector(`${btnSelector}:not([disabled])`);
                    if (btn) {
                        e.preventDefault();
                        btn.click(); // reutiliza tu flujo de "Agregar" existente

                        // Opcional: saltar automáticamente a la siguiente fila habilitada
                        const updated = visibleRows();
                        let next = selIndex;
                        for (let i = selIndex + 1; i < updated.length; i++) {
                            const b = updated[i].querySelector(btnSelector);
                            if (b && !b.disabled) {
                                next = i;
                                break;
                            }
                        }
                        setActive(next);
                    }
                }
            });
        });
    </script>


    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6',
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif


@endsection
