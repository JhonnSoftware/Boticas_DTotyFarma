{{-- resources/views/productos/partials/modal_editar.blade.php --}}
<div class="modal-header bg-primary text-white">
  <h5 class="modal-title">Editar producto — {{ $producto->codigo }}</h5>
  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
</div>

<form id="formEditarProducto"
      action="{{ route('productos.actualizar', $producto->id) }}"
      method="POST" enctype="multipart/form-data" novalidate>
  @csrf
  @method('PUT')

  <div class="modal-body">
    <div class="row g-4">
      {{-- 1) Info principal --}}
      <div class="col-md-8">
        <label class="form-label" for="descripcion">Descripción</label>
        <input id="descripcion" type="text" name="descripcion"
               class="form-control rounded-3"
               value="{{ old('descripcion', $producto->descripcion) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="codigo">Código</label>
        <input id="codigo" type="text" name="codigo"
               class="form-control rounded-3"
               value="{{ old('codigo', $producto->codigo) }}" readonly required>
      </div>

      {{-- 2) Catálogos --}}
      <div class="col-md-4">
        <label class="form-label mb-1" for="categorias">Categorías</label>
        @php $selCat = $producto->categorias->pluck('id')->toArray(); @endphp
        <select id="categorias" class="form-select select2-categorias-edit"
                name="categorias[]" multiple
                data-placeholder="Seleccionar categorías..."
                data-modal="#modalEditar" required>
          @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}" {{ in_array($categoria->id, $selCat) ? 'selected' : '' }}>
              {{ $categoria->nombre }}
            </option>
          @endforeach
        </select>
        <div class="invalid-feedback">Seleccione al menos una categoría.</div>
      </div>

      <div class="col-md-4">
        <label class="form-label" for="id_clase">Clase terapéutica</label>
        <select id="id_clase" name="id_clase" class="form-select rounded-3">
          <option value="">Seleccione clase</option>
          @foreach ($clases as $cl)
            <option value="{{ $cl->id }}" @selected(old('id_clase', $producto->id_clase)==$cl->id)>
              {{ $cl->nombre }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label" for="id_generico">Genérico / Principio activo</label>
        <select id="id_generico" name="id_generico" class="form-select rounded-3">
          <option value="">Seleccione genérico</option>
          @foreach ($genericos as $ge)
            <option value="{{ $ge->id }}" @selected(old('id_generico', $producto->id_generico)==$ge->id)>
              {{ $ge->nombre }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- 3) Presentación y datos comerciales --}}
      <div class="col-md-6">
        <label class="form-label" for="presentacion">Presentación</label>
        <input id="presentacion" type="text" name="presentacion"
               class="form-control rounded-3"
               value="{{ old('presentacion', $producto->presentacion) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="laboratorio">Laboratorio</label>
        <input id="laboratorio" type="text" name="laboratorio"
               class="form-control rounded-3"
               value="{{ old('laboratorio', $producto->laboratorio) }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="lote">Lote</label>
        <input id="lote" type="text" name="lote"
               class="form-control rounded-3"
               value="{{ old('lote', $producto->lote) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="fecha_vencimiento">Fecha de vencimiento</label>
        <input id="fecha_vencimiento" type="date" name="fecha_vencimiento"
               class="form-control rounded-3"
               value="{{ old('fecha_vencimiento', \Illuminate\Support\Str::of($producto->fecha_vencimiento)->limit(10,'')) }}"
               required>
      </div>

      {{-- 4) Ratios --}}
      <div class="col-md-6">
        <label class="form-label" for="unidades_por_blister">Unidades por blíster</label>
        <input id="unidades_por_blister" type="number" name="unidades_por_blister"
               class="form-control rounded-3" min="0"
               value="{{ old('unidades_por_blister', ($producto->unidades_por_blister ?? 0) > 0 ? $producto->unidades_por_blister : '') }}"
               placeholder="Ej: 10 (0 o vacío = sin definir)">
      </div>
      <div class="col-md-6">
        <label class="form-label" for="unidades_por_caja">Unidades por caja</label>
        <input id="unidades_por_caja" type="number" name="unidades_por_caja"
               class="form-control rounded-3" min="0"
               value="{{ old('unidades_por_caja', ($producto->unidades_por_caja ?? 0) > 0 ? $producto->unidades_por_caja : '') }}"
               placeholder="Ej: 12 (0 o vacío = sin definir)">
      </div>

      {{-- 5) Stock y mínimos --}}
      <div class="col-md-4">
        <label class="form-label" for="cantidad">Cantidad (unidades)</label>
        <input id="cantidad" type="number" name="cantidad" min="0"
               class="form-control rounded-3" value="{{ old('cantidad', $producto->cantidad) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="cantidad_blister">Cantidad (blíster)</label>
        <input id="cantidad_blister" type="number" name="cantidad_blister" min="0"
               class="form-control rounded-3" value="{{ old('cantidad_blister', $producto->cantidad_blister) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label" for="cantidad_caja">Cantidad (caja)</label>
        <input id="cantidad_caja" type="number" name="cantidad_caja" min="0"
               class="form-control rounded-3" value="{{ old('cantidad_caja', $producto->cantidad_caja) }}">
      </div>

      <div class="col-md-4">
        <label class="form-label" for="stock_minimo">Stock mínimo (unidades)</label>
        <input id="stock_minimo" type="number" name="stock_minimo" min="0"
               class="form-control rounded-3" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="stock_minimo_blister">Stock mínimo (blíster)</label>
        <input id="stock_minimo_blister" type="number" name="stock_minimo_blister" min="0"
               class="form-control rounded-3" value="{{ old('stock_minimo_blister', $producto->stock_minimo_blister) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label" for="stock_minimo_caja">Stock mínimo (caja)</label>
        <input id="stock_minimo_caja" type="number" name="stock_minimo_caja" min="0"
               class="form-control rounded-3" value="{{ old('stock_minimo_caja', $producto->stock_minimo_caja) }}">
      </div>

      {{-- 6) Descuentos --}}
      <div class="col-md-4">
        <label class="form-label" for="descuento">Descuento unidad (%)</label>
        <div class="input-group">
          <input id="descuento" type="number" step="0.01" min="0" max="100"
                 name="descuento" class="form-control rounded-start-3"
                 value="{{ old('descuento', number_format($producto->descuento ?? 0, 2, '.', '')) }}" required>
          <span class="input-group-text rounded-end-3">%</span>
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="descuento_blister">Descuento blíster (%)</label>
        <div class="input-group">
          <input id="descuento_blister" type="number" step="0.01" min="0" max="100"
                 name="descuento_blister" class="form-control rounded-start-3"
                 value="{{ old('descuento_blister', optional($producto->descuento_blister) !== null ? number_format($producto->descuento_blister, 2, '.', '') : '') }}">
          <span class="input-group-text rounded-end-3">%</span>
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="descuento_caja">Descuento caja (%)</label>
        <div class="input-group">
          <input id="descuento_caja" type="number" step="0.01" min="0" max="100"
                 name="descuento_caja" class="form-control rounded-start-3"
                 value="{{ old('descuento_caja', optional($producto->descuento_caja) !== null ? number_format($producto->descuento_caja, 2, '.', '') : '') }}">
          <span class="input-group-text rounded-end-3">%</span>
        </div>
      </div>

      {{-- 7) Precios de venta --}}
      <div class="col-md-4">
        <label class="form-label" for="precio_venta">Venta (unidad) S/</label>
        <input id="precio_venta" type="number" step="0.01" min="0" name="precio_venta"
               class="form-control rounded-3" value="{{ old('precio_venta', $producto->precio_venta) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="precio_venta_blister">Venta (blíster) S/</label>
        <input id="precio_venta_blister" type="number" step="0.01" min="0"
               name="precio_venta_blister" class="form-control rounded-3"
               value="{{ old('precio_venta_blister', $producto->precio_venta_blister) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label" for="precio_venta_caja">Venta (caja) S/</label>
        <input id="precio_venta_caja" type="number" step="0.01" min="0"
               name="precio_venta_caja" class="form-control rounded-3"
               value="{{ old('precio_venta_caja', $producto->precio_venta_caja) }}">
      </div>

      {{-- 8) Precios de compra --}}
      <div class="col-md-4">
        <label class="form-label" for="precio_compra">Compra (unidad) S/</label>
        <input id="precio_compra" type="number" step="0.01" min="0"
               name="precio_compra" class="form-control rounded-3"
               value="{{ old('precio_compra', $producto->precio_compra) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="precio_compra_blister">Compra (blíster) S/</label>
        <input id="precio_compra_blister" type="number" step="0.01" min="0"
               name="precio_compra_blister" class="form-control rounded-3"
               value="{{ old('precio_compra_blister', $producto->precio_compra_blister) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label" for="precio_compra_caja">Compra (caja) S/</label>
        <input id="precio_compra_caja" type="number" step="0.01" min="0"
               name="precio_compra_caja" class="form-control rounded-3"
               value="{{ old('precio_compra_caja', $producto->precio_compra_caja) }}">
      </div>

      {{-- 9) Proveedor --}}
      <div class="col-md-6">
        <label class="form-label" for="id_proveedor">Proveedor</label>
        <select id="id_proveedor" name="id_proveedor" class="form-select rounded-3" required>
          <option value="" disabled>Seleccione proveedor</option>
          @foreach ($proveedores as $prov)
            <option value="{{ $prov->id }}" @selected(old('id_proveedor', $producto->id_proveedor)==$prov->id)>
              {{ $prov->nombre }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- 10) Imagen --}}
      <div class="col-md-6">
        <label class="form-label" for="foto">Foto del producto</label>
        <input id="foto" type="file" name="foto" class="form-control rounded-3" accept=".jpg,.jpeg,.png,.webp">
        @if ($producto->foto)
          <div class="mt-2 d-flex align-items-center gap-2">
            <img src="{{ url($producto->foto) }}" alt="foto" width="90" class="rounded shadow-sm">
            <small class="text-muted d-inline-block">{{ $producto->foto }}</small>
          </div>
        @endif
      </div>

      {{-- 11) Estado (oculto) --}}
      <input type="hidden" name="estado" value="{{ old('estado', $producto->estado) }}">
    </div>
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-primary">Guardar cambios</button>
  </div>
</form>
