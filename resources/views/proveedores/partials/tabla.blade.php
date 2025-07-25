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

    <!-- Modal: Editar proveedor -->
    <div class="modal fade" id="editarProveedor{{ $proveedor->id }}" tabindex="-1"
        aria-labelledby="editarProveedorLabel{{ $proveedor->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow"
                style="border-radius: 12px; background-color: #ffffff; overflow: hidden;">

                <!-- Encabezado -->
                <div class="modal-header text-white py-3 px-4" style="background-color: #0A7ABF;">
                    <h5 class="modal-title fw-semibold mb-0" id="editarProveedorLabel{{ $proveedor->id }}">
                        <i class="bi bi-pencil-square me-2"></i>Editar proveedor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('proveedores.actualizar', $proveedor->id) }}" method="POST"
                    class="needs-validation was-validated" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Cuerpo -->
                    <div class="modal-body px-4 py-4" style="background-color: #F9F9F9;">
                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label for="nombre{{ $proveedor->id }}"
                                    class="form-label fw-semibold text-dark">Nombre</label>
                                <input type="text" class="form-control rounded-3" id="nombre{{ $proveedor->id }}"
                                    name="nombre" value="{{ $proveedor->nombre }}" required>
                                <div class="invalid-feedback" id="nombreError{{ $proveedor->id ?? '' }}">
                                    Se debe registrar un nombre del proveedor.
                                </div>
                            </div>

                            <!-- RUC -->
                            <div class="col-md-6">
                                <label for="ruc{{ $proveedor->id }}"
                                    class="form-label fw-semibold text-dark">RUC</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control rounded-3" id="ruc{{ $proveedor->id }}"
                                        name="ruc" value="{{ $proveedor->ruc }}" maxlength="11" pattern="\d{11}"
                                        inputmode="numeric" required>
                                    <div class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    </div>
                                    <div class="invalid-feedback" id="rucError{{ $proveedor->id ?? '' }}">
                                        El RUC debe contener exactamente 11 dígitos
                                        numéricos.
                                    </div>
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="telefono{{ $proveedor->id }}"
                                    class="form-label fw-semibold text-dark">Teléfono</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control rounded-3"
                                        id="telefono{{ $proveedor->id }}" name="telefono"
                                        value="{{ $proveedor->telefono }}" maxlength="9" pattern="\d{9}"
                                        inputmode="numeric">
                                    <div class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    </div>
                                    <div class="invalid-feedback">
                                        El teléfono debe tener exactamente 9 dígitos
                                        numéricos (opcional).
                                    </div>
                                </div>
                            </div>

                            <!-- Correo -->
                            <div class="col-md-6">
                                <label for="correo{{ $proveedor->id }}"
                                    class="form-label fw-semibold text-dark">Correo</label>
                                <div class="position-relative">
                                    <input type="email" class="form-control rounded-3"
                                        id="correo{{ $proveedor->id }}" name="correo"
                                        value="{{ $proveedor->correo }}">
                                    <div class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    </div>
                                    <div class="invalid-feedback">
                                        Por favor ingrese un correo electrónico válido
                                        (opcional)
                                        .
                                    </div>
                                </div>
                            </div>

                            <!-- Dirección -->
                            <div class="col-12">
                                <label for="direccion{{ $proveedor->id }}"
                                    class="form-label fw-semibold text-dark">Dirección</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control rounded-3"
                                        id="direccion{{ $proveedor->id }}" name="direccion"
                                        value="{{ $proveedor->direccion }}">
                                    <div class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    </div>
                                    <div class="invalid-feedback">
                                        Por favor ingrese la dirección del proveedor
                                        (opcional).
                                    </div>
                                </div>
                            </div>

                            <!-- Contacto -->
                            <div class="col-md-6">
                                <label for="contacto{{ $proveedor->id }}"
                                    class="form-label fw-semibold text-dark">Contacto</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control rounded-3"
                                        id="contacto{{ $proveedor->id }}" name="contacto"
                                        value="{{ $proveedor->contacto }}">
                                    <div class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    </div>
                                    <div class="invalid-feedback">
                                        Por favor ingrese el nombre del contacto del
                                        proveedor (opcional).
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer mt-3 border-0 px-4 d-flex justify-content-end bg-white"
                        style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4 me-2"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn text-white rounded-3 px-4"
                            style="background-color: #25A6D9;">
                            Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endforeach
