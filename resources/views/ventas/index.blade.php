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

        /* Ancho extra del modal de productos */
        #modalBuscarProducto .modal-dialog {
            max-width: min(95vw, 1700px);
            /* ancho flexible y grande */
        }

        /* Opcional: encabezado pegajoso para que el thead no se pierda al hacer scroll */
        #modalBuscarProducto thead tr th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 2;
        }

        /* Ajustar ancho de columnas específicas del modal */
        #tablaProductosModal th:nth-child(4),
        #tablaProductosModal td:nth-child(4) {
            max-width: 250px;
            /* puedes probar 200 o 180 según se vea */
            width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Opcional: mostrar texto completo al pasar el mouse */
        #tablaProductosModal td:nth-child(4)[title]::after {
            content: attr(title);
        }
    </style>

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
                                            <th>U/Caja</th>
                                            <th>Cant. (CajasFUnid)</th>
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
                                    <th>Genérico</th>
                                    <th>Lote</th>
                                    <th>U/Blíster</th>
                                    <th>U/Caja</th>
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

                                        if ($uxc && $uxc > 0) {
                                            $cajas = intdiv($totalUnidades, $uxc);
                                            $sueltas = $totalUnidades % $uxc;
                                        } else {
                                            $cajas = null;
                                            $sueltas = $totalUnidades;
                                        }

                                        $blisters = $upb && $upb > 0 ? intdiv($sueltas, $upb) : null;

                                        $dispU = $sueltas > 0;
                                        $dispB = !is_null($blisters) && $blisters > 0;
                                        $dispC = !is_null($cajas) && $cajas > 0;
                                        $sinStockTotal = !$dispU && !$dispB && !$dispC;

                                        $fv = \Carbon\Carbon::parse($producto->fecha_vencimiento);
                                        $meses = now()->diffInMonths($fv, false);

                                        if ($meses < 0) {
                                            $claseVenc = 'vencido';
                                            $tip = 'Vencido hace ' . abs($meses) . ' mes(es)';
                                        } elseif ($meses <= 3) {
                                            $claseVenc = 'vence-3m';
                                            $tip = "Vence en {$meses} mes(es)";
                                        } elseif ($meses <= 6) {
                                            $claseVenc = 'vence-6m';
                                            $tip = "Vence en {$meses} mes(es)";
                                        } elseif ($meses <= 9) {
                                            $claseVenc = 'vence-9m';
                                            $tip = "Vence en {$meses} mes(es)";
                                        } else {
                                            $claseVenc = 'vence-10m';
                                            $tip = "Vence en {$meses} mes(es)";
                                        }

                                        // NUEVO: lote y genérico
                                        $lote = $producto->lote ?? '—';
                                        $generico = optional($producto->generico)->nombre ?? '—';
                                    @endphp


                                    <tr class="{{ $totalUnidades == 0 ? 'stock-zero' : '' }} {{ $claseVenc }}"
                                        data-nombre="{{ strtolower($producto->descripcion) }}"
                                        data-presentacion="{{ strtolower($producto->presentacion) }}"
                                        data-laboratorio="{{ strtolower($producto->laboratorio) }}"
                                        data-categoria="{{ strtolower($producto->categorias->pluck('nombre')->implode(', ') ?? '') }}"
                                        data-generico="{{ strtolower($generico) }}" data-lote="{{ strtolower($lote) }}"
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
                                                data-generico="{{ $generico }}" data-lote="{{ $lote }}"
                                                {{ $sinStockTotal ? 'disabled' : '' }}>
                                                <i data-feather="plus-circle"></i>
                                            </button>

                                        </td>
                                        <td class="info-producto">{{ $producto->descripcion }}</td>
                                        <td>{{ $producto->laboratorio ?? '—' }}</td>
                                        <td title="{{ $generico }}">{{ Str::limit($generico, 50) }}</td>
                                        {{-- Genérico --}}
                                        <td>{{ $lote }}</td> {{-- Lote --}}
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ $upb ?? '—' }}</span>
                                        </td> {{-- U/Blíster --}}
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ $uxc ?? '—' }}</span>
                                        </td> {{-- U/Caja --}}
                                        <td>{{ \Carbon\Carbon::parse($producto->fecha_vencimiento)->format('d/m/Y') }}</td>
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
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
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

    <script>
        /* ========== Utilidades de formato CajasFUnid con modo simple (uxc<=1) ========== */
        /**
         * MODO SIMPLE (uxc<=1): solo enteros, sin "F".
         * - parseCantidad("1F2", 1) => { unidadesTotales: 12, simple:true, hadF:true }  (se detecta presencia de F)
         *
         * MODO CAJAS (uxc>1):
         * - "3F5" -> 3 cajas y 5 unidades
         * - "F5"  -> 0 cajas y 5 unidades
         * - "12"  -> 12 unidades totales
         */
        function parseCantidad(raw, uxc) {
            uxc = parseInt(uxc || '0', 10) || 0;
            const txt = String(raw || '').trim().toUpperCase();

            // MODO SIMPLE: uxc<=1 => solo enteros, nada de "F"
            if (uxc <= 1) {
                const hadF = /F/.test(txt);
                if (!txt) return {
                    cajas: 0,
                    unidadesSueltas: 0,
                    unidadesTotales: 0,
                    vacio: true,
                    simple: true,
                    hadF
                };
                const n = parseInt(txt.replace(/[^\d]/g, ''), 10);
                const tot = isNaN(n) ? 0 : n;
                return {
                    cajas: 0,
                    unidadesSueltas: tot,
                    unidadesTotales: tot,
                    simple: true,
                    hadF
                };
            }

            // MODO CAJAS-F-UNID
            if (!txt) return {
                cajas: 0,
                unidadesSueltas: 0,
                unidadesTotales: 0,
                vacio: true
            };

            if (/^\d+$/.test(txt)) {
                const tot = parseInt(txt, 10);
                return {
                    cajas: Math.floor(tot / uxc),
                    unidadesSueltas: tot % uxc,
                    unidadesTotales: tot
                };
            }

            const m = txt.match(/^(\d+)?F(\d+)?$/i);
            if (m) {
                const c = parseInt(m[1] || '0', 10) || 0;
                const u = parseInt(m[2] || '0', 10) || 0;
                const tot = c * uxc + u;
                return {
                    cajas: c,
                    unidadesSueltas: u,
                    unidadesTotales: tot
                };
            }

            const n = parseInt(txt.replace(/[^\d]/g, ''), 10);
            const tot = isNaN(n) ? 0 : n;
            return {
                cajas: Math.floor(tot / uxc),
                unidadesSueltas: tot % uxc,
                unidadesTotales: tot
            };
        }

        /* Unidades totales disponibles combinando U/B/C */
        function unidadesDisponibles(btn) {
            const stockU = parseInt(btn.dataset.stockU || '0', 10) || 0;
            const stockB = parseInt(btn.dataset.stockB || '0', 10) || 0;
            const stockC = parseInt(btn.dataset.stockC || '0', 10) || 0;
            const upb = parseInt(btn.dataset.upb || '0', 10) || 0;
            const uxc = parseInt(btn.dataset.uxc || '0', 10) || 0;
            const viaB = (stockB > 0 && upb > 0) ? stockB * upb : 0;
            const viaC = (stockC > 0 && uxc > 0) ? stockC * uxc : 0;
            return stockU + viaB + viaC;
        }

        /* Precio y descuento por unidad */
        function getPrecioUnidad(btn) {
            return parseFloat(btn.dataset.precioU || '0') || 0;
        }

        function getDescPctUnidad(btn) {
            return parseFloat(btn.dataset.descpctU || '0') || 0;
        }

        /* Helpers de importes */
        function calcularImportes(btn, totU) {
            const precioU = getPrecioUnidad(btn);
            const descPct = getDescPctUnidad(btn);
            const descLinea = totU * precioU * (descPct / 100);
            const subtotal = (totU * precioU) - descLinea;
            return {
                precioU,
                descPct,
                descLinea,
                subtotal
            };
        }

        function pintarImportesFila(fila, metrics) {
            fila.querySelector('.precio').value = metrics.precioU.toFixed(2);
            fila.querySelector('.descuento-linea').value = metrics.descLinea.toFixed(2);
            fila.querySelector('.subtotal').textContent = metrics.subtotal.toFixed(2);
        }
    </script>

    <script>
        /* ========== Borrador localStorage (guarda lo que el usuario tipeó tal cual) ========== */
        window.addEventListener('load', () => {
            const DRAFT_KEY = 'ventaDraft_v2_cf';

            function saveDraft() {
                const filas = Array.from(document.querySelectorAll('#tablaVenta tbody tr')).map(fila => {
                    const id = fila.getAttribute('data-id');
                    const btn = document.querySelector(`.agregar-producto[data-id='${id}']`);
                    return {
                        id,
                        nombre: btn?.dataset.nombre || '',
                        laboratorio: btn?.dataset.lab || '—',
                        fv: btn?.dataset.fv || '—',
                        uxc: parseInt(btn?.dataset.uxc || '0', 10) || 0,
                        cantidadRaw: fila.querySelector('.cantidad')?.value || '',
                        precioU: btn?.dataset.precioU || '0',
                        descU: btn?.dataset.descpctU || '0'
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
                    console.warn(e);
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
                    if (!btn) return;

                    const uxc = parseInt(btn.dataset.uxc || '0', 10) || 0;
                    const precioU = getPrecioUnidad(btn);
                    const descPct = getDescPctUnidad(btn);

                    const placeholder = (uxc > 1) ? "p.ej. 3F5 o F5 o 12" : "p.ej. 12";
                    const uxcTxt = (uxc > 1) ? uxc : '—';
                    const cantRaw = item.cantidadRaw || '';

                    const filaHTML = `
        <tr data-id="${item.id}">
          <td>
            <input type="hidden" name="productos[]" value="${item.id}">
            <input type="hidden" name="unidades_venta[]" value="unidad"><!-- clásico -->
            <input type="hidden" name="cantidades[]" value="0"><!-- se setea al confirmar -->
            ${btn.dataset.nombre || ''}
          </td>
          <td>${btn.dataset.lab || '—'}</td>
          <td>${btn.dataset.fv  || '—'}</td>
          <td><span class="badge bg-light text-dark">${uxcTxt}</span></td>
          <td>
            <input type="text" name="cantidades_raw[]" class="form-control cantidad" value="${cantRaw}" placeholder="${placeholder}">
            <div class="form-text small text-muted hint-cant">Total U: —</div>
          </td>
          <td><input type="number" name="precios[]" class="form-control precio" value="${precioU.toFixed(2)}" step="0.01" readonly></td>
          <td>
            <input type="number" name="descuentos[]" class="form-control descuento-linea" value="0.00" step="0.01" readonly>
            <div class="form-text small text-muted">(-${descPct}%)</div>
          </td>
          <td><span class="subtotal">0.00</span></td>
          <td>
            <button type="button" class="btn btn-danger btn-sm eliminar-producto">
              <i data-feather="trash"></i>
            </button>
          </td>
        </tr>
      `;
                    tbodyVenta.insertAdjacentHTML('beforeend', filaHTML);

                    window.previewFila(tbodyVenta.querySelector(`tr[data-id='${item.id}']`));
                    window.commitFila(tbodyVenta.querySelector(`tr[data-id='${item.id}']`));
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
                if (e.target.name === 'id_cliente' || e.target.name === 'id_documento' || e.target.name ===
                    'id_pago') saveDraft();
            });

            const form = document.getElementById('formVenta');
            if (form) {
                form.addEventListener('submit', clearDraft);
                form.addEventListener('reset', clearDraft);
            }

            loadDraft();
        });
    </script>

    <script>
        /* ========== Interacción UI / Escritura libre + validación al terminar (blur/Enter) ========== */
        document.addEventListener('DOMContentLoaded', () => {
            // F2 abre modal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.getElementById('btnAbrirModalProductos')?.click();
                }
            });

            // Filtro del modal
            const inputModal = document.getElementById('buscarProductoModal');
            if (inputModal) {
                inputModal.addEventListener('keyup', function() {
                    const filtro = this.value.toLowerCase();
                    document.querySelectorAll('#tablaProductosModal tbody tr').forEach(fila => {
                        const nombre = fila.dataset.nombre || '';
                        const presentacion = fila.dataset.presentacion || '';
                        const laboratorio = fila.dataset.laboratorio || '';
                        const categoria = fila.dataset.categoria || '';
                        const generico = fila.dataset.generico || '';
                        const lote = fila.dataset.lote || '';
                        const coincide = [nombre, presentacion, laboratorio, categoria, generico,
                            lote
                        ].some(t => t.includes(filtro));
                        fila.style.display = coincide ? '' : 'none';
                    });
                });
            }

            // Toast éxito
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

            // PREVIEW: solo hint (sin normalizar, sin errores)
            window.previewFila = function(fila) {
                if (!fila) return;
                const id = fila.getAttribute('data-id');
                const btn = document.querySelector(`.agregar-producto[data-id='${id}']`);
                const uxc = parseInt(btn.dataset.uxc || '0', 10) || 0;

                const qtyInput = fila.querySelector('.cantidad');
                const hint = fila.querySelector('.hint-cant');
                const raw = qtyInput.value;

                const parsed = parseCantidad(raw, uxc);
                if (parsed.vacio) {
                    hint.textContent = 'Total U: —';
                    qtyInput.classList.remove('is-invalid');
                    return;
                }

                hint.textContent = `Total U: ${parsed.unidadesTotales}`;
                qtyInput.classList.remove('is-invalid');
            };

            // COMMIT: normaliza, valida stock; si uxc<=1 y usó "F", alerta y normaliza a entero
            window.commitFila = function(fila) {
                if (!fila) return;
                const id = fila.getAttribute('data-id');
                const btn = document.querySelector(`.agregar-producto[data-id='${id}']`);
                const uxc = parseInt(btn.dataset.uxc || '0', 10) || 0;

                const qtyInput = fila.querySelector('.cantidad');
                const hint = fila.querySelector('.hint-cant');
                const hiddenQty = fila.querySelector('input[name="cantidades[]"]');

                const parsed = parseCantidad(qtyInput.value, uxc);
                const maxU = unidadesDisponibles(btn);

                let totU = parsed.unidadesTotales;
                let huboError = false;
                let msgError = '';

                // Regla especial: uxc<=1 NO admite "F"
                if (uxc <= 1 && parsed.hadF) {
                    huboError = true;
                    msgError = 'Este producto no maneja cajas. Ingresa solo un número entero (ej. 12).';
                }

                if (parsed.vacio || totU < 1) {
                    totU = 1;
                    huboError = true;
                    msgError = msgError || 'La cantidad mínima es 1 unidad.';
                } else if (totU > maxU) {
                    totU = maxU;
                    huboError = true;
                    msgError = msgError || `Stock insuficiente. Disponible: ${maxU} unidades.`;
                }

                if (huboError) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cantidad inválida',
                        text: msgError
                    });
                }

                // Normalización visual
                if (uxc > 1) {
                    const c = Math.floor(totU / uxc);
                    const u = totU % uxc;
                    qtyInput.value = `${c}F${u}`;
                } else {
                    qtyInput.value = String(totU); // SOLO enteros cuando uxc<=1
                }
                hint.textContent = `Total U: ${totU}`;

                // Importes + sincronización clásica
                const metrics = calcularImportes(btn, totU);
                pintarImportesFila(fila, metrics);
                if (hiddenQty) hiddenQty.value = String(totU);

                window.calcularTotales();
            };

            // Agregar producto desde el modal
            document.addEventListener('click', function(e) {
                const trigger = e.target.closest('.agregar-producto');
                if (!trigger) return;

                const btn = trigger;
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre || '';
                const tabla = document.querySelector('#tablaVenta tbody');
                const filaExistente = tabla.querySelector(`tr[data-id='${id}']`);

                const dispU = unidadesDisponibles(btn);
                if (dispU <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sin stock',
                        text: 'Este producto no tiene unidades disponibles.'
                    });
                    return;
                }

                if (filaExistente) {
                    // sumar +1 y confirmar
                    const qtyInput = filaExistente.querySelector('.cantidad');
                    const uxc = parseInt(btn.dataset.uxc || '0', 10) || 0;
                    const parsed = parseCantidad(qtyInput.value, uxc);
                    const nuevoTot = (parsed.unidadesTotales || 0) + 1;

                    if (uxc > 1) {
                        const c = Math.floor(nuevoTot / uxc);
                        const u = nuevoTot % uxc;
                        qtyInput.value = `${c}F${u}`;
                    } else {
                        qtyInput.value = String(nuevoTot); // entero
                    }
                    window.commitFila(filaExistente);
                    return;
                }

                // Nueva fila
                const uxc = parseInt(btn.dataset.uxc || '0', 10) || 0;
                const precioU = getPrecioUnidad(btn);
                const descPct = getDescPctUnidad(btn);

                const placeholder = (uxc > 1) ? "p.ej. 3F5 o F5 o 12" : "p.ej. 12";
                const uxcTxt = (uxc > 1) ? uxc : '—';

                const fila = `
      <tr data-id="${id}">
        <td>
          <input type="hidden" name="productos[]" value="${id}">
          <input type="hidden" name="unidades_venta[]" value="unidad"><!-- clásico -->
          <input type="hidden" name="cantidades[]" value="1"><!-- inicia en 1 -->
          ${nombre}
        </td>
        <td>${btn.dataset.lab || '—'}</td>
        <td>${btn.dataset.fv  || '—'}</td>
        <td><span class="badge bg-light text-dark">${uxcTxt}</span></td>
        <td>
          <input type="text" name="cantidades_raw[]" class="form-control cantidad" value="1" placeholder="${placeholder}">
          <div class="form-text small text-muted hint-cant">Total U: 1</div>
        </td>
        <td><input type="number" name="precios[]" class="form-control precio" value="${precioU.toFixed(2)}" step="0.01" readonly></td>
        <td>
          <input type="number" name="descuentos[]" class="form-control descuento-linea" value="${(precioU*(descPct/100)).toFixed(2)}" step="0.01" readonly>
          <div class="form-text small text-muted">(-${descPct}%)</div>
        </td>
        <td><span class="subtotal">${(precioU - (precioU*(descPct/100))).toFixed(2)}</span></td>
        <td>
          <button type="button" class="btn btn-danger btn-sm eliminar-producto">
            <i data-feather="trash"></i>
          </button>
        </td>
      </tr>
    `;
                tabla.insertAdjacentHTML('beforeend', fila);
                feather.replace();

                btn.disabled = true;

                // Confirmamos inmediatamente 1 unidad
                const nueva = tabla.querySelector(`tr[data-id='${id}']`);
                window.commitFila(nueva);

                showAddedToast(`"${nombre}" agregado a la venta`);
            });

            // Mientras escribe: solo hint
            document.addEventListener('input', function(e) {
                if (e.target.matches('.cantidad')) {
                    const fila = e.target.closest('tr');
                    window.previewFila(fila);
                }
            });

            // Al salir del input: validar y normalizar
            document.addEventListener('blur', function(e) {
                if (e.target.matches('.cantidad')) {
                    const fila = e.target.closest('tr');
                    window.commitFila(fila);
                }
            }, true);

            // Enter en el input = confirmar
            document.addEventListener('keydown', function(e) {
                if (e.target.matches('.cantidad') && e.key === 'Enter') {
                    e.preventDefault();
                    const fila = e.target.closest('tr');
                    window.commitFila(fila);
                    const cants = Array.from(document.querySelectorAll('#tablaVenta .cantidad'));
                    const idx = cants.indexOf(e.target);
                    if (idx >= 0 && idx < cants.length - 1) cants[idx + 1].focus();
                }
            });

            // Eliminar producto
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.eliminar-producto')) return;
                const fila = e.target.closest('tr');
                const id = fila.getAttribute('data-id');
                fila.remove();
                const boton = document.querySelector(`.agregar-producto[data-id='${id}']`);
                if (boton) boton.disabled = false;
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

            // Validación al enviar
            document.getElementById('formVenta').addEventListener('submit', function(e) {
                // Confirma todas las filas (aplica clamp y sincroniza clásicos)
                document.querySelectorAll('#tablaVenta tbody tr').forEach(f => window.commitFila(f));

                const filas = document.querySelectorAll('#tablaVenta tbody tr');
                if (filas.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Debes agregar al menos un producto a la venta.'
                    });
                    return;
                }

                let ok = true;
                filas.forEach(fila => {
                    const precio = parseFloat(fila.querySelector('.precio').value) || 0;
                    const desc = parseFloat(fila.querySelector('.descuento-linea').value) || 0;
                    const hiddenQty = fila.querySelector('input[name="cantidades[]"]');
                    const unidades = parseInt(hiddenQty?.value || '0', 10) || 0;
                    const base = (parseFloat(precio) || 0) * unidades;

                    if (desc > base) {
                        ok = false;
                        const inp = fila.querySelector('.descuento-linea');
                        inp.classList.add('is-invalid');
                        if (!inp.nextElementSibling || !inp.nextElementSibling.classList.contains(
                                'invalid-feedback')) {
                            const fb = document.createElement('div');
                            fb.className = 'invalid-feedback';
                            fb.textContent =
                            'El descuento no puede superar el importe de la línea.';
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

            // Cambio automático
            document.getElementById('pagado').addEventListener('input', function() {
                const totalTexto = document.getElementById('total').textContent.replace('S/', '').trim();
                const total = parseFloat(totalTexto) || 0;
                const pagado = parseFloat(this.value) || 0;
                const cambio = pagado - total;
                document.getElementById('cambio').textContent =
                    `S/ ${cambio >= 0 ? cambio.toFixed(2) : '0.00'}`;
            });

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

            // Botón “Predeterminado”
            (() => {
                const btnPred = document.getElementById('btnPredeterminado');
                if (btnPred) {
                    btnPred.addEventListener('click', () => {
                        const c = document.querySelector("select[name='id_cliente']");
                        if (c) c.value = "1";
                        const d = document.querySelector("select[name='id_documento']");
                        if (d) d.value = "1";
                        const p = document.querySelector("select[name='id_pago']");
                        if (p) p.value = "1";
                    });
                }
            })();

            feather.replace();
        });
    </script>

    {{-- Navegación por teclado en el modal (opcional) --}}
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
                    const b = r.querySelector('.agregar-producto');
                    return b && !b.disabled;
                });
            }

            function setActive(index) {
                const rows = visibleRows();
                rows.forEach(r => r.classList.remove('row-active'));
                if (!rows.length) {
                    selIndex = -1;
                    return;
                }
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

            modalEl?.addEventListener('shown.bs.modal', () => {
                if (filterInput) {
                    filterInput.focus();
                    filterInput.select?.();
                }
                setTimeout(selectFirst, 0);
            });
            modalEl?.addEventListener('hidden.bs.modal', () => {
                selIndex = -1;
                visibleRows().forEach(r => r.classList.remove('row-active'));
            });
            filterInput?.addEventListener('input', () => {
                setTimeout(selectFirst, 0);
            });

            modalEl?.addEventListener('keydown', (e) => {
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
                        btn.click();
                        const updated = visibleRows();
                        let next = selIndex;
                        for (let i = selIndex + 1; i < updated.length; i++) {
                            const b = updated[i].querySelector('.agregar-producto');
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
