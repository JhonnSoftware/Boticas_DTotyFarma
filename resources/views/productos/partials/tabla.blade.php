@foreach ($productos as $producto)
    <tr>
        <td class="border-top-0 px-2 py-4">
            <div class="d-flex no-block align-items-center">
                <div class="mr-3">
                    <img src="{{ url('imagenes/producto_icon1.png') }}" alt="user" class="rounded-circle" width="45"
                        height="45" />
                </div>
                <div class="">
                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                        {{ $producto->codigo }}</h5>
                </div>
            </div>
        </td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->descripcion }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->presentacion }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->laboratorio }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->lote }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->cantidad }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->stock_minimo }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->descuento }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->fecha_vencimiento }}
        </td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->precio_compra }}
        </td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->precio_venta }}</td>
        <td>
            <img src="{{ url($producto->foto) }}" alt="Foto del producto" width="70">

        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <span
                style="background: {{ $producto->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                            color: {{ $producto->estado == 'Activo' ? '#159258' : '#780909' }};
                                                            padding: 4px; border-radius:4px;">
                {{ $producto->estado }}
            </span>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <a href="#" class="text-primary me-2"><i data-feather="eye"></i></a>

            <a href="#" class="text-success" data-bs-toggle="modal"
                data-bs-target="#editarProducto{{ $producto->id }}">
                <i data-feather="edit"></i>
            </a>


            @if ($producto->estado === 'Activo')
                <form action="{{ route('productos.desactivar', $producto->id) }}" method="POST"
                    style="display:inline;" id="form-desactivar-{{ $producto->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-danger"
                        onclick="event.preventDefault(); confirmarDesactivacion({{ $producto->id }});">
                        <i data-feather="trash-2"></i>
                    </a>
                </form>
            @else
                <form action="{{ route('productos.activar', $producto->id) }}" method="POST" style="display:inline;"
                    id="form-activar-{{ $producto->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-success"
                        onclick="event.preventDefault(); confirmarActivacion({{ $producto->id }});">
                        <i data-feather="refresh-ccw"></i>
                    </a>
                </form>
            @endif
        </td>

    </tr>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarProducto{{ $producto->id }}" tabindex="-1"
        aria-labelledby="editarProductoLabel{{ $producto->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- Tamaño grande para que todo entre -->
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarProductoLabel{{ $producto->id }}">Editar Producto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('productos.actualizar', $producto->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-4">
                            <!-- Código -->
                            <div class="col-md-6">
                                <label for="codigo{{ $producto->id }}" class="form-label">Código</label>
                                <input type="text" class="form-control rounded-3" id="codigo{{ $producto->id }}"
                                    name="codigo" value="{{ $producto->codigo }}" required>
                            </div>

                            <!-- Descripción -->
                            <div class="col-md-6">
                                <label for="descripcion{{ $producto->id }}" class="form-label">Descripción</label>
                                <input type="text" class="form-control rounded-3"
                                    id="descripcion{{ $producto->id }}" name="descripcion"
                                    value="{{ $producto->descripcion }}" required>
                            </div>

                            <!-- Presentación -->
                            <div class="col-md-6">
                                <label for="presentacion{{ $producto->id }}" class="form-label">Presentación</label>
                                <input type="text" class="form-control rounded-3"
                                    id="presentacion{{ $producto->id }}" name="presentacion"
                                    value="{{ $producto->presentacion }}" required>
                            </div>

                            <!-- Laboratorio -->
                            <div class="col-md-6">
                                <label for="laboratorio{{ $producto->id }}" class="form-label">Laboratorio</label>
                                <input type="text" class="form-control rounded-3"
                                    id="laboratorio{{ $producto->id }}" name="laboratorio"
                                    value="{{ $producto->laboratorio }}" required>
                            </div>

                            <!-- Lote -->
                            <div class="col-md-4">
                                <label for="lote{{ $producto->id }}" class="form-label">Lote</label>
                                <input type="number" class="form-control rounded-3" id="lote{{ $producto->id }}"
                                    name="lote" value="{{ $producto->lote }}" required>
                            </div>

                            <!-- Cantidad -->
                            <div class="col-md-4">
                                <label for="cantidad{{ $producto->id }}" class="form-label">Cantidad</label>
                                <input type="number" class="form-control rounded-3"
                                    id="cantidad{{ $producto->id }}" name="cantidad" min="0"
                                    value="{{ $producto->cantidad }}" required>
                            </div>

                            <!-- Stock mínimo -->
                            <div class="col-md-4">
                                <label for="stock_minimo{{ $producto->id }}" class="form-label">Stock mínimo</label>
                                <input type="number" class="form-control rounded-3"
                                    id="stock_minimo{{ $producto->id }}" name="stock_minimo" min="0"
                                    value="{{ $producto->stock_minimo }}" required>
                            </div>

                            <!-- Descuento -->
                            <div class="col-md-6">
                                <label for="descuento{{ $producto->id }}" class="form-label">Descuento (%)</label>
                                <input type="number" step="0.01" class="form-control rounded-3"
                                    id="descuento{{ $producto->id }}" name="descuento" min="0"
                                    value="{{ $producto->descuento }}" required>
                            </div>

                            <!-- Fecha de vencimiento -->
                            <div class="col-md-6">
                                <label for="fecha_vencimiento{{ $producto->id }}" class="form-label">Fecha de
                                    vencimiento</label>
                                <input type="date" class="form-control rounded-3"
                                    id="fecha_vencimiento{{ $producto->id }}" name="fecha_vencimiento"
                                    value="{{ $producto->fecha_vencimiento }}" required>
                            </div>

                            <!-- Precio compra -->
                            <div class="col-md-6">
                                <label for="precio_compra{{ $producto->id }}" class="form-label">Precio de compra
                                    (S/)</label>
                                <input type="number" step="0.01" class="form-control rounded-3"
                                    id="precio_compra{{ $producto->id }}" name="precio_compra" min="0"
                                    value="{{ $producto->precio_compra }}" required>
                            </div>

                            <!-- Precio venta -->
                            <div class="col-md-6">
                                <label for="precio_venta{{ $producto->id }}" class="form-label">Precio de venta
                                    (S/)</label>
                                <input type="number" step="0.01" class="form-control rounded-3"
                                    id="precio_venta{{ $producto->id }}" name="precio_venta" min="0"
                                    value="{{ $producto->precio_venta }}" required>
                            </div>

                            <!-- Proveedor -->
                            <div class="col-md-6">
                                <label for="id_proveedor{{ $producto->id }}" class="form-label">Proveedor</label>
                                <select class="form-select rounded-3" id="id_proveedor{{ $producto->id }}"
                                    name="id_proveedor" required>
                                    <option value="" disabled>Seleccione
                                        proveedor</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}"
                                            {{ $producto->id_proveedor == $proveedor->id ? 'selected' : '' }}>
                                            {{ $proveedor->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Categoría -->
                            <div class="col-md-6">
                                <label for="id_categoria{{ $producto->id }}" class="form-label">Categoría</label>
                                <select class="form-select rounded-3" id="id_categoria{{ $producto->id }}"
                                    name="id_categoria" required>
                                    <option value="" disabled>Seleccione
                                        categoría</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}"
                                            {{ $producto->id_categoria == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Foto -->
                            <div class="col-12">
                                <label for="foto{{ $producto->id }}" class="form-label">Foto del producto</label>
                                <input type="file" class="form-control rounded-3" id="foto{{ $producto->id }}"
                                    name="foto" accept=".jpg,.jpeg,.png,.webp">
                                @if ($producto->foto)
                                    <small class="text-muted">Actual:
                                        {{ $producto->foto }}</small>
                                @endif
                            </div>

                            <!-- Estado oculto -->
                            <input type="hidden" name="estado" value="Activo">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar
                            Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
