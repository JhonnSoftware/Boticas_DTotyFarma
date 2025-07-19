@extends('layouts.plantilla')

@section('title', 'Historial de Devoluciones')

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
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 20px;">
                    <div class="card-body">

                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title" style="font-size: 24px;">Historial de Devoluciones</h4>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="entries-info">
                                Showing 10 entries
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch gap-2">
                                <div class="input-group">
                                    <form action="{{ route('devoluciones.buscar') }}" method="GET" class="input-group">
                                        <input type="text" name="buscar" id="inputBusqueda" class="form-control"
                                            placeholder="Buscar por producto, usuario, motivo o fecha..."
                                            value="{{ request('buscar') }}"
                                            style="border-radius: 10px 0 0 10px; padding: 15px; height: auto;">
                                        <button type="submit" class="btn btn-primary"
                                            style="border-radius: 0 10px 10px 0; background: #ffffff; color: #000; border: 1px solid #eaecef; padding:15px;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr class="border-0">
                                        <th class="border-0 font-14 font-weight-medium text-black px-2">Código de Venta</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Cliente</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Producto Devuelto</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Cantidad</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Motivo</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Fecha de Devolución</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Registrado por</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaHistorialDevoluciones">
                                    @forelse ($devoluciones as $devolucion)
                                        <tr>
                                            <td>{{ $devolucion->venta->codigo }}</td>
                                            <td>
                                                {{ $devolucion->venta->cliente->nombre ?? 'N/A' }}
                                                {{ $devolucion->venta->cliente->apellidos ?? '' }}
                                            </td>
                                            <td>{{ $devolucion->producto->descripcion ?? '—' }}</td>
                                            <td>{{ $devolucion->cantidad }}</td>
                                            <td>{{ $devolucion->motivo }}</td>
                                            <td>{{ \Carbon\Carbon::parse($devolucion->fecha)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $devolucion->usuario->name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No se encontraron devoluciones.</td>
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


@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#inputBusqueda').on('keyup', function() {
                var buscar = $(this).val();

                $.ajax({
                    url: "{{ route('devoluciones.buscar') }}",
                    type: "GET",
                    data: {
                        buscar: buscar
                    },
                    success: function(data) {
                        $('#tablaHistorialDevoluciones').html(data);
                    }
                });
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

@endsection
