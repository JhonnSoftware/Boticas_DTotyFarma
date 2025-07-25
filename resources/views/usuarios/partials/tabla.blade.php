@foreach ($usuarios as $usuario)
    <tr>
        <td class="border-top-0 px-2 py-4">
            <div class="d-flex no-block align-items-center">
                <div class="mr-3">
                    @if ($usuario->foto && Storage::exists('public/usuarios/' . $usuario->foto))
                        <img src="{{ url('storage/usuarios/' . $usuario->foto) }}" alt="Foto" class="rounded-circle"
                            width="45" height="45">
                    @else
                        <img src="{{ url('imagenes/usuario_icon.jpg') }}" alt="Sin foto" class="rounded-circle"
                            width="45" height="45">
                    @endif

                </div>
                <div class="">
                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                        {{ $usuario->name }}</h5>
                </div>
            </div>
        </td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $usuario->email }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $usuario->password }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $usuario->rol }}</td>

        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <!-- Botón de Editar -->
            <a href="#" class="text-success me-2" data-bs-toggle="modal"
                data-bs-target="#editarUsuario{{ $usuario->id }}">
                <i data-feather="edit"></i>
            </a>

            <!-- Botón de Eliminar solo si es tipo usuario -->
            @if ($usuario->rol === 'usuario')
                <form id="formEliminar{{ $usuario->id }}" action="{{ route('usuarios.destroy', $usuario->id) }}"
                    method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-link text-danger p-0 border-0"
                        onclick="confirmarEliminacion({{ $usuario->id }}, '{{ $usuario->name }}')">
                        <i data-feather="trash-2"></i>
                    </button>
                </form>
            @endif

        </td>


    </tr>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarUsuario{{ $usuario->id }}" tabindex="-1"
        aria-labelledby="editarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0 rounded-4" style="background-color: #F2F2F2;">

                <!-- Header -->
                <div class="modal-header text-white px-4 py-3" style="background-color: #0A7ABF;">
                    <h5 class="modal-title fw-bold" id="editarUsuarioLabel{{ $usuario->id }}">
                        <i class="bi bi-pencil-square me-2"></i>Editar Usuario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- Formulario -->
                <form action="{{ route('usuarios.actualizar', $usuario->id) }}" method="POST"
                    enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="modal-body px-4 py-4">
                        <div class="row g-3">

                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label for="name{{ $usuario->id }}" class="form-label fw-semibold text-dark">Nombre
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-pill shadow-sm"
                                    id="name{{ $usuario->id }}" name="name" value="{{ $usuario->name }}" required>
                                <div class="invalid-feedback" id="nameError{{ $usuario->id }}">El nombre es
                                    obligatorio.</div>
                            </div>

                            <!-- Correo -->
                            <div class="col-md-6">
                                <label for="email{{ $usuario->id }}" class="form-label fw-semibold text-dark">Correo
                                    electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-pill shadow-sm"
                                    id="email{{ $usuario->id }}" name="email" value="{{ $usuario->email }}"
                                    required>
                                <div class="invalid-feedback" id="emailError{{ $usuario->id }}">Ingresa un
                                    correo válido.</div>
                            </div>

                            <!-- Rol -->
                            <div class="col-md-6">
                                <label for="rol{{ $usuario->id }}" class="form-label fw-semibold text-dark">Rol <span
                                        class="text-danger">*</span></label>
                                <select class="form-select rounded-pill shadow-sm" id="rol{{ $usuario->id }}"
                                    name="rol" required>
                                    <option value="" disabled>Seleccionar rol
                                    </option>
                                    <option value="usuario" {{ $usuario->rol === 'usuario' ? 'selected' : '' }}>
                                        Usuario</option>
                                    <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>
                                        Administrador</option>
                                </select>
                                <div class="invalid-feedback" id="rolError{{ $usuario->id }}">Selecciona un
                                    rol.</div>
                            </div>

                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <label for="password{{ $usuario->id }}" class="form-label fw-semibold text-dark">Nueva
                                    contraseña (opcional)</label>
                                <input type="password" class="form-control rounded-pill shadow-sm"
                                    id="password{{ $usuario->id }}" name="password">
                                <div class="invalid-feedback" id="passwordError{{ $usuario->id }}">La
                                    contraseña debe ser válida.</div>
                            </div>

                            <!-- Confirmar contraseña -->
                            <div class="col-md-6">
                                <label for="password_confirmation{{ $usuario->id }}"
                                    class="form-label fw-semibold text-dark">Confirmar
                                    contraseña</label>
                                <input type="password" class="form-control rounded-pill shadow-sm"
                                    id="password_confirmation{{ $usuario->id }}" name="password_confirmation">
                            </div>

                            <!-- Foto -->
                            <div class="col-md-6">
                                <label for="foto{{ $usuario->id }}" class="form-label fw-semibold text-dark">Foto
                                    (opcional)
                                </label>
                                <input type="file" class="form-control rounded-pill shadow-sm"
                                    id="foto{{ $usuario->id }}" name="foto" accept="image/*">
                                @if ($usuario->foto && Storage::exists('public/usuarios/' . $usuario->foto))
                                    <div class="mt-2">
                                        <small class="text-muted">Foto
                                            actual:</small><br>
                                        <img src="{{ url('storage/usuarios/' . $usuario->foto) }}" alt="Foto actual"
                                            class="rounded shadow-sm" width="60" height="60">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white rounded-bottom-4 px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn text-white rounded-pill px-4"
                            style="background-color: #25A6D9;">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
