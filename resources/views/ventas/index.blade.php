@extends('layouts.plantilla')

@section('content')
    <style>
        tr.stock-zero {
            background: #fdecea !important;
        }

        tr.stock-zero td {
            color: #e81328;
        }

        tr.stock-zero img {
            filter: grayscale(100%);
            opacity: .8;
        }

        .boton-agregar-producto {
            background: linear-gradient(135deg, #6EBF49, #8BBF65);
            border: none;
            border-radius: .6rem;
            color: #fff;
        }

        /* Toast éxito estilo verde suave */
        .colored-toast.swal2-popup {
            background: #f0fdf4;
            /* verde muy claro */
            color: #14532d;
            /* texto verde oscuro */
            border: 1px solid #bbf7d0;
            /* borde sutil */
            box-shadow: 0 10px 25px rgba(22, 163, 74, .15);
        }

        .colored-toast .swal2-title {
            font-weight: 600;
            letter-spacing: .2px;
        }

        .colored-toast .swal2-timer-progress-bar {
            background: #86efac;
            /* barra de tiempo */
        }

        /* === Colores por vencimiento (modal) === */
        /* ROJO: vencido */
        tr.vencido {
            background: #ffe5e5 !important;
            /* rojo pastel */
        }

        tr.vencido td {
            color: #b91c1c;
            /* rojo más oscuro y legible */
        }

        /* NARANJA: ≤ 3 meses */
        tr.vence-3m {
            background: #fff1e6 !important;
            /* naranja pastel */
        }

        tr.vence-3m td {
            color: #c2410c;
            /* naranja fuerte */
        }

        /* VERDE: ≤ 6 meses */
        tr.vence-6m {
            background: #e6f7ec !important;
            /* verde pastel */
        }

        tr.vence-6m td {
            color: #15803d;
            /* verde intenso */
        }

        /* AZUL: ≤ 9 meses */
        tr.vence-9m {
            background: #e6f0ff !important;
            /* azul pastel */
        }

        tr.vence-9m td {
            color: #1d4ed8;
            /* azul medio */
        }

        /* PLOMO: ≥ 10 meses */
        tr.vence-10m {
            background: #f2f2f2 !important;
            /* gris claro */
        }

        tr.vence-10m td {
            color: #374151;
            /* gris oscuro para contraste */
        }

        /* Fila enfocada por navegación con teclado */
        #tablaProductosModal tbody tr.row-active {
            outline: 2px solid #0A7ABF;
            box-shadow: inset 0 0 0 9999px rgba(10, 122, 191, 0.08);
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row g-4">
            <!-- Panel Registro de Venta (único) -->
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header text-white fw-bold fs-5 rounded-top-4 d-flex align-items-center justify-content-between"
                        style="background: linear-gradient(135deg, #0A7ABF, #25A6D9);">
                        <div><i class="fas fa-cash-register me-2 fs-4"></i> Registro de Venta</div>

                        <!-- Botón que abre el modal de productos -->
                        <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal"
                            data-bs-target="#modalBuscarProducto" id="btnAbrirModalProductos">
                            <i class="fas fa-search me-1"></i> Buscar productos (F2)
                        </button>
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
                                <div class="invalid-feedback">Por favor seleccione un cliente.</div>
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


                            <div class="d-flex flex-wrap align-items-stretch gap-2 mb-3">
                                <button type="button" class="btn btn-success fw-bold px-4 shadow-sm boton-agregar-producto"
                                    data-bs-toggle="modal" data-bs-target="#modalBuscarProducto">
                                    <i class="fas fa-plus-circle me-2"></i> Agregar producto
                                </button>

                                <!-- Predeterminado -->
                                <button type="button" id="btnPredeterminado"
                                    class="btn btn-primary fw-bold px-4 shadow-sm">
                                    <i class="fas fa-magic me-2"></i> Predeter.
                                </button>

                                <span class="w-100 d-block d-sm-none"></span>

                            </div>

                            <!-- Tabla de productos seleccionados -->
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered shadow-sm rounded text-center align-middle"
                                    id="tablaVenta">
                                    <thead style="background-color: #25A6D9; color: white;">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Laboratorio</th>
                                            <th>F. Venc.</th>
                                            <th>Presentación</th>
                                            <th>U/Blíster</th>
                                            <th>U/Caja</th>
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
        </div>
    </div>

    <!-- MODAL: Buscar/Agregar Productos -->
    <div class="modal fade" id="modalBuscarProducto" tabindex="-1" aria-labelledby="modalBuscarProductoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header" style="background: linear-gradient(135deg, #6EBF49, #8BBF65);">
                    <h5 class="modal-title text-white fw-bold" id="modalBuscarProductoLabel">
                        <i class="fas fa-box-open me-2"></i> Buscar productos
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <input type="text" id="buscarProductoModal" class="form-control mb-3"
                        placeholder="Escribe para filtrar por nombre, presentación, laboratorio o categoría...">

                    <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                        <table class="table table-hover align-middle" id="tablaProductosModal">
                            <thead class="table-light">
                                <tr>
                                    <th>Opción</th>
                                    <th>Nombre</th>
                                    <th>Laboratorio</th>
                                    <th>Fecha de venc.</th>
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
                                            : null;
                                        $uxc = $producto->unidades_por_caja ? (int) $producto->unidades_por_caja : null;

                                        // Cajas y sueltas (sin romper cajas)
                                        if ($uxc && $uxc > 0) {
                                            $cajas = intdiv($totalUnidades, $uxc);
                                            $sueltas = $totalUnidades % $uxc;
                                        } else {
                                            $cajas = null;
                                            $sueltas = $totalUnidades;
                                        }

                                        // Blísteres desde sueltas
                                        $blisters = $upb && $upb > 0 ? intdiv($sueltas, $upb) : null;

                                        $dispU = $sueltas > 0;
                                        $dispB = !is_null($blisters) && $blisters > 0;
                                        $dispC = !is_null($cajas) && $cajas > 0;
                                        $sinStockTotal = !$dispU && !$dispB && !$dispC;

                                        $fv = \Carbon\Carbon::parse($producto->fecha_vencimiento);
                                        $meses = now()->diffInMonths($fv, false); // negativo si ya venció

                                        if ($meses < 0) {
                                            $claseVenc = 'vencido'; // Rojo
                                            $tip = 'Vencido hace ' . abs($meses) . ' mes(es)';
                                        } elseif ($meses <= 3) {
                                            $claseVenc = 'vence-3m'; // Naranja
                                            $tip = "Vence en {$meses} mes(es)";
                                        } elseif ($meses <= 6) {
                                            $claseVenc = 'vence-6m'; // Verde
                                            $tip = "Vence en {$meses} mes(es)";
                                        } elseif ($meses <= 9) {
                                            $claseVenc = 'vence-9m'; // Azul
                                            $tip = "Vence en {$meses} mes(es)";
                                        } else {
                                            $claseVenc = 'vence-10m'; // Plomo (≥10 meses)
                                            $tip = "Vence en {$meses} mes(es)";
                                        }
                                    @endphp

                                    <tr class="{{ $totalUnidades == 0 ? 'stock-zero' : '' }} {{ $claseVenc }}"
                                        data-nombre="{{ strtolower($producto->descripcion) }}"
                                        data-presentacion="{{ strtolower($producto->presentacion) }}"
                                        data-laboratorio="{{ strtolower($producto->laboratorio) }}"
                                        data-categoria="{{ strtolower($producto->categorias->pluck('nombre')->implode(', ') ?? '') }}"
                                        title="{{ $tip }}">

                                        <td>
                                            <button type="button" class="btn btn-outline-primary btn-sm agregar-producto"
                                                data-id="{{ $producto->id }}" data-nombre="{{ $producto->descripcion }}"
                                                data-precio-u="{{ $producto->precio_venta ?? '' }}"
                                                data-precio-b="{{ $producto->precio_venta_blister ?? '' }}"
                                                data-precio-c="{{ $producto->precio_venta_caja ?? '' }}"
                                                data-descpct-u="{{ $producto->descuento ?? 0 }}"
                                                data-descpct-b="{{ $producto->descuento_blister ?? 0 }}"
                                                data-descpct-c="{{ $producto->descuento_caja ?? 0 }}"
                                                data-stock-u="{{ (int) ($producto->cantidad ?? 0) }}"
                                                data-stock-b="{{ $dispB ? $blisters : '' }}"
                                                data-stock-c="{{ $dispC ? $cajas : '' }}"
                                                data-lab="{{ $producto->laboratorio ?? '' }}"
                                                data-fv="{{ \Carbon\Carbon::parse($producto->fecha_vencimiento)->format('d/m/Y') }}"
                                                data-upb="{{ $upb ?? '' }}" data-uxc="{{ $uxc ?? '' }}"
                                                {{ $sinStockTotal ? 'disabled' : '' }}>
                                                <i data-feather="plus-circle"></i>
                                            </button>
                                        </td>
                                        <td class="info-producto">{{ $producto->descripcion }}</td>
                                        <td>{{ $producto->laboratorio ?? '—' }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($producto->fecha_vencimiento)->format('d/m/Y') }}
                                        </td>

                                        <td>
                                            @if ($uxc)
                                                <div class="small">
                                                    <span
                                                        class="badge {{ $cajas > 0 ? 'bg-success' : 'bg-secondary' }}">Cj:
                                                        {{ $cajas }}</span>
                                                    <span class="badge {{ $sueltas > 0 ? 'bg-success' : 'bg-danger' }}">U:
                                                        {{ $sueltas }}</span>
                                                    @if (!is_null($blisters))
                                                        <span
                                                            class="badge {{ $blisters > 0 ? 'bg-success' : 'bg-secondary' }}">Bl:
                                                            {{ $blisters }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="small">
                                                    <span class="badge {{ $sueltas > 0 ? 'bg-success' : 'bg-danger' }}">U:
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

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Mensajes SweetAlert --}}
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
            }).then((r) => {
                if (r.isConfirmed) {
                    window.open("{{ session('voucher_url') }}", '_blank');
                }
            });
        </script>
    @endif

    <!-- ===========================
                 Borrador persistente de venta
                 =========================== -->
    <script>
        window.addEventListener('load', () => {
            const DRAFT_KEY = 'ventaDraft_v1';

            function saveDraft() {
                const filas = Array.from(document.querySelectorAll('#tablaVenta tbody tr')).map(fila => {
                    const id = fila.getAttribute('data-id');
                    const sel = fila.querySelector('.presentacion');
                    const qty = fila.querySelector('.cantidad');
                    const btnModal = document.querySelector(`.agregar-producto[data-id='${id}']`);

                    return {
                        id,
                        // Nombre/lab/fv desde dataset del botón
                        nombre: btnModal?.dataset.nombre || '',
                        laboratorio: btnModal?.dataset.lab || '—',
                        fv: btnModal?.dataset.fv || '—',
                        presentacion: sel?.value || 'unidad',
                        cantidad: parseInt(qty?.value || '1', 10),

                        // datasets para recalcular
                        precioU: btnModal?.dataset.precioU || '',
                        precioB: btnModal?.dataset.precioB || '',
                        precioC: btnModal?.dataset.precioC || '',
                        descU: btnModal?.dataset.descpctU || 0,
                        descB: btnModal?.dataset.descpctB || 0,
                        descC: btnModal?.dataset.descpctC || 0
                        // ratios no son necesarios para persistir: se leen del botón
                    };
                });

                const draft = {
                    cliente: document.querySelector("select[name='id_cliente']")?.value || '',
                    documento: document.querySelector("select[name='id_documento']")?.value || '',
                    pago: document.querySelector("select[name='id_pago']")?.value || '',
                    pagado: document.getElementById('pagado')?.value || '',
                    filas
                };

                try {
                    localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
                } catch (e) {
                    console.warn('No se pudo guardar el borrador:', e);
                }
            }

            function clearDraft() {
                try {
                    localStorage.removeItem(DRAFT_KEY);
                } catch {}
            }

            function loadDraft() {
                let raw = null;
                try {
                    raw = localStorage.getItem(DRAFT_KEY);
                } catch {}
                if (!raw) return;

                let draft = null;
                try {
                    draft = JSON.parse(raw);
                } catch {
                    return;
                }

                // Restituir selects y pagado
                const cSel = document.querySelector("select[name='id_cliente']");
                const dSel = document.querySelector("select[name='id_documento']");
                const pSel = document.querySelector("select[name='id_pago']");
                const inpPagado = document.getElementById('pagado');
                if (cSel && draft.cliente) cSel.value = String(draft.cliente);
                if (dSel && draft.documento) dSel.value = String(draft.documento);
                if (pSel && draft.pago) pSel.value = String(draft.pago);
                if (inpPagado && draft.pagado !== undefined) inpPagado.value = draft.pagado;

                const tbodyVenta = document.querySelector('#tablaVenta tbody');
                if (!tbodyVenta) return;

                (draft.filas || []).forEach(item => {
                    const btn = document.querySelector(`.agregar-producto[data-id='${item.id}']`);
                    if (!btn) return; // si el producto ya no existe, saltamos

                    // Igual que al agregar desde el modal
                    const presDefault = window.primeraPresentacionDisponible(btn);
                    const precioUnit = window.getPrecio(btn, presDefault);
                    const descPct = window.getDescPct(btn, presDefault);
                    const cantidadIni = 1;
                    const descLinea = cantidadIni * precioUnit * (descPct / 100);
                    const subtotal = (cantidadIni * precioUnit) - descLinea;

                    const opts = [];
                    opts.push(
                        `<option value="unidad"  ${window.getStock(btn,'unidad')>0?'':'disabled'}  ${presDefault==='unidad'?'selected':''}>Unidad</option>`
                    );
                    opts.push(
                        `<option value="blister" ${window.getStock(btn,'blister')>0?'':'disabled'} ${presDefault==='blister'?'selected':''}>Blíster</option>`
                    );
                    opts.push(
                        `<option value="caja"    ${window.getStock(btn,'caja')>0?'':'disabled'}    ${presDefault==='caja'?'selected':''}>Caja</option>`
                    );

                    // Ratios desde dataset
                    const upb = parseInt(btn.dataset.upb || '') || null;
                    const uxc = parseInt(btn.dataset.uxc || '') || null;
                    const upbTxt = upb ? upb : '—';
                    const uxcTxt = uxc ? uxc : '—';

                    const filaHTML = `
                        <tr data-id="${item.id}">
                          <td><input type="hidden" name="productos[]" value="${item.id}">${item.nombre}</td>
                          <td>${btn.dataset.lab || '—'}</td>
                          <td>${btn.dataset.fv  || '—'}</td>
                          <td>
                            <select name="unidades_venta[]" class="form-select form-select-sm presentacion">
                              ${opts.join('')}
                            </select>
                            <div class="form-text small text-muted stock-pres">Stock: ${window.getStock(btn, presDefault)}</div>
                          </td>
                          <td><span class="badge bg-light text-dark">${upbTxt}</span></td>
                          <td><span class="badge bg-light text-dark">${uxcTxt}</span></td>
                          <td><input type="number" name="cantidades[]" class="form-control cantidad" value="${cantidadIni}" min="1" step="1" inputmode="numeric" pattern="\\d*"></td>
                          <td><input type="number" name="precios[]" class="form-control precio" value="${precioUnit.toFixed(2)}" step="0.01" readonly></td>
                          <td>
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
                    tbodyVenta.insertAdjacentHTML('beforeend', filaHTML);

                    // Ajustar presentación/cantidad guardadas y recalcular
                    const fila = tbodyVenta.querySelector(`tr[data-id='${item.id}']`);
                    if (fila) {
                        const sel = fila.querySelector('.presentacion');
                        if (sel && item.presentacion) sel.value = item.presentacion;
                        const qty = fila.querySelector('.cantidad');
                        if (qty) qty.value = String(item.cantidad || 1);
                        window.recalcFila(fila);
                    }

                    // Deshabilitar botón en modal para evitar duplicado
                    btn.disabled = true;
                });

                feather.replace?.();
                window.calcularTotales?.();
            }

            // Disparadores de guardado
            document.addEventListener('click', (e) => {
                if (e.target.closest('.agregar-producto') || e.target.closest('.eliminar-producto')) {
                    setTimeout(saveDraft, 0);
                }
            });
            document.addEventListener('input', (e) => {
                if (e.target.matches('.cantidad') || e.target.id === 'pagado') saveDraft();
            });
            document.addEventListener('change', (e) => {
                if (e.target.matches('.presentacion') ||
                    e.target.name === 'id_cliente' ||
                    e.target.name === 'id_documento' ||
                    e.target.name === 'id_pago') {
                    saveDraft();
                }
            });

            const form = document.getElementById('formVenta');
            if (form) {
                form.addEventListener('submit', clearDraft);
                form.addEventListener('reset', clearDraft);
            }

            // Carga inicial
            loadDraft();
        });
    </script>

    <!-- ===========================
                 Interacción UI / Helpers / Reglas
                 =========================== -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Atajo F2 abre modal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F2') {
                    e.preventDefault();
                    const btn = document.getElementById('btnAbrirModalProductos');
                    if (btn) btn.click();
                }
            });

            // Filtro en el modal
            const inputModal = document.getElementById('buscarProductoModal');
            if (inputModal) {
                inputModal.addEventListener('keyup', function() {
                    const filtro = this.value.toLowerCase();
                    document.querySelectorAll('#tablaProductosModal tbody tr').forEach(fila => {
                        const nombre = fila.dataset.nombre || '';
                        const presentacion = fila.dataset.presentacion || '';
                        const laboratorio = fila.dataset.laboratorio || '';
                        const categoria = fila.dataset.categoria || '';
                        const coincide = nombre.includes(filtro) || presentacion.includes(filtro) ||
                            laboratorio.includes(filtro) || categoria.includes(filtro);
                        fila.style.display = coincide ? '' : 'none';
                    });
                });
            }

            // ===== Helpers =====
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

            // Agregar producto desde el modal a la tabla de venta
            document.addEventListener('click', function(e) {
                const trigger = e.target.closest('.agregar-producto');
                if (!trigger) return;

                const btn = trigger;
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre;

                const tabla = document.querySelector('#tablaVenta tbody');
                const filaExistente = tabla.querySelector(`tr[data-id='${id}']`);

                const allZero = getStock(btn, 'unidad') === 0 &&
                    getStock(btn, 'blister') === 0 &&
                    getStock(btn, 'caja') === 0;
                if (allZero) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sin stock',
                        text: 'Este producto está agotado en todas las presentaciones.'
                    });
                    return;
                }

                if (filaExistente) {
                    const cantidadInput = filaExistente.querySelector('.cantidad');
                    const pres = filaExistente.querySelector('.presentacion').value;
                    const stock = getStock(btn, pres);
                    const nueva = (parseInt(cantidadInput.value || '0', 10) + 1);
                    if (nueva > stock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock insuficiente',
                            text: `Solo hay ${stock} en stock (${pres}).`
                        });
                        return;
                    }
                    cantidadInput.value = nueva;
                    cantidadInput.dispatchEvent(new Event('input', {
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
                    `<option value="unidad"  ${getStock(btn,'unidad')>0?'':'disabled'}  ${presDefault==='unidad'?'selected':''}>Unidad</option>`
                );
                opts.push(
                    `<option value="blister" ${getStock(btn,'blister')>0?'':'disabled'} ${presDefault==='blister'?'selected':''}>Blíster</option>`
                );
                opts.push(
                    `<option value="caja"    ${getStock(btn,'caja')>0?'':'disabled'}    ${presDefault==='caja'?'selected':''}>Caja</option>`
                );

                // Ratios
                const upb = parseInt(btn.dataset.upb || '') || null;
                const uxc = parseInt(btn.dataset.uxc || '') || null;
                const upbTxt = upb ? upb : '—';
                const uxcTxt = uxc ? uxc : '—';

                const fila = `
                    <tr data-id="${id}">
                        <td><input type="hidden" name="productos[]" value="${id}">${nombre}</td>
                        <td>${btn.dataset.lab || '—'}</td>
                        <td>${btn.dataset.fv || '—'}</td>
                        <td>
                            <select name="unidades_venta[]" class="form-select form-select-sm presentacion">
                              ${opts.join('')}
                            </select>
                            <div class="form-text small text-muted stock-pres">Stock: ${getStock(btn, presDefault)}</div>
                        </td>
                        <td><span class="badge bg-light text-dark">${upbTxt}</span></td>
                        <td><span class="badge bg-light text-dark">${uxcTxt}</span></td>
                        <td><input type="number" name="cantidades[]" class="form-control cantidad" value="${cantidadIni}" min="1" step="1" inputmode="numeric" pattern="\\d*"></td>
                        <td><input type="number" name="precios[]" class="form-control precio" value="${precioUnit.toFixed(2)}" step="0.01" readonly></td>
                        <td>
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

                tabla.insertAdjacentHTML('beforeend', fila);
                feather.replace();

                // Deshabilita botón del modal para evitar duplicado inmediato
                btn.disabled = true;

                window.calcularTotales();
                showAddedToast(`"${nombre}" agregado a la venta`);
            });

            // Recalcular al cambiar presentación o cantidad
            document.addEventListener('input', function(e) {
                if (e.target.matches('.cantidad')) {
                    const fila = e.target.closest('tr');
                    recalcFila(fila);
                }
            });
            document.addEventListener('change', function(e) {
                if (e.target.matches('.presentacion')) {
                    const fila = e.target.closest('tr');
                    recalcFila(fila);
                }
            });

            function recalcFila(fila) {
                const id = fila.getAttribute('data-id');
                const btn = document.querySelector(`.agregar-producto[data-id='${id}']`);
                const pres = fila.querySelector('.presentacion').value;

                const stock = getStock(btn, pres);
                const qtyInp = fila.querySelector('.cantidad');
                let qty = parseInt(qtyInp.value || '0', 10) || 0;

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

                fila.querySelector('.precio').value = precioUnit.toFixed(2);
                fila.querySelector('.descuento-linea').value = descLinea.toFixed(2);
                fila.querySelector('.subtotal').textContent = subtotal.toFixed(2);
                fila.querySelector('.stock-pres').textContent = `Stock: ${stock}`;

                window.calcularTotales();
            }

            // PUBLICAR helpers y recalc en window para el borrador
            window.getPrecio = getPrecio;
            window.getDescPct = getDescPct;
            window.getStock = getStock;
            window.primeraPresentacionDisponible = primeraPresentacionDisponible;
            window.recalcFila = recalcFila;

            // Eliminar producto desde la tabla
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.eliminar-producto')) return;
                const fila = e.target.closest('tr');
                const id = fila.getAttribute('data-id');
                fila.remove();

                const botonAgregar = document.querySelector(`.agregar-producto[data-id='${id}']`);
                if (botonAgregar) botonAgregar.disabled = false;

                window.calcularTotales();
            });

            // Totales globales
            window.calcularTotales = function() {
                let total = 0;
                document.querySelectorAll('.subtotal').forEach(s => total += parseFloat(s.textContent) || 0);
                document.getElementById('subtotal').textContent = `S/ ${total.toFixed(2)}`;
                document.getElementById('igv').textContent = `S/ 0.00`;
                document.getElementById('total').textContent = `S/ ${total.toFixed(2)}`;
            };

            // Validación de cantidades (solo enteros)
            document.addEventListener('keydown', function(e) {
                const el = e.target;
                if (el.classList && el.classList.contains('cantidad')) {
                    const invalid = ['-', '+', 'e', 'E', '.', ','];
                    if (invalid.includes(e.key)) e.preventDefault();
                }
            });

            // Cambio automático
            document.getElementById('pagado').addEventListener('input', function() {
                const totalTexto = document.getElementById('total').textContent.replace('S/', '').trim();
                const total = parseFloat(totalTexto) || 0;
                const pagado = parseFloat(this.value) || 0;
                const cambio = pagado - total;
                document.getElementById('cambio').textContent =
                    `S/ ${cambio >= 0 ? cambio.toFixed(2) : '0.00'}`;
            });

            feather.replace();
        });
    </script>

    <script>
        // Validación al enviar (debe haber al menos 1 producto)
        document.getElementById('formVenta').addEventListener('submit', function(e) {
            const filas = document.querySelectorAll('#tablaVenta tbody tr');
            if (filas.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Debes agregar al menos un producto a la venta.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Aceptar'
                });
            }

            // Validar que el descuento de la línea no supere el precio unitario (por línea)
            let ok = true;
            document.querySelectorAll('#tablaVenta tbody tr').forEach(fila => {
                const precio = parseFloat(fila.querySelector('.precio').value) || 0;
                const descuento = parseFloat(fila.querySelector('.descuento-linea').value) || 0;
                if (descuento > precio) {
                    ok = false;
                    const inp = fila.querySelector('.descuento-linea');
                    inp.classList.add('is-invalid');
                    if (!inp.nextElementSibling || !inp.nextElementSibling.classList.contains(
                            'invalid-feedback')) {
                        const fb = document.createElement('div');
                        fb.className = 'invalid-feedback';
                        fb.textContent = 'El descuento no puede ser mayor que el precio unitario.';
                        inp.parentNode.appendChild(fb);
                    }
                }
            });
            if (!ok) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('was-validated');
            }
        });
    </script>

    <script>
        // Instancia global para toasts de éxito
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
        // Botón Predeterminado
        document.addEventListener('DOMContentLoaded', () => {
            const btnPred = document.getElementById('btnPredeterminado');
            if (btnPred) {
                btnPred.addEventListener('click', () => {
                    const clienteSel = document.querySelector("select[name='id_cliente']");
                    if (clienteSel) clienteSel.value = "1";
                    const docSel = document.querySelector("select[name='id_documento']");
                    if (docSel) docSel.value = "1";
                    const pagoSel = document.querySelector("select[name='id_pago']");
                    if (pagoSel) pagoSel.value = "1";
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('modalBuscarProducto');
            const tableSelector = '#tablaProductosModal tbody tr';
            const filterInput = document.getElementById('buscarProductoModal');
            let selIndex = -1;

            function visibleRows() {
                if (!modalEl) return [];
                return Array.from(modalEl.querySelectorAll(tableSelector))
                    .filter(r => getComputedStyle(r).display !== 'none');
            }

            function firstEnabledRowIndex(rows) {
                return rows.findIndex(r => {
                    const btn = r.querySelector('.agregar-producto');
                    return btn && !btn.disabled;
                });
            }

            function setActive(index) {
                const rows = visibleRows();
                rows.forEach(r => r.classList.remove('row-active'));
                if (rows.length === 0) {
                    selIndex = -1;
                    return;
                }

                // Wrap-around
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
                if (rows.length === 0) {
                    selIndex = -1;
                    return;
                }
                const idx = firstEnabledRowIndex(rows);
                setActive(idx >= 0 ? idx : 0);
            }

            // Al abrir el modal: enfoca el buscador y selecciona la primera fila visible/habilitada
            modalEl?.addEventListener('shown.bs.modal', () => {
                if (filterInput) {
                    filterInput.focus();
                    filterInput.select?.();
                }
                // pequeña espera para que se apliquen filtros iniciales si los hubiera
                setTimeout(selectFirst, 0);
            });

            // Al cerrar, limpia el estado
            modalEl?.addEventListener('hidden.bs.modal', () => {
                selIndex = -1;
                visibleRows().forEach(r => r.classList.remove('row-active'));
            });

            // Recalcular selección cuando filtras
            filterInput?.addEventListener('input', () => {
                // Tu filtro ya oculta/mostrar filas; aquí solo re-anclamos la selección
                setTimeout(selectFirst, 0);
            });

            // Navegación con ↑/↓ y Enter dentro del modal
            modalEl?.addEventListener('keydown', (e) => {
                // Si quieres que las flechas funcionen aun cuando estás escribiendo en el input de búsqueda,
                // dejamos esta condición como está. Si prefieres que ↑/↓ NO interfieran cuando escribes,
                // descomenta esta línea para ignorar cuando el foco está en el input:
                // if (e.target === filterInput) return;

                if (e.key === 'ArrowDown') {
                    const rows = visibleRows();
                    if (!rows.length) return;
                    e.preventDefault();
                    setActive(selIndex + 1);
                } else if (e.key === 'ArrowUp') {
                    const rows = visibleRows();
                    if (!rows.length) return;
                    e.preventDefault();
                    setActive(selIndex - 1);
                } else if (e.key === 'Enter') {
                    const rows = visibleRows();
                    if (selIndex < 0 || !rows[selIndex]) return;

                    const btn = rows[selIndex].querySelector('.agregar-producto:not([disabled])');
                    if (btn) {
                        e.preventDefault();
                        btn.click(); // reutiliza tu flujo existente de "agregar producto"

                        // Avanzar a la siguiente fila habilitada (opcional, mejora el flujo rápido)
                        const updatedRows = visibleRows();
                        let next = selIndex;
                        // busca la próxima fila con botón habilitado
                        for (let i = selIndex + 1; i < updatedRows.length; i++) {
                            const b = updatedRows[i].querySelector('.agregar-producto');
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
@endsection
