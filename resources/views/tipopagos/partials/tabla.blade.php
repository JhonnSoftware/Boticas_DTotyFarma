@foreach ($tipopagos as $tipopago)
    <tr>
        <td class="border-top-0 px-2 py-4">
            <div class="d-flex no-block align-items-center">
                <div class="mr-3">

                </div>
                <div class="">
                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                        {{ $tipopago->nombre }}</h5>
                </div>
            </div>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <span
                style="background: {{ $tipopago->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                            color: {{ $tipopago->estado == 'Activo' ? '#159258' : '#780909' }};
                                                            padding: 4px; border-radius:4px;">
                {{ $tipopago->estado }}
            </span>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <a href="#" class="text-primary me-2"><i data-feather="eye"></i></a>

            <a href="#" class="text-success" data-bs-toggle="modal"
                data-bs-target="#editarTipoPago{{ $tipopago->id }}">
                <i data-feather="edit"></i>
            </a>


            @if ($tipopago->estado === 'Activo')
                <form action="{{ route('tipopagos.desactivar', $tipopago->id) }}" method="POST" style="display:inline;"
                    id="form-desactivar-{{ $tipopago->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-danger"
                        onclick="event.preventDefault(); confirmarDesactivacion({{ $tipopago->id }});">
                        <i data-feather="trash-2"></i>
                    </a>
                </form>
            @else
                <form action="{{ route('tipopagos.activar', $tipopago->id) }}" method="POST" style="display:inline;"
                    id="form-activar-{{ $tipopago->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-success"
                        onclick="event.preventDefault(); confirmarActivacion({{ $tipopago->id }});">
                        <i data-feather="refresh-ccw"></i>
                    </a>
                </form>
            @endif
        </td>

    </tr>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarTipoPago{{ $tipopago->id }}" tabindex="-1"
        aria-labelledby="editarTipoPagoLabel{{ $tipopago->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarTipoPagoLabel{{ $tipopago->id }}">
                        Editar Tipo Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('tipopagos.actualizar', $tipopago->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="nombre{{ $tipopago->id }}">Nombre</label>
                            <input type="text" class="form-control" id="nombre{{ $tipopago->id }}" name="nombre"
                                value="{{ $tipopago->nombre }}" required>
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
