@foreach ($proveedores as $proveedor)
    <tr>
        <td class="border-top-0 px-2 py-4">
            <div class="d-flex no-block align-items-center">
                <div class="mr-3">
                    <img src="{{ url('imagenes/proveedor_icon.jpg') }}" alt="user" class="rounded-circle" width="45"
                        height="45" />
                </div>
                <div class="">
                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                        {{ $proveedor->nombre }}</h5>
                </div>
            </div>
        </td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->ruc }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->telefono }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->correo }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->direccion }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->contacto }}</td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <span
                style="background: {{ $proveedor->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                            color: {{ $proveedor->estado == 'Activo' ? '#159258' : '#780909' }};
                                                            padding: 4px; border-radius:4px;">
                {{ $proveedor->estado }}
            </span>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <a href="#" class="text-primary me-2"><i data-feather="eye"></i></a>

            <a href="#" class="text-success" data-bs-toggle="modal"
                data-bs-target="#editarProveedor{{ $proveedor->id }}">
                <i data-feather="edit"></i>
            </a>


            @if ($proveedor->estado === 'Activo')
                <form action="{{ route('proveedores.desactivar', $proveedor->id) }}" method="POST"
                    style="display:inline;" id="form-desactivar-{{ $proveedor->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-danger"
                        onclick="event.preventDefault(); confirmarDesactivacion({{ $proveedor->id }});">
                        <i data-feather="trash-2"></i>
                    </a>
                </form>
            @else
                <form action="{{ route('proveedores.activar', $proveedor->id) }}" method="POST"
                    style="display:inline;" id="form-activar-{{ $proveedor->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-success"
                        onclick="event.preventDefault(); confirmarActivacion({{ $proveedor->id }});">
                        <i data-feather="refresh-ccw"></i>
                    </a>
                </form>
            @endif
        </td>

    </tr>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarProveedor{{ $proveedor->id }}" tabindex="-1"
        aria-labelledby="editarProveedorLabel{{ $proveedor->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarProveedorLabel{{ $proveedor->id }}">
                        Editar Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('proveedores.actualizar', $proveedor->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="nombre{{ $proveedor->id }}">Nombre</label>
                            <input type="text" class="form-control" id="nombre{{ $proveedor->id }}" name="nombre"
                                value="{{ $proveedor->nombre }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="ruc{{ $proveedor->id }}">RUC</label>
                            <input type="text" class="form-control" id="ruc{{ $proveedor->id }}" name="ruc"
                                value="{{ $proveedor->ruc }}" required maxlength="11">
                        </div>
                        <div class="form-group mb-3">
                            <label for="telefono{{ $proveedor->id }}">Teléfono</label>
                            <input type="text" class="form-control" id="telefono{{ $proveedor->id }}"
                                name="telefono" value="{{ $proveedor->telefono }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="correo{{ $proveedor->id }}">Correo</label>
                            <input type="text" class="form-control" id="correo{{ $proveedor->id }}" name="correo"
                                value="{{ $proveedor->correo }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="direccion{{ $proveedor->id }}">Dirección</label>
                            <input type="text" class="form-control" id="direccion{{ $proveedor->id }}"
                                name="direccion" value="{{ $proveedor->direccion }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="contacto{{ $proveedor->id }}">Contacto</label>
                            <input type="text" class="form-control" id="contacto{{ $proveedor->id }}"
                                name="contacto" value="{{ $proveedor->contacto }}">
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
