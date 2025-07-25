@foreach ($documentos as $documento)
    <tr>
        <td class="border-top-0 px-2 py-4">
            <div class="d-flex no-block align-items-center">
                <div class="mr-3">

                </div>
                <div class="">
                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                        {{ $documento->nombre }}</h5>
                </div>
            </div>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <span
                style="background: {{ $documento->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                            color: {{ $documento->estado == 'Activo' ? '#159258' : '#780909' }};
                                                            padding: 4px; border-radius:4px;">
                {{ $documento->estado }}
            </span>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">

            <a href="#" class="text-success" data-bs-toggle="modal"
                data-bs-target="#editarDocumento{{ $documento->id }}">
                <i data-feather="edit"></i>
            </a>


            @if ($documento->estado === 'Activo')
                <form action="{{ route('documentos.desactivar', $documento->id) }}" method="POST"
                    style="display:inline;" id="form-desactivar-{{ $documento->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-danger"
                        onclick="event.preventDefault(); confirmarDesactivacion({{ $documento->id }});">
                        <i data-feather="trash-2"></i>
                    </a>
                </form>
            @else
                <form action="{{ route('documentos.activar', $documento->id) }}" method="POST" style="display:inline;"
                    id="form-activar-{{ $documento->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-success"
                        onclick="event.preventDefault(); confirmarActivacion({{ $documento->id }});">
                        <i data-feather="refresh-ccw"></i>
                    </a>
                </form>
            @endif
        </td>

    </tr>

    <!-- Modal: Editar documento -->
    <div class="modal fade" id="editarDocumento{{ $documento->id }}" tabindex="-1"
        aria-labelledby="editarDocumentoLabel{{ $documento->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow"
                style="border-radius: 12px; background-color: #ffffff; overflow: hidden;">

                <!-- Encabezado -->
                <div class="modal-header text-white py-3 px-4" style="background-color: #0A7ABF;">
                    <h5 class="modal-title fw-semibold mb-0" id="editarDocumentoLabel{{ $documento->id }}">
                        <i class="bi bi-pencil-square me-2"></i>Editar Documento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('documentos.actualizar', $documento->id) }}" method="POST"
                    class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Cuerpo -->
                    <div class="modal-body px-4 py-4" style="background-color: #F9F9F9;">
                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-12">
                                <label for="nombre{{ $documento->id }}" class="form-label fw-semibold text-dark">Nombre
                                    del
                                    documento</label>
                                <input type="text" class="form-control rounded-3" id="nombre{{ $documento->id }}"
                                    name="nombre" value="{{ $documento->nombre }}" required>
                                <div class="invalid-feedback" id="nombreError{{ $documento->id }}">
                                    Por favor, ingresa un nombre válido.
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
                        <button type="submit" class="btn text-white rounded-3 px-4" style="background-color: #25A6D9;">
                            Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endforeach
