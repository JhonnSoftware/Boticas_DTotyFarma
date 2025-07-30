@extends('layouts.plantilla')

@section('title', 'Historial de Ventas')

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

        #formBusquedaVentas .btn:hover {
            filter: brightness(1.1);
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 20px;">
                    <div class="card-body">

                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title" style="font-size: 24px;">Historial de Ventas</h4>
                        </div>

                        <form id="formBusquedaVentas"
                            class="bg-white rounded-3 p-3 d-flex flex-wrap gap-3 align-items-center justify-content-start mb-4">

                            {{-- 🔍 Campo de texto --}}
                            <div class="input-group" style="min-width: 240px;">
                                <input type="text" name="buscar" id="inputBusqueda" class="form-control"
                                    placeholder="Buscar por código, cliente, documento, usuario..."
                                    style="border-radius: 12px 0 0 12px; border-right: none;">
                                <button type="submit" class="btn"
                                    style="border-radius: 0 12px 12px 0; background-color: #0A7ABF; color: white; border: 1px solid #0A7ABF;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>

                            {{-- 📅 Fecha de inicio --}}
                            <div>
                                <label for="fechaInicio" class="form-label mb-1 text-muted">Desde:</label>
                                <input type="date" name="fecha_inicio" id="fechaInicio" class="form-control"
                                    style="min-width: 160px; border-radius: 10px; background-color: #F2F2F2;">
                            </div>

                            {{-- 📅 Fecha de fin --}}
                            <div>
                                <label for="fechaFin" class="form-label mb-1 text-muted">Hasta:</label>
                                <input type="date" name="fecha_fin" id="fechaFin" class="form-control"
                                    style="min-width: 160px; border-radius: 10px; background-color: #F2F2F2;">
                            </div>

                            {{-- ❌ Botón limpiar --}}
                            <div>
                                <label class="form-label d-none d-md-block mb-1 text-white">Limpiar</label>
                                <button type="button" id="btnLimpiarFechas"
                                    class="btn w-100 d-flex align-items-center gap-1 rounded"
                                    style="background-color: #6EBF49; color: white; border: none;">
                                    <i class="fas fa-times-circle"></i> Limpiar
                                </button>
                            </div>

                            {{-- 📁 Botón Exportar (Dropdown) al lado --}}
                            <div>
                                <label class="form-label d-none d-md-block mb-1 text-white">Exportar</label>
                                <div class="dropdown">
                                    <button
                                        class="btn btn-secondary dropdown-toggle w-100 d-flex align-items-center gap-1 rounded"
                                        type="button" id="dropdownExportar" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="background-color: #0A7ABF; color: white; border: none;">
                                        <i class="fas fa-file-export"></i> Exportar
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownExportar">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('ventas.exportar', 'pdf') }}">
                                                <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('ventas.exportar', 'xlsx') }}">
                                                <i class="fas fa-file-excel text-success me-2"></i> Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('ventas.exportar', 'csv') }}">
                                                <i class="fas fa-file-csv text-primary me-2"></i> CSV
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('ventas.exportar', 'txt') }}">
                                                <i class="fas fa-file-alt text-muted me-2"></i> TXT
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>


                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr class="border-0">
                                        <th class="border-0 font-14 font-weight-medium text-black px-2">Código</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Cliente</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Fecha</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Total</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Método de Pago</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Tipo de Documento</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Registrado por</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Estado</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Acciones</th>

                                    </tr>
                                </thead>
                                <tbody id="tablaHistorialVentas">
                                    @forelse($ventas as $venta)
                                        <tr>
                                            <td class="px-2 py-4">{{ $venta->codigo }}</td>
                                            <td>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellidos }}</td>
                                            <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                                            <td>S/ {{ number_format($venta->total, 2) }}</td>

                                            <td>{{ $venta->pago->nombre }}</td>
                                            <td>{{ $venta->documento->nombre }}</td>
                                            <td>{{ $venta->usuario->name }}</td>

                                            <td>
                                                <span
                                                    style="background: {{ $venta->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                        color: {{ $venta->estado == 'Activo' ? '#159258' : '#780909' }};
                                                        padding: 4px; border-radius:4px;">
                                                    {{ $venta->estado }}
                                                </span>
                                            </td>
                                            <td>
                                                @if (strtolower($venta->documento->nombre) === 'voucher')
                                                    <a href="{{ url('storage/vouchers/voucher_' . $venta->codigo . '.pdf') }}"
                                                        target="_blank"
                                                        class="btn btn-sm d-inline-flex justify-content-center align-items-center"
                                                        style="background-color: #ff4c4c; color: white; border-radius: 50%; width: 36px; height: 36px;">
                                                        <i class="fa fa-file-pdf"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                                @if ($venta->estado === 'Activo' && $venta->detalles->count() > 0)
                                                    <button type="button" class="btn btn-sm btn-devolver"
                                                        data-id="{{ $venta->id }}" title="Devolver venta"
                                                        style="background-color: #ffc107; color: #212529; border-radius: 50%; width: 36px; height: 36px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: 0.3s;">
                                                        <i class="fas fa-undo-alt"></i>
                                                    </button>
                                                @endif
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">No se encontraron ventas
                                                registradas.</td>
                                        </tr>
                                    @endforelse
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

    @foreach ($ventas as $venta)
        @if ($venta->estado === 'Activo' && $venta->detalles->count() > 0)
            <!-- Modal -->
            <div class="modal fade" id="modalDevolucion{{ $venta->id }}" tabindex="-1" role="dialog"
                aria-labelledby="modalLabel{{ $venta->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <form action="{{ route('devoluciones.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="venta_id" value="{{ $venta->id }}">
                        <input type="hidden" name="id_producto" value="{{ $venta->detalles->first()->id_producto }}">
                        <input type="hidden" name="cantidad" value="{{ $venta->detalles->first()->cantidad }}">

                        <div class="modal-content shadow-lg" style="border-radius: 15px;">
                            <div class="modal-header"
                                style="background-color: #f8f9fc; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                <h5 class="modal-title text-primary font-weight-bold" id="modalLabel{{ $venta->id }}">
                                    Registrar Devolución
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="motivo" class="font-weight-bold">Motivo de la devolución:</label>
                                    <textarea name="motivo" class="form-control" rows="3" required>
Devolución solicitada por el usuario debido a producto no conforme.</textarea>
                                </div>

                                <div class="alert alert-warning mt-3" role="alert">
                                    Esta devolución afectará el stock del producto asociado a la venta
                                    <strong>#{{ $venta->codigo }}</strong>.
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Confirmar devolución
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach


@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            function buscarVentas() {
                const buscar = $('#inputBusqueda').val();
                const fechaInicio = $('#fechaInicio').val();
                const fechaFin = $('#fechaFin').val();

                $.ajax({
                    url: "{{ route('ventas.buscar') }}",
                    type: "GET",
                    data: {
                        buscar: buscar,
                        fecha_inicio: fechaInicio,
                        fecha_fin: fechaFin
                    },
                    success: function(data) {
                        $('#tablaHistorialVentas').html(data);
                        feather.replace(); // si usas íconos feather
                    }
                });
            }

            $('#inputBusqueda, #fechaInicio, #fechaFin').on('keyup change', function() {
                buscarVentas();
            });

            $('#btnLimpiarFechas').on('click', function() {
                $('#inputBusqueda').val('');
                $('#fechaInicio').val('');
                $('#fechaFin').val('');
                buscarVentas();
            });
        });
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-devolver').forEach(button => {
                button.addEventListener('click', function() {
                    const ventaId = this.getAttribute('data-id'); // capturamos el ID de la venta
                    const modalId = `#modalDevolucion${ventaId}`; // construimos el ID del modal

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción marcará la venta como devuelta y ajustará el stock.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, devolver',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Mostrar el modal de Bootstrap
                            $(modalId).modal('show');
                        }
                    });
                });
            });
        });
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        </script>
    @endif

@endsection
