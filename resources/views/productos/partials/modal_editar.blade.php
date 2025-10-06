{{-- resources/views/productos/partials/modal_editar.blade.php --}}

<!-- Encabezado igual que en modal_registrar -->
<div class="modal-header bg-success text-white rounded-top-4 py-3">
    <h5 class="modal-title fw-semibold">
        <i class="bi bi-pencil-square me-2"></i>Editar producto — {{ $producto->codigo }}
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"
        aria-label="Cerrar"></button>
</div>

<form id="formEditarProducto" action="{{ route('productos.actualizar', $producto->id) }}" method="POST"
    enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    @method('PUT')

    <div class="modal-body p-3" id="modalEditarBody">
        {{-- ==== Estilos compactos iguales al registrar ==== --}}
        <style>
            #modalEditarBody .form-label {
                font-weight: 600;
                font-size: .9rem;
                margin-bottom: .25rem;
            }

            #modalEditarBody .form-control,
            #modalEditarBody .form-select {
                padding: .45rem .6rem;
                font-size: .92rem;
            }

            #modalEditarBody .input-group .form-control {
                padding: .45rem .6rem;
            }

            #modalEditarBody .form-text {
                margin-top: .2rem;
                font-size: .8rem;
            }

            #modalEditarBody .invalid-feedback {
                font-size: .8rem;
            }

            #modalEditarBody .g-compact {
                --bs-gutter-x: 1rem;
                --bs-gutter-y: 1.5rem;
            }
        </style>

        {{-- Bloque de errores (por si renderizas servidor-side errores) --}}
        @if ($errors->any())
            <div class="alert alert-danger rounded-3 mb-3">
                <strong>Revisa estos campos:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            // mismos cálculos que usaste en editPartial()
            $uXB = (int) ($producto->unidades_por_blister ?? 0);
            $uXC = (int) ($producto->unidades_por_caja ?? 0);
            $cantTotal = (int) ($producto->cantidad ?? 0);
            $cantBli = (int) ($producto->cantidad_blister ?? 0);
            $cantCaj = (int) ($producto->cantidad_caja ?? 0);
            $cantidadBase = $cantTotal - ($uXB ? $cantBli * $uXB : 0) - ($uXC ? $cantCaj * $uXC : 0);
            if ($cantidadBase < 0) {
                $cantidadBase = 0;
            }
            $selCat = $producto->categorias->pluck('id')->toArray();
        @endphp

        {{-- ============================= --}}
        {{-- FILA 1 (4 campos) --}}
        {{-- ============================= --}}
        <div class="row g-compact">
            <!-- Código (solo lectura) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="codigo" class="form-label">Código</label>
                <input type="text" class="form-control rounded-3" id="codigo" name="codigo"
                    value="{{ old('codigo', $producto->codigo) }}" readonly required>
            </div>

            <!-- Descripción -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control rounded-3" id="descripcion" name="descripcion"
                    value="{{ old('descripcion', $producto->descripcion) }}" required>
                <div class="invalid-feedback">Por favor ingrese la descripción del producto.</div>
            </div>

            @php
                // IDs actuales (o de old()) como strings
                $selCat = collect(old('categorias', $producto->categorias->pluck('id')->all()))
                    ->map(fn($v) => (string) $v)
                    ->values()
                    ->all();
            @endphp

            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label mb-1">Categorías</label>
                <select class="form-select select2-categorias select2-categorias--edit" name="categorias[]" multiple
                    data-placeholder="Seleccionar categorías…" data-selected='@json($selCat)'>
                    @foreach ($categorias as $categoria)
                        @php $catId = (string) $categoria->id; @endphp
                        <option value="{{ $catId }}" {{ in_array($catId, $selCat, true) ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Quita o agrega categorías libremente.</div>
            </div>



            <!-- Clase terapéutica (opcional) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="id_clase" class="form-label">Clase terapéutica</label>
                <select class="form-select rounded-3" id="id_clase" name="id_clase">
                    <option value="">Seleccione clase</option>
                    @foreach ($clases as $cl)
                        <option value="{{ $cl->id }}" @selected(old('id_clase', $producto->id_clase) == $cl->id)>
                            {{ $cl->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- FILA 2 (4 campos) --}}
        {{-- ============================= --}}
        <div class="row g-compact">
            <!-- Genérico (opcional) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="id_generico" class="form-label">Genérico / Principio activo</label>
                <select class="form-select rounded-3" id="id_generico" name="id_generico">
                    <option value="">Seleccione genérico</option>
                    @foreach ($genericos as $ge)
                        <option value="{{ $ge->id }}" @selected(old('id_generico', $producto->id_generico) == $ge->id)>
                            {{ $ge->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Presentación -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="presentacion" class="form-label">Presentación</label>
                <input type="text" class="form-control rounded-3" id="presentacion" name="presentacion"
                    value="{{ old('presentacion', $producto->presentacion) }}" required>
                <div class="invalid-feedback">Por favor ingrese la presentación del producto.</div>
            </div>

            <!-- Laboratorio -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="laboratorio" class="form-label">Laboratorio</label>
                <input type="text" class="form-control rounded-3" id="laboratorio" name="laboratorio"
                    value="{{ old('laboratorio', $producto->laboratorio) }}" required>
                <div class="invalid-feedback">Por favor ingrese el laboratorio del producto.</div>
            </div>

            <!-- Lote -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="lote" class="form-label">Lote</label>
                <input type="text" class="form-control rounded-3" id="lote" name="lote"
                    value="{{ old('lote', $producto->lote) }}" required>
                <div class="invalid-feedback">Por favor ingrese el lote.</div>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- FILA 3 (4 campos) --}}
        {{-- ============================= --}}
        <div class="row g-compact">
            <!-- Fecha de vencimiento -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="fecha_vencimiento" class="form-label">Fecha de vencimiento</label>
                <input type="date" class="form-control rounded-3" id="fecha_vencimiento" name="fecha_vencimiento"
                    value="{{ old('fecha_vencimiento', \Illuminate\Support\Str::of($producto->fecha_vencimiento)->limit(10, '')) }}"
                    required>
                <div class="invalid-feedback">Por favor ingrese la fecha de vencimiento.</div>
            </div>

            <!-- Unidades por blíster (opcional) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="unidades_por_blister" class="form-label">Unidades por blíster</label>
                <input type="number" class="form-control rounded-3 ratio-field" id="unidades_por_blister"
                    name="unidades_por_blister" min="0"
                    value="{{ old('unidades_por_blister', ($producto->unidades_por_blister ?? 0) > 0 ? $producto->unidades_por_blister : '') }}"
                    placeholder="Ej: 10 (0 = sin definir)">
                <div class="form-text">Déjalo en 0 o vacío si no lo sabes (se guardará como <strong>sin
                        definir</strong>).</div>
            </div>

            <!-- Unidades por caja (opcional) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="unidades_por_caja" class="form-label">Unidades por caja</label>
                <input type="number" class="form-control rounded-3 ratio-field" id="unidades_por_caja"
                    name="unidades_por_caja" min="0"
                    value="{{ old('unidades_por_caja', ($producto->unidades_por_caja ?? 0) > 0 ? $producto->unidades_por_caja : '') }}"
                    placeholder="Ej: 100 (0 = sin definir)">
                <div class="form-text">Déjalo en 0 o vacío si no lo sabes (se guardará como <strong>sin
                        definir</strong>).</div>
            </div>

            <!-- Cantidad (unidades) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="cantidad" class="form-label">Cantidad (en unidades)</label>
                <input type="number" class="form-control rounded-3" id="cantidad" name="cantidad" min="0"
                    value="{{ old('cantidad', $cantidadBase) }}" required>
                <div class="invalid-feedback">Ingrese la cantidad inicial (unidades).</div>
                <small id="totalPreview" class="text-muted d-block mt-1">Total en unidades: —</small>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- FILA 4 (4 campos) --}}
        {{-- ============================= --}}
        <div class="row g-compact">
            <!-- Cantidad (blíster) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="cantidad_blister" class="form-label">Cantidad (en blíster)</label>
                <input type="number" class="form-control rounded-3" id="cantidad_blister" name="cantidad_blister"
                    min="0" value="{{ old('cantidad_blister', $producto->cantidad_blister) }}">
                <div class="form-text">Dejar vacío si no aplica.</div>
            </div>

            <!-- Cantidad (caja) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="cantidad_caja" class="form-label">Cantidad (en caja)</label>
                <input type="number" class="form-control rounded-3" id="cantidad_caja" name="cantidad_caja"
                    min="0" value="{{ old('cantidad_caja', $producto->cantidad_caja) }}">
                <div class="form-text">Dejar vacío si no aplica.</div>
            </div>

            <!-- Stock mínimo -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="stock_minimo" class="form-label">Stock mínimo (en unidades)</label>
                <input type="number" class="form-control rounded-3" id="stock_minimo" name="stock_minimo"
                    min="0" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required>
                <div class="invalid-feedback">Ingrese el stock mínimo.</div>
            </div>

            <!-- Proveedor -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="id_proveedor" class="form-label">Proveedor</label>
                <select class="form-select rounded-3" id="id_proveedor" name="id_proveedor" required>
                    <option value="" disabled
                        {{ old('id_proveedor', $producto->id_proveedor) ? '' : 'selected' }}>Seleccione proveedor
                    </option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" @selected(old('id_proveedor', $producto->id_proveedor) == $proveedor->id)>
                            {{ $proveedor->nombre }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Por favor seleccione el proveedor.</div>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- FILA 5 (VENTA) => 3 campos --}}
        {{-- ============================= --}}
        <div class="row g-compact">
            <div class="col-12 col-md-4">
                <label for="precio_venta" class="form-label">Venta (unidad) S/</label>
                <input type="number" step="0.01" class="form-control rounded-3" id="precio_venta"
                    name="precio_venta" min="0" value="{{ old('precio_venta', $producto->precio_venta) }}"
                    required>
                <div class="invalid-feedback">Ingrese el precio de venta por unidad.</div>
            </div>

            <div class="col-12 col-md-4">
                <label for="precio_venta_blister" class="form-label">Venta (blíster) S/</label>
                <input type="number" step="0.01" class="form-control rounded-3" id="precio_venta_blister"
                    name="precio_venta_blister" min="0"
                    value="{{ old('precio_venta_blister', $producto->precio_venta_blister) }}">
            </div>

            <div class="col-12 col-md-4">
                <label for="precio_venta_caja" class="form-label">Venta (caja) S/</label>
                <input type="number" step="0.01" class="form-control rounded-3" id="precio_venta_caja"
                    name="precio_venta_caja" min="0"
                    value="{{ old('precio_venta_caja', $producto->precio_venta_caja) }}">
            </div>
        </div>

        {{-- ============================= --}}
        {{-- FILA 6 (COMPRA) => 3 campos --}}
        {{-- ============================= --}}
        <div class="row g-compact">
            <div class="col-12 col-md-4">
                <label for="precio_compra" class="form-label">Compra (unidad) S/</label>
                <input type="number" step="0.01" class="form-control rounded-3" id="precio_compra"
                    name="precio_compra" min="0" value="{{ old('precio_compra', $producto->precio_compra) }}"
                    required>
                <div class="invalid-feedback">Ingrese el precio de compra por unidad.</div>
            </div>

            <div class="col-12 col-md-4">
                <label for="precio_compra_blister" class="form-label">Compra (blíster) S/</label>
                <input type="number" step="0.01" class="form-control rounded-3" id="precio_compra_blister"
                    name="precio_compra_blister" min="0"
                    value="{{ old('precio_compra_blister', $producto->precio_compra_blister) }}">
            </div>

            <div class="col-12 col-md-4">
                <label for="precio_compra_caja" class="form-label">Compra (caja) S/</label>
                <input type="number" step="0.01" class="form-control rounded-3" id="precio_compra_caja"
                    name="precio_compra_caja" min="0"
                    value="{{ old('precio_compra_caja', $producto->precio_compra_caja) }}">
            </div>
        </div>

        {{-- ============================= --}}
        {{-- FILA 7 (4 campos): Descuentos + Foto --}}
        {{-- ============================= --}}
        <div class="row g-compact">
            <!-- Descuento unidad -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="descuento" class="form-label">Descuento por unidad (%)</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" max="100"
                        class="form-control rounded-start-3" id="descuento" name="descuento"
                        value="{{ old('descuento', number_format($producto->descuento ?? 0, 2, '.', '')) }}" required>
                    <span class="input-group-text rounded-end-3">%</span>
                </div>
                <div class="invalid-feedback">Ingrese un porcentaje entre 0 y 100.</div>
            </div>

            <!-- Descuento blíster -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="descuento_blister" class="form-label">Descuento por blíster (%)</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" max="100"
                        class="form-control rounded-start-3" id="descuento_blister" name="descuento_blister"
                        value="{{ old('descuento_blister', optional($producto->descuento_blister) !== null ? number_format($producto->descuento_blister, 2, '.', '') : '') }}">
                    <span class="input-group-text rounded-end-3">%</span>
                </div>
                <div class="form-text">Dejar vacío si no aplica.</div>
            </div>

            <!-- Descuento caja -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="descuento_caja" class="form-label">Descuento por caja (%)</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" max="100"
                        class="form-control rounded-start-3" id="descuento_caja" name="descuento_caja"
                        value="{{ old('descuento_caja', optional($producto->descuento_caja) !== null ? number_format($producto->descuento_caja, 2, '.', '') : '') }}">
                    <span class="input-group-text rounded-end-3">%</span>
                </div>
                <div class="form-text">Dejar vacío si no aplica.</div>
            </div>

            <!-- Foto -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="foto" class="form-label">Foto del producto</label>
                <input type="file" class="form-control rounded-3" id="foto" name="foto"
                    accept=".jpg,.jpeg,.png,.webp">
                @if ($producto->foto)
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <img src="{{ url($producto->foto) }}" alt="foto" width="90"
                            class="rounded shadow-sm">
                        <small class="text-muted d-inline-block text-truncate"
                            style="max-width:65%">{{ $producto->foto }}</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Estado (oculto) --}}
        <input type="hidden" name="estado" value="{{ old('estado', $producto->estado) }}">
    </div>

    {{-- Footer --}}
    <div class="modal-footer mt-3 px-3 d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal"
            data-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancelar
        </button>

        <button type="submit" class="btn btn-success rounded-3">
            <i class="bi bi-save me-1"></i>Guardar cambios
        </button>
    </div>
