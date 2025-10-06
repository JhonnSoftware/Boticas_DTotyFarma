<div class="modal fade" id="nuevoProducto" tabindex="-1" aria-labelledby="nuevoProductoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:95%;">
    <div class="modal-content shadow-lg rounded-4 border-0">

      {{-- Encabezado --}}
      <div class="modal-header bg-success text-white rounded-top-4 py-3">
        <h5 class="modal-title fw-semibold" id="nuevoProductoLabel">
          <i class="bi bi-capsule-pill me-2"></i>Registrar nuevo producto
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      {{-- Cuerpo --}}
      <div class="modal-body p-3">
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
          @csrf

          {{-- Bloque de errores para ver por qué “no guarda” --}}
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

          <style>
            /* Compacta altura y tipografía */
            #nuevoProducto .form-label { font-weight:600; font-size:.9rem; margin-bottom:.25rem; }
            #nuevoProducto .form-control, #nuevoProducto .form-select { padding:.45rem .6rem; font-size:.92rem; }
            #nuevoProducto .input-group .form-control { padding:.45rem .6rem; }
            #nuevoProducto .form-text { margin-top:.2rem; font-size:.8rem; }
            #nuevoProducto .invalid-feedback{ font-size:.8rem; }

            /* Gutters compactos con más aire vertical */
            #nuevoProducto .g-compact { --bs-gutter-x:1rem; --bs-gutter-y:1.5rem; }

            @media (min-width:1600px){
              #nuevoProducto .modal-dialog{ max-width:85% !important; }
            }
          </style>

          {{-- ============================= --}}
          {{-- FILA 1 (4 campos) --}}
          {{-- ============================= --}}
          <div class="row g-compact">
            <!-- Código -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="codigo" class="form-label">Código</label>
              <input type="text" class="form-control rounded-3" id="codigo" name="codigo" value="{{ old('codigo', $nuevoCodigo) }}" readonly>
            </div>

            <!-- Descripción -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <input type="text" class="form-control rounded-3" id="descripcion" name="descripcion" value="{{ old('descripcion') }}" required>
              <div class="invalid-feedback">Por favor ingrese la descripción del producto.</div>
            </div>

            <!-- Categorías (sin required en el front; valida el back) -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="form-label mb-1">Categorías</label>
              <select class="form-select select2-categorias" name="categorias[]" id="categorias_select" multiple data-placeholder="Seleccionar categorías...">
                <option value=""></option>
                @foreach ($categorias as $categoria)
                  <option value="{{ $categoria->id }}" @selected(collect(old('categorias', []))->contains($categoria->id))>
                    {{ $categoria->nombre }}
                  </option>
                @endforeach
              </select>
              <div class="form-text">Debe seleccionar al menos una (lo validamos en el servidor).</div>
            </div>

            <!-- Clase terapéutica -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="id_clase" class="form-label">Clase terapéutica</label>
              <select class="form-select rounded-3" id="id_clase" name="id_clase">
                <option value="">Seleccione clase</option>
                @foreach ($clases as $cl)
                  <option value="{{ $cl->id }}" @selected(old('id_clase') == $cl->id)>{{ $cl->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- ============================= --}}
          {{-- FILA 2 (4 campos) --}}
          {{-- ============================= --}}
          <div class="row g-compact">
            <!-- Genérico -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="id_generico" class="form-label">Genérico / Principio activo</label>
              <select class="form-select rounded-3" id="id_generico" name="id_generico">
                <option value="">Seleccione genérico</option>
                @foreach ($genericos as $ge)
                  <option value="{{ $ge->id }}" @selected(old('id_generico') == $ge->id)>{{ $ge->nombre }}</option>
                @endforeach
              </select>
            </div>

            <!-- Presentación -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="presentacion" class="form-label">Presentación</label>
              <input type="text" class="form-control rounded-3" id="presentacion" name="presentacion" value="{{ old('presentacion') }}" required>
              <div class="invalid-feedback">Por favor ingrese la presentación del producto.</div>
            </div>

            <!-- Laboratorio -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="laboratorio" class="form-label">Laboratorio</label>
              <input type="text" class="form-control rounded-3" id="laboratorio" name="laboratorio" value="{{ old('laboratorio') }}" required>
              <div class="invalid-feedback">Por favor ingrese el laboratorio del producto.</div>
            </div>

            <!-- Lote -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="lote" class="form-label">Lote</label>
              <input type="text" class="form-control rounded-3" id="lote" name="lote" value="{{ old('lote') }}" required>
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
              <input type="date" class="form-control rounded-3" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" required>
              <div class="invalid-feedback">Por favor ingrese la fecha de vencimiento.</div>
            </div>

            <!-- Unidades por blíster (opcional, SIN required) -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="unidades_por_blister" class="form-label">Unidades por blíster</label>
              <input type="number" class="form-control rounded-3 ratio-field" id="unidades_por_blister" name="unidades_por_blister" min="0" value="{{ old('unidades_por_blister') }}" placeholder="Ej: 10 (0 = sin definir)">
              <div class="form-text">Déjalo en 0 o vacío si no lo sabes (se guardará como <strong>sin definir</strong>).</div>
            </div>

            <!-- Unidades por caja (opcional, SIN required) -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="unidades_por_caja" class="form-label">Unidades por caja</label>
              <input type="number" class="form-control rounded-3 ratio-field" id="unidades_por_caja" name="unidades_por_caja" min="0" value="{{ old('unidades_por_caja') }}" placeholder="Ej: 100 (0 = sin definir)">
              <div class="form-text">Déjalo en 0 o vacío si no lo sabes (se guardará como <strong>sin definir</strong>).</div>
            </div>

            <!-- Cantidad (unidades) -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="cantidad" class="form-label">Cantidad (en unidades)</label>
              <input type="number" class="form-control rounded-3" id="cantidad" name="cantidad" min="0" value="{{ old('cantidad') }}" required>
              <div class="invalid-feedback">Ingrese la cantidad inicial (unidades).</div>
            </div>
          </div>

          {{-- ============================= --}}
          {{-- FILA 4 (4 campos) --}}
          {{-- ============================= --}}
          <div class="row g-compact">
            <!-- Cantidad (blíster) -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="cantidad_blister" class="form-label">Cantidad (en blíster)</label>
              <input type="number" class="form-control rounded-3" id="cantidad_blister" name="cantidad_blister" min="0" value="{{ old('cantidad_blister') }}">
              <div class="form-text">Dejar vacío si no aplica.</div>
            </div>

            <!-- Cantidad (caja) -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="cantidad_caja" class="form-label">Cantidad (en caja)</label>
              <input type="number" class="form-control rounded-3" id="cantidad_caja" name="cantidad_caja" min="0" value="{{ old('cantidad_caja') }}">
              <div class="form-text">Dejar vacío si no aplica.</div>
            </div>

            <!-- Stock mínimo -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="stock_minimo" class="form-label">Stock mínimo (en unidades)</label>
              <input type="number" class="form-control rounded-3" id="stock_minimo" name="stock_minimo" min="0" value="{{ old('stock_minimo') }}" required>
              <div class="invalid-feedback">Ingrese el stock mínimo.</div>
            </div>

            <!-- Proveedor -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="id_proveedor" class="form-label">Proveedor</label>
              <select class="form-select rounded-3" id="id_proveedor" name="id_proveedor" required>
                <option value="" disabled {{ old('id_proveedor') ? '' : 'selected' }}>Seleccione proveedor</option>
                @foreach ($proveedores as $proveedor)
                  <option value="{{ $proveedor->id }}" @selected(old('id_proveedor') == $proveedor->id)>{{ $proveedor->nombre }}</option>
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
              <input type="number" step="0.01" class="form-control rounded-3" id="precio_venta" name="precio_venta" min="0" value="{{ old('precio_venta') }}" required>
              <div class="invalid-feedback">Ingrese el precio de venta por unidad.</div>
            </div>

            <div class="col-12 col-md-4">
              <label for="precio_venta_blister" class="form-label">Venta (blíster) S/</label>
              <input type="number" step="0.01" class="form-control rounded-3" id="precio_venta_blister" name="precio_venta_blister" min="0" value="{{ old('precio_venta_blister') }}">
            </div>

            <div class="col-12 col-md-4">
              <label for="precio_venta_caja" class="form-label">Venta (caja) S/</label>
              <input type="number" step="0.01" class="form-control rounded-3" id="precio_venta_caja" name="precio_venta_caja" min="0" value="{{ old('precio_venta_caja') }}">
            </div>
          </div>

          {{-- ============================= --}}
          {{-- FILA 6 (COMPRA) => 3 campos --}}
          {{-- ============================= --}}
          <div class="row g-compact">
            <div class="col-12 col-md-4">
              <label for="precio_compra" class="form-label">Compra (unidad) S/</label>
              <input type="number" step="0.01" class="form-control rounded-3" id="precio_compra" name="precio_compra" min="0" value="{{ old('precio_compra') }}" required>
              <div class="invalid-feedback">Ingrese el precio de compra por unidad.</div>
            </div>

            <div class="col-12 col-md-4">
              <label for="precio_compra_blister" class="form-label">Compra (blíster) S/</label>
              <input type="number" step="0.01" class="form-control rounded-3" id="precio_compra_blister" name="precio_compra_blister" min="0" value="{{ old('precio_compra_blister') }}">
            </div>

            <div class="col-12 col-md-4">
              <label for="precio_compra_caja" class="form-label">Compra (caja) S/</label>
              <input type="number" step="0.01" class="form-control rounded-3" id="precio_compra_caja" name="precio_compra_caja" min="0" value="{{ old('precio_compra_caja') }}">
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
                <input type="number" step="0.01" min="0" max="100" class="form-control rounded-start-3" id="descuento" name="descuento" value="{{ old('descuento', 0) }}" required>
                <span class="input-group-text rounded-end-3">%</span>
              </div>
              <div class="invalid-feedback">Ingrese un porcentaje entre 0 y 100.</div>
            </div>

            <!-- Descuento blíster -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="descuento_blister" class="form-label">Descuento por blíster (%)</label>
              <div class="input-group">
                <input type="number" step="0.01" min="0" max="100" class="form-control rounded-start-3" id="descuento_blister" name="descuento_blister" value="{{ old('descuento_blister') }}">
                <span class="input-group-text rounded-end-3">%</span>
              </div>
              <div class="form-text">Dejar vacío si no aplica.</div>
            </div>

            <!-- Descuento caja -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="descuento_caja" class="form-label">Descuento por caja (%)</label>
              <div class="input-group">
                <input type="number" step="0.01" min="0" max="100" class="form-control rounded-start-3" id="descuento_caja" name="descuento_caja" value="{{ old('descuento_caja') }}">
                <span class="input-group-text rounded-end-3">%</span>
              </div>
              <div class="form-text">Dejar vacío si no aplica.</div>
            </div>

            <!-- Foto -->
            <div class="col-12 col-sm-6 col-lg-3">
              <label for="foto" class="form-label">Foto del producto</label>
              <input type="file" class="form-control rounded-3" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp">
            </div>
          </div>

          {{-- Estado --}}
          <input type="hidden" name="estado" value="Activo">

          {{-- Footer --}}
          <div class="modal-footer mt-3 px-0">
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

