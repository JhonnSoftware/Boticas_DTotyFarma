@foreach ($clientes as $cliente)
    <tr>
        <td class="border-top-0 px-2 py-4">
            <div class="d-flex no-block align-items-center">
                <div class="mr-3">
                    <img src="{{ url('imagenes/cliente_02.png') }}" alt="user" class="rounded-circle" width="45"
                        height="45" />
                </div>
                <div class="">
                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                        {{ $cliente->dni }}</h5>
                </div>
            </div>
        </td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $cliente->nombre }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $cliente->apellidos }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $cliente->telefono }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $cliente->direccion }}</td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <span
                style="background: {{ $cliente->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                            color: {{ $cliente->estado == 'Activo' ? '#159258' : '#780909' }};
                                                            padding: 4px; border-radius:4px;">
                {{ $cliente->estado }}
            </span>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <a href="#" class="text-primary me-2"><i data-feather="eye"></i></a>

            <a href="#" class="text-success" data-bs-toggle="modal"
                data-bs-target="#editarCliente{{ $cliente->id }}">
                <i data-feather="edit"></i>
            </a>


            @if ($cliente->estado === 'Activo')
                <form action="{{ route('clientes.desactivar', $cliente->id) }}" method="POST" style="display:inline;"
                    id="form-desactivar-{{ $cliente->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-danger"
                        onclick="event.preventDefault(); confirmarDesactivacion({{ $cliente->id }});">
                        <i data-feather="trash-2"></i>
                    </a>
                </form>
            @else
                <form action="{{ route('clientes.activar', $cliente->id) }}" method="POST" style="display:inline;"
                    id="form-activar-{{ $cliente->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-success"
                        onclick="event.preventDefault(); confirmarActivacion({{ $cliente->id }});">
                        <i data-feather="refresh-ccw"></i>
                    </a>
                </form>
            @endif
        </td>

    </tr>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarCliente{{ $cliente->id }}" tabindex="-1"
        aria-labelledby="editarClienteLabel{{ $cliente->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarClienteLabel{{ $cliente->id }}">
                        Editar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('clientes.actualizar', $cliente->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="dni{{ $cliente->id }}">DNI</label>
                            <input type="text" class="form-control" id="dni{{ $cliente->id }}" name="dni"
                                value="{{ $cliente->dni }}" required maxlength="8">
                        </div>
                        <div class="form-group mb-3">
                            <label for="nombre{{ $cliente->id }}">Nombre</label>
                            <input type="text" class="form-control" id="nombre{{ $cliente->id }}" name="nombre"
                                value="{{ $cliente->nombre }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="apellidos{{ $cliente->id }}">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos{{ $cliente->id }}"
                                name="apellidos" value="{{ $cliente->apellidos }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="telefono{{ $cliente->id }}">Teléfono</label>
                            <input type="text" class="form-control" id="telefono{{ $cliente->id }}" name="telefono"
                                value="{{ $cliente->telefono }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="direccion{{ $cliente->id }}">Dirección</label>
                            <input type="text" class="form-control" id="direccion{{ $cliente->id }}"
                                name="direccion" value="{{ $cliente->direccion }}">
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
