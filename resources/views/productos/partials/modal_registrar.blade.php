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

                        {{-- 1) Información para venta rápida --}}

                        <!-- Descripción -->
                        <div class="col-md-8">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" class="form-control rounded-3" id="descripcion" name="descripcion"
                                required>
                            <div class="invalid-feedback">Por favor ingrese la descripción del producto.</div>
                        </div>
                        <!-- Código -->
                        <div class="col-md-4">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text" class="form-control rounded-3" id="codigo" name="codigo"
                                value="{{ $nuevoCodigo }}" readonly>
                        </div>

                        {{-- 2) Clasificación / Catálogos --}}

                        <!-- Categoría -->
                        <div class="col-md-4">
                            <label class="form-label mb-1">Categorías</label>
                            <select class="form-select select2-categorias" name="categorias[]" id="categorias_select"
                                multiple required data-placeholder="Seleccionar categorías...">
                                <option value=""></option> {{-- placeholder real para Select2/clear --}}
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        @if (collect(old('categorias'))->contains($categoria->id)) selected @endif>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="invalid-feedback">Seleccione al menos una categoría.</div>
                        </div>

                        <!-- Clase -->
                        <div class="col-md-4">
                            <label for="id_clase" class="form-label">Clase terapéutica</label>
                            <select class="form-select rounded-3" id="id_clase" name="id_clase">
                                <option value="">Seleccione clase</option>
                                @foreach ($clases as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Genérico -->
                        <div class="col-md-4">
                            <label for="id_generico" class="form-label">Genérico / Principio activo</label>
                            <select class="form-select rounded-3" id="id_generico" name="id_generico">
                                <option value="">Seleccione genérico</option>
                                @foreach ($genericos as $ge)
                                    <option value="{{ $ge->id }}">{{ $ge->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3) Presentación comercial --}}

                        <!-- Presentación -->
                        <div class="col-md-6">
                            <label for="presentacion" class="form-label">Presentación</label>
                            <input type="text" class="form-control rounded-3" id="presentacion" name="presentacion"
                                required>
                            <div class="invalid-feedback">Por favor ingrese la presentación del producto.</div>
                        </div>
                        <!-- Laboratorio -->
                        <div class="col-md-6">
                            <label for="laboratorio" class="form-label">Laboratorio</label>
                            <input type="text" class="form-control rounded-3" id="laboratorio" name="laboratorio"
                                required>
                            <div class="invalid-feedback">Por favor ingrese el laboratorio del producto.</div>
                        </div>

                        <!-- Lote -->
                        <div class="col-md-6">
                            <label for="lote" class="form-label">Lote</label>
                            <input type="number" class="form-control rounded-3" id="lote" name="lote" required>
                            <div class="invalid-feedback">Por favor ingrese el lote.</div>
                        </div>
                        <!-- Fecha de vencimiento -->
                        <div class="col-md-6">
                            <label for="fecha_vencimiento" class="form-label">Fecha de vencimiento</label>
                            <input type="date" class="form-control rounded-3" id="fecha_vencimiento"
                                name="fecha_vencimiento" required>
                            <div class="invalid-feedback">Por favor ingrese la fecha de vencimiento.</div>
                        </div>

                        <!-- Ratios de conversión (opcionales) -->
                        <div class="col-md-6">
                            <label for="unidades_por_blister" class="form-label">Unidades por blíster</label>
                            <input type="number" class="form-control rounded-3 ratio-field" id="unidades_por_blister"
                                name="unidades_por_blister" min="0" value="{{ old('unidades_por_blister') }}"
                                placeholder="Ej: 10 (0 = sin definir)">
                            <div class="form-text">Déjalo en 0 o vacío si no lo sabes. (Se guardará como <strong>sin
                                    definir</strong>)</div>
                        </div>

                        <div class="col-md-6">
                            <label for="unidades_por_caja" class="form-label">Unidades por caja</label>
                            <input type="number" class="form-control rounded-3 ratio-field" id="unidades_por_caja"
                                name="unidades_por_caja" min="0" value="{{ old('unidades_por_caja') }}"
                                placeholder="Ej: 100 (0 = sin definir)">
                            <div class="form-text">Déjalo en 0 o vacío si no lo sabes. (Se guardará como <strong>sin
                                    definir</strong>)</div>
                        </div>


                        <div class="col-md-4">
                            <label for="cantidad" class="form-label">Cantidad (en unidades)</label>
                            <input type="number" class="form-control rounded-3" id="cantidad" name="cantidad"
                                min="0" required>
                            <div class="invalid-feedback">Ingrese la cantidad inicial (unidades).</div>
                        </div>
                        <!-- NUEVO: Stock en blíster -->
                        <div class="col-md-4">
                            <label for="cantidad_blister" class="form-label">Cantidad (en blíster)</label>
                            <input type="number" class="form-control rounded-3" id="cantidad_blister"
                                name="cantidad_blister" min="0" value="{{ old('cantidad_blister') }}">
                            <div class="form-text">Dejar vacío si no aplica.</div>
                        </div>

                        <!-- NUEVO: Stock en caja -->
                        <div class="col-md-4">
                            <label for="cantidad_caja" class="form-label">Cantidad (en caja)</label>
                            <input type="number" class="form-control rounded-3" id="cantidad_caja"
                                name="cantidad_caja" min="0" value="{{ old('cantidad_caja') }}">
                            <div class="form-text">Dejar vacío si no aplica.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="stock_minimo" class="form-label">Stock mínimo (en unidades)</label>
                            <input type="number" class="form-control rounded-3" id="stock_minimo"
                                name="stock_minimo" min="0" required>
                            <div class="invalid-feedback">Ingrese el stock mínimo.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="stock_minimo_blister" class="form-label">Stock mínimo (en blister)</label>
                            <input type="number" class="form-control rounded-3" id="stock_minimo_blister"
                                name="stock_minimo_blister" min="0">
                            <div class="form-text">Dejar vacío si no aplica.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="stock_minimo_caja" class="form-label">Stock mínimo (en caja)</label>
                            <input type="number" class="form-control rounded-3" id="stock_minimo_caja"
                                name="stock_minimo_caja" min="0">
                            <div class="form-text">Dejar vacío si no aplica.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="descuento" class="form-label">Descuento por unidad (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100"
                                    class="form-control rounded-start-3" id="descuento" name="descuento"
                                    value="{{ old('descuento', 0) }}" required>
                                <span class="input-group-text rounded-end-3">%</span>
                            </div>
                            <div class="invalid-feedback">Ingrese un porcentaje entre 0 y 100.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="descuento_blister" class="form-label">Descuento por blíster (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100"
                                    class="form-control rounded-start-3" id="descuento_blister"
                                    name="descuento_blister" value="{{ old('descuento_blister') }}">
                                <span class="input-group-text rounded-end-3">%</span>
                            </div>
                            <div class="form-text">Dejar vacío si no aplica.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="descuento_caja" class="form-label">Descuento por caja (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100"
                                    class="form-control rounded-start-3" id="descuento_caja" name="descuento_caja"
                                    value="{{ old('descuento_caja') }}">
                                <span class="input-group-text rounded-end-3">%</span>
                            </div>
                            <div class="form-text">Dejar vacío si no aplica.</div>
                        </div>

                        {{-- 6) Precios de venta (lo que usarás en caja) --}}

                        <div class="col-md-4">
                            <label for="precio_venta" class="form-label">Venta (unidad) S/</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="precio_venta"
                                name="precio_venta" min="0" required>
                            <div class="invalid-feedback">Ingrese el precio de venta por unidad.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="precio_venta_blister" class="form-label">Venta (blíster) S/</label>
                            <input type="number" step="0.01" class="form-control rounded-3"
                                id="precio_venta_blister" name="precio_venta_blister" min="0">
                        </div>
                        <div class="col-md-4">
                            <label for="precio_venta_caja" class="form-label">Venta (caja) S/</label>
                            <input type="number" step="0.01" class="form-control rounded-3"
                                id="precio_venta_caja" name="precio_venta_caja" min="0">
                        </div>

                        {{-- 8) Precios de compra (para costos y kardex) --}}

                        <div class="col-md-4">
                            <label for="precio_compra" class="form-label">Compra (unidad) S/</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="precio_compra"
                                name="precio_compra" min="0" required>
                            <div class="invalid-feedback">Ingrese el precio de compra por unidad.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="precio_compra_blister" class="form-label">Compra (blíster) S/</label>
                            <input type="number" step="0.01" class="form-control rounded-3"
                                id="precio_compra_blister" name="precio_compra_blister" min="0">
                        </div>
                        <div class="col-md-4">
                            <label for="precio_compra_caja" class="form-label">Compra (caja) S/</label>
                            <input type="number" step="0.01" class="form-control rounded-3"
                                id="precio_compra_caja" name="precio_compra_caja" min="0">
                        </div>

                        {{-- 9) Proveedor --}}

                        <div class="col-md-6">
                            <label for="id_proveedor" class="form-label">Proveedor</label>
                            <select class="form-select rounded-3" id="id_proveedor" name="id_proveedor" required>
                                <option value="" disabled selected>Seleccione proveedor</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el proveedor.</div>
                        </div>

                        {{-- 10) Imagen --}}
                        <div class="col-md-6">
                            <label for="foto" class="form-label">Foto del producto</label>
                            <input type="file" class="form-control rounded-3" id="foto" name="foto"
                                accept=".jpg,.jpeg,.png,.webp">
                        </div>

                        {{-- Estado --}}
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
