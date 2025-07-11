@foreach ($usuarios as $usuario)
    <tr>
        <td class="border-top-0 px-2 py-4">
            <div class="d-flex no-block align-items-center">
                <div class="mr-3">
                    <img src="{{ url('imagenes/usuario_icon.jpg') }}" alt="user" class="rounded-circle" width="45"
                        height="45" />
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
    <div class="modal fade" id="editarUsuario{{ $usuario->id }}" tabindex="-1"
        aria-labelledby="editarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarUsuarioLabel{{ $usuario->id }}">
                        Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('usuarios.actualizar', $usuario->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name{{ $usuario->id }}">Nombre</label>
                            <input type="text" class="form-control" id="name{{ $usuario->id }}" name="name"
                                value="{{ $usuario->name }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email{{ $usuario->id }}">Correo</label>
                            <input type="email" class="form-control" id="email{{ $usuario->id }}" name="email"
                                value="{{ $usuario->email }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="rol{{ $usuario->id }}">Rol</label>
                            <select class="form-control" id="rol{{ $usuario->id }}" name="rol" required>
                                <option value="usuario" {{ $usuario->rol === 'usuario' ? 'selected' : '' }}>
                                    Usuario</option>
                                <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>
                                    Administrador</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password{{ $usuario->id }}">Nueva Contraseña
                                (opcional)
                            </label>
                            <input type="password" class="form-control" id="password{{ $usuario->id }}"
                                name="password" placeholder="Dejar en blanco si no desea cambiar">
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation{{ $usuario->id }}">Confirmar
                                Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation{{ $usuario->id }}"
                                name="password_confirmation" placeholder="Repetir contraseña si la cambiaste">
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
