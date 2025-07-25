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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow"
                style="border-radius: 12px; background-color: #ffffff; overflow: hidden;">

                <!-- Encabezado -->
                <div class="modal-header text-white py-3 px-4" style="background-color: #0A7ABF;">
                    <h5 class="modal-title fw-semibold mb-0" id="editarClienteLabel{{ $cliente->id }}">
                        <i class="bi bi-pencil-square me-2"></i>Editar Cliente
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- Formulario -->
                <form action="{{ route('clientes.actualizar', $cliente->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Cuerpo -->
                    <div class="modal-body px-4 py-4" style="background-color: #F9F9F9;">
                        <div class="row g-3">
                            <!-- DNI -->
                            <div class="col-md-6">
                                <label for="dni{{ $cliente->id }}" class="form-label fw-semibold text-dark">DNI <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-2" id="dni{{ $cliente->id }}"
                                    name="dni" value="{{ $cliente->dni }}" maxlength="8" pattern="\d{8}"
                                    inputmode="numeric" required>
                                <div id="dniError{{ $cliente->id }}" class="invalid-feedback d-none">
                                    El DNI debe tener 8 dígitos numéricos.
                                </div>
                            </div>

                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label for="nombre{{ $cliente->id }}" class="form-label fw-semibold text-dark">Nombre
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-2" id="nombre{{ $cliente->id }}"
                                    name="nombre" value="{{ $cliente->nombre }}" required>
                                <div id="nombreError{{ $cliente->id }}" class="invalid-feedback d-none">
                                    Por favor, ingresa el nombre del cliente.
                                </div>
                            </div>

                            <!-- Apellidos -->
                            <div class="col-md-6">
                                <label for="apellidos{{ $cliente->id }}"
                                    class="form-label fw-semibold text-dark">Apellidos</label>
                                <input type="text" class="form-control rounded-2" id="apellidos{{ $cliente->id }}"
                                    name="apellidos" value="{{ $cliente->apellidos }}">
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="telefono{{ $cliente->id }}"
                                    class="form-label fw-semibold text-dark">Teléfono</label>
                                <input type="text" class="form-control rounded-2" id="telefono{{ $cliente->id }}"
                                    name="telefono" value="{{ $cliente->telefono }}" maxlength="9" pattern="\d{9}"
                                    inputmode="numeric">
                            </div>

                            <!-- Dirección -->
                            <div class="col-12">
                                <label for="direccion{{ $cliente->id }}"
                                    class="form-label fw-semibold text-dark">Dirección</label>
                                <input type="text" class="form-control rounded-2"
                                    id="direccion{{ $cliente->id }}" name="direccion"
                                    value="{{ $cliente->direccion }}">
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer mt-3 border-0 px-4 d-flex justify-content-end bg-white"
                        style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                        <button type="button" class="btn btn-outline-secondary rounded-2 px-4 me-2"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn text-white rounded-2 px-4"
                            style="background-color: #25A6D9;">
                            Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endforeach
