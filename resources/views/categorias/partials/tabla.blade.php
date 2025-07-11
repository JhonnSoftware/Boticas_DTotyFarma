@foreach ($categorias as $categoria)
    <tr>
        <td class="border-top-0 px-2 py-4">
            <div class="d-flex no-block align-items-center">
                <div class="mr-3">
                    <img src="{{ url('imagenes/categoria_icon3.png') }}" alt="user" class="rounded-circle"
                        width="45" height="45" />
                </div>
                <div class="">
                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                        {{ $categoria->nombre }}</h5>
                </div>
            </div>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <span
                style="background: {{ $categoria->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                            color: {{ $categoria->estado == 'Activo' ? '#159258' : '#780909' }};
                                                            padding: 4px; border-radius:4px;">
                {{ $categoria->estado }}
            </span>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <a href="#" class="text-primary me-2"><i data-feather="eye"></i></a>

            <a href="#" class="text-success" data-bs-toggle="modal"
                data-bs-target="#editarCategoria{{ $categoria->id }}">
                <i data-feather="edit"></i>
            </a>


            @if ($categoria->estado === 'Activo')
                <form action="{{ route('categorias.desactivar', $categoria->id) }}" method="POST"
                    style="display:inline;" id="form-desactivar-{{ $categoria->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-danger"
                        onclick="event.preventDefault(); confirmarDesactivacion({{ $categoria->id }});">
                        <i data-feather="trash-2"></i>
                    </a>
                </form>
            @else
                <form action="{{ route('categorias.activar', $categoria->id) }}" method="POST" style="display:inline;"
                    id="form-activar-{{ $categoria->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-success"
                        onclick="event.preventDefault(); confirmarActivacion({{ $categoria->id }});">
                        <i data-feather="refresh-ccw"></i>
                    </a>
                </form>
            @endif
        </td>

    </tr>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarCategoria{{ $categoria->id }}" tabindex="-1"
        aria-labelledby="editarCategoriaLabel{{ $categoria->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarCategoriaLabel{{ $categoria->id }}">
                        Editar Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('categorias.actualizar', $categoria->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="nombre{{ $categoria->id }}">Nombre</label>
                            <input type="text" class="form-control" id="nombre{{ $categoria->id }}" name="nombre"
                                value="{{ $categoria->nombre }}" required>
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