{{-- Scripts específicos del modal (opcional si ya los tienes globales) --}}
@push('scripts')
  {{-- Re-abrir el modal si hay errores o si quieres mostrar el success dentro del modal --}}
  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('nuevoProducto');
        if (window.bootstrap && bootstrap.Modal) {
          bootstrap.Modal.getOrCreateInstance(el).show();
        } else if (window.jQuery) {
          $('#nuevoProducto').modal('show');
        }
      });
    </script>
  @endif

  {{-- Select2 dentro del modal (si usas Select2) --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const $modal = document.getElementById('nuevoProducto');
      const init = () => {
        const els = $modal.querySelectorAll('.select2-categorias');
        if (window.jQuery && window.jQuery.fn && jQuery.fn.select2) {
          els.forEach(el => {
            const $el = jQuery(el);
            if ($el.data('select2')) return;
            $el.select2({
              width: '100%',
              placeholder: $el.data('placeholder') || 'Seleccionar categorías…',
              allowClear: true,
              closeOnSelect: false,
              dropdownParent: jQuery('#nuevoProducto')
            });
          });
        }
      };
      // Inicializa cuando el modal se abre (y por si ya está en el DOM)
      init();
      $modal.addEventListener('shown.bs.modal', init);
    });
  </script>

  {{-- Validación Bootstrap 5 básica (si no la tienes global) --}}
  <script>
    (function () {
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
