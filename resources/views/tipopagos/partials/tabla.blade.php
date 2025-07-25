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

    <!-- Modal: Editar Tipo de Pago -->
    <div class="modal fade" id="editarTipoPago{{ $tipopago->id }}" tabindex="-1"
        aria-labelledby="editarTipoPagoLabel{{ $tipopago->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0"
                style="border-radius: 12px; background-color: #ffffff; overflow: hidden;">

                <!-- Encabezado -->
                <div class="modal-header text-white" style="background-color: #0A7ABF;">
                    <h5 class="modal-title fw-bold" id="editarTipoPagoLabel{{ $tipopago->id }}">
                        <i class="bi bi-pencil-square me-2"></i>Editar Tipo de Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body px-4 py-4" style="background-color: #F9F9F9;">
                    <form action="{{ route('tipopagos.actualizar', $tipopago->id) }}" method="POST" novalidate
                        class="needs-validation">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-12">
                                <label for="nombre{{ $tipopago->id }}" class="form-label text-dark">Nombre del Tipo de
                                    Pago</label>
                                <input type="text" class="form-control rounded-3" id="nombre{{ $tipopago->id }}"
                                    name="nombre" value="{{ $tipopago->nombre }}" required>
                                <div class="invalid-feedback" id="nombreError{{ $tipopago->id }}">
                                    Por favor ingresa un nombre válido.
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer mt-4 border-0 px-0 d-flex justify-content-end"
                            style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                            <button type="button" class="btn btn-outline-secondary rounded-3 me-2"
                                data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn text-white rounded-3" style="background-color: #25A6D9;">
                                <i class="bi bi-save me-1"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endforeach