</form>

{{-- Scripts específicos del modal de edición --}}

<script>
    (function() {
        // Busca el contenedor del parcial y el modal real que lo envuelve
        const container = document.getElementById('modalEditarBody');
        if (!container) return;

        function initSelect2Categorias() {
            if (!(window.jQuery && jQuery.fn && jQuery.fn.select2)) return;

            container.querySelectorAll('.select2-categorias--edit').forEach(el => {
                const $el = jQuery(el);
                if ($el.data('select2')) return; // evita doble init

                const $closestModal = $el.closest('.modal');
                $el.select2({
                    width: '100%',
                    placeholder: $el.data('placeholder') || 'Seleccionar categorías…',
                    allowClear: true,
                    closeOnSelect: false,
                    dropdownParent: $closestModal.length ? $closestModal : jQuery(document.body)
                });

                // Forzar preselección (IDs como string)
                try {
                    const preset = ($el.data('selected') || []).map(String);
                    if (preset.length) {
                        $el.val(preset).trigger('change.select2');
                    }
                } catch (e) {}
            });
        }

        // Ejecuta ahora (por si ya está en el DOM)…
        initSelect2Categorias();
        // …y también cuando se muestre el modal (contenido inyectado por AJAX)
        const parentModal = container.closest('.modal');
        if (parentModal) parentModal.addEventListener('shown.bs.modal', initSelect2Categorias);
    })();
</script>


@push('scripts')
    {{-- Preview de total en unidades (igual lógica que tu versión anterior) --}}
    <script>
        (function() {
            const u = document.getElementById('cantidad');
            const b = document.getElementById('cantidad_blister');
            const c = document.getElementById('cantidad_caja');
            const rub = document.getElementById('unidades_por_blister');
            const ruc = document.getElementById('unidades_por_caja');
            const out = document.getElementById('totalPreview');
            const val = el => parseInt((el && el.value) || '0', 10) || 0;

            function calc() {
                const total = val(u) +
                    (val(rub) ? val(b) * val(rub) : 0) +
                    (val(ruc) ? val(c) * val(ruc) : 0);
                if (out) out.textContent = 'Total en unidades: ' + total;
            }
            ['input', 'change'].forEach(evt => [u, b, c, rub, ruc].forEach(el => el && el.addEventListener(evt, calc)));
            calc();
        })();
    </script>



    {{-- Validación Bootstrap 5 (misma que en registrar) --}}
    <script>
        (function() {
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
@endpush
