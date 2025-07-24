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
            <a href="#" class="text-primary me-2"><i data-feather="eye"></i></a>

            <a href="#" class="text-success" data-bs-toggle="modal"
                data-bs-target="#editarUsuario{{ $usuario->id }}">
                <i data-feather="edit"></i>
            </a>

        </td>

    </tr>

    <!-- Modal de edición -->
    <!-- Modal de edición -->
    <div class="modal fade" id="editarUsuario{{ $usuario->id }}" tabindex="-1"
        aria-labelledby="editarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 20px; background-color: #F2F2F2; border: none;">
                <div class="modal-header" style="background-color: #0A7ABF; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title text-white" id="editarUsuarioLabel{{ $usuario->id }}">
                        <i class="bi bi-pencil-square me-2"></i>Editar Usuario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('usuarios.actualizar', $usuario->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body px-4 py-3">
                        <div class="form-group mb-3">
                            <label for="name{{ $usuario->id }}" class="fw-semibold">Nombre</label>
                            <input type="text" class="form-control rounded-3" id="name{{ $usuario->id }}"
                                name="name" value="{{ $usuario->name }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email{{ $usuario->id }}" class="fw-semibold">Correo</label>
                            <input type="email" class="form-control rounded-3" id="email{{ $usuario->id }}"
                                name="email" value="{{ $usuario->email }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="rol{{ $usuario->id }}" class="fw-semibold">Rol</label>
                            <select class="form-control rounded-3" id="rol{{ $usuario->id }}" name="rol" required>
                                <option value="usuario" {{ $usuario->rol === 'usuario' ? 'selected' : '' }}>
                                    Usuario</option>
                                <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>
                                    Administrador</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password{{ $usuario->id }}" class="fw-semibold">Nueva Contraseña
                                (opcional)</label>
                            <input type="password" class="form-control rounded-3" id="password{{ $usuario->id }}"
                                name="password" placeholder="Dejar en blanco si no desea cambiar">
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation{{ $usuario->id }}" class="fw-semibold">Confirmar
                                Contraseña</label>
                            <input type="password" class="form-control rounded-3"
                                id="password_confirmation{{ $usuario->id }}" name="password_confirmation"
                                placeholder="Repetir contraseña si la cambiaste">
                        </div>

                        <div class="form-group mb-3">
                            <label for="foto{{ $usuario->id }}" class="fw-semibold">Foto (opcional)</label>
                            <input type="file" class="form-control rounded-3" id="foto{{ $usuario->id }}"
                                name="foto" accept="image/*">
                            @if ($usuario->foto && Storage::exists('public/usuarios/' . $usuario->foto))
                                <div class="mt-2">
                                    <small class="text-muted">Foto actual:</small><br>
                                    <img src="{{ url('storage/usuarios/' . $usuario->foto) }}" alt="Foto actual"
                                        class="rounded shadow-sm" width="60" height="60">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer bg-light rounded-bottom-3">
                        <button type="button" class="btn btn-outline-secondary rounded-3"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-3"
                            style="background-color: #25A6D9; border: none;">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
