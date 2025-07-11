@extends('layouts.plantilla')

@section('title', 'Modulo Tipo Pagos')

@section('content')
    <style>
        .entries-info {
            color: #6c757d;
            font-size: 14px;
        }

        .search-box {
            min-width: 200px;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fc;
            /* Un color de fondo para pruebas */
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination .page-item .page-link {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            text-align: center;
            padding: 0;
            line-height: 38px;
            border: none;
            background-color: transparent;
            color: black;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #ccc;
        }

        .pagination .page-link:focus {
            box-shadow: none;
        }

        .btnAgregarTipoPago:hover {
            background-color: #2275fc !important;
            color: #ffffff !important;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 20px;">
                    <div class="card-body">

                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title" style="font-size: 24px;">Lista de Tipo de Pagos</h4>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="entries-info">
                                Showing 10 entries
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch gap-2">
                                <div class="input-group">
                                    <form action="{{ route('tipopagos.index') }}" method="GET" class="input-group">
                                        <input type="text" name="buscar" id="inputBusqueda" class="form-control"
                                            placeholder="Buscar por nombre..." value="{{ request('buscar') }}"
                                            style="border-radius: 10px 0 0 10px; padding: 15px; height: auto;">
                                        <button type="submit" class="btn btn-primary"
                                            style="border-radius: 0 10px 10px 0; background: #ffffff; color: #000; border: 1px solid #eaecef; padding:15px;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <button class="btnAgregarTipoPago"
                                    style="white-space: nowrap; border: 1px solid #2275fc; border-radius: 10px; color: #2275fc; background: #fff; padding:15px; transition: background-color 0.3s ease, color 0.3s ease;"
                                    data-bs-toggle="modal" data-bs-target="#nuevoTipoPago">
                                    <i class="fas fa-plus"></i> Nuevo Tipo Pago
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr class="border-0">
                                        <th class="border-0 font-14 font-weight-medium text-black px-2">Nombre</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Estado</th>
                                        <th class="border-0 font-14 font-weight-medium text-black rounded-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaTipoPagos">
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
                                                    <form action="{{ route('tipopagos.desactivar', $tipopago->id) }}"
                                                        method="POST" style="display:inline;"
                                                        id="form-desactivar-{{ $tipopago->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <a href="#" class="text-danger"
                                                            onclick="event.preventDefault(); confirmarDesactivacion({{ $tipopago->id }});">
                                                            <i data-feather="trash-2"></i>
                                                        </a>
                                                    </form>
                                                @else
                                                    <form action="{{ route('tipopagos.activar', $tipopago->id) }}"
                                                        method="POST" style="display:inline;"
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
                                                        <h5 class="modal-title"
                                                            id="editarTipoPagoLabel{{ $tipopago->id }}">
                                                            Editar Tipo Pago</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Cerrar"></button>
                                                    </div>
                                                    <form action="{{ route('tipopagos.actualizar', $tipopago->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="form-group mb-3">
                                                                <label for="nombre{{ $tipopago->id }}">Nombre</label>
                                                                <input type="text" class="form-control"
                                                                    id="nombre{{ $tipopago->id }}" name="nombre"
                                                                    value="{{ $tipopago->nombre }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Guardar
                                                                Cambios</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center" id="pagination">
                                        <!-- Los elementos del paginador se generarán con JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nuevoTipoPago" tabindex="-1" aria-labelledby="nuevoTipoPagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0 rounded-4 overflow-hidden">

            <!-- Encabezado -->
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="nuevoTipoPagoLabel">
                    <i class="bi bi-folder-plus me-2"></i> Nuevo Tipo Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body p-4">
                <form action="{{ route('tipopagos.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold">Nombre del Tipo Pago</label>
                        <input type="text" class="form-control form-control-lg rounded-3" id="nombre" name="nombre" placeholder="Ej: Yape" required>
                        <div class="invalid-feedback">Por favor, ingresa un nombre válido.</div>
                    </div>

                    <input type="hidden" name="estado" value="Activo">

                    <!-- Footer -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-save me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


@endsection


@section('scripts')

    @if (session('success'))
        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $('#inputBusqueda').on('keyup', function() {
                var buscar = $(this).val();

                $.ajax({
                    url: "{{ route('tipopagos.buscar') }}",
                    type: "GET",
                    data: {
                        buscar: buscar
                    },
                    success: function(data) {
                        $('#tablaTipoPagos').html(data);
                        feather.replace();
                    }
                });
            });
        });
    </script>

    <script>
        function confirmarDesactivacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El tipo pago será marcado como inactivo",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, desactivar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-desactivar-' + id).submit();
                }
            });
        }

        function confirmarActivacion(id) {
            Swal.fire({
                title: '¿Reingresar tipo pago?',
                text: "El tipo pago será marcado como activo nuevamente.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, reingresar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-activar-' + id).submit();
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rowsPerPage = 10;
            const table = document.querySelector('.table');
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');
            const pageCount = Math.ceil(rows.length / rowsPerPage);
            const pagination = document.getElementById('pagination');

            let currentPage = 1;

            // Función para mostrar las filas de la página actual
            function showPage(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });

                updatePaginationButtons(page);
            }

            // Función para actualizar los botones del paginador
            function updatePaginationButtons(page) {
                pagination.innerHTML = '';
                currentPage = page;

                // Botón Anterior
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${page === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML =
                    `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (page > 1) showPage(page - 1);
                });
                pagination.appendChild(prevLi);

                // Botones de páginas
                const maxVisiblePages = 5; // Máximo de botones de página a mostrar
                let startPage, endPage;

                if (pageCount <= maxVisiblePages) {
                    startPage = 1;
                    endPage = pageCount;
                } else {
                    const maxPagesBeforeCurrent = Math.floor(maxVisiblePages / 2);
                    const maxPagesAfterCurrent = Math.ceil(maxVisiblePages / 2) - 1;

                    if (page <= maxPagesBeforeCurrent) {
                        startPage = 1;
                        endPage = maxVisiblePages;
                    } else if (page + maxPagesAfterCurrent >= pageCount) {
                        startPage = pageCount - maxVisiblePages + 1;
                        endPage = pageCount;
                    } else {
                        startPage = page - maxPagesBeforeCurrent;
                        endPage = page + maxPagesAfterCurrent;
                    }
                }

                // Botón primera página si es necesario
                if (startPage > 1) {
                    const firstLi = document.createElement('li');
                    firstLi.className = 'page-item';
                    firstLi.innerHTML = `<a class="page-link" href="#">1</a>`;
                    firstLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        showPage(1);
                    });
                    pagination.appendChild(firstLi);

                    if (startPage > 2) {
                        const dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<span class="page-link">...</span>`;
                        pagination.appendChild(dotsLi);
                    }
                }

                // Botones de páginas numeradas
                for (let i = startPage; i <= endPage; i++) {
                    const pageLi = document.createElement('li');
                    pageLi.className = `page-item ${i === page ? 'active' : ''}`;
                    pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    pageLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        showPage(i);
                    });
                    pagination.appendChild(pageLi);
                }

                // Botón última página si es necesario
                if (endPage < pageCount) {
                    if (endPage < pageCount - 1) {
                        const dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<span class="page-link">...</span>`;
                        pagination.appendChild(dotsLi);
                    }

                    const lastLi = document.createElement('li');
                    lastLi.className = 'page-item';
                    lastLi.innerHTML = `<a class="page-link" href="#">${pageCount}</a>`;
                    lastLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        showPage(pageCount);
                    });
                    pagination.appendChild(lastLi);
                }

                // Botón Siguiente
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${page === pageCount ? 'disabled' : ''}`;
                nextLi.innerHTML =
                    `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
                nextLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (page < pageCount) showPage(page + 1);
                });
                pagination.appendChild(nextLi);
            }

            // Inicializar paginación
            showPage(1);
        });
    </script>
@endsection
