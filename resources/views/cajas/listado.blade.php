@extends('layouts.plantilla')

@section('title', 'Historial de Cajas')

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

        .btnAgregarCategoria:hover {
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
                            <h4 class="card-title" style="font-size: 24px;">Historial de Cajas</h4>
                        </div>

                        <div class="d-flex justify-content-end mb-4">
                            <form action="{{ route('caja.listado') }}" method="GET"
                                class="d-flex align-items-center gap-3 flex-wrap">

                                <!-- Campo de Fecha -->
                                <div class="input-group" style="width: 250px;">
                                    <span class="input-group-text bg-white border-end-0 px-3"
                                        style="border: 1px solid #d6d8db; border-right: none;">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </span>
                                    <input type="date" name="fecha" value="{{ request('fecha') }}"
                                        class="form-control border-start-0"
                                        style="background-color: #f8f9fc; border-left: none; border: 1px solid #d6d8db;" />
                                </div>

                                <!-- Botón Buscar -->
                                <button type="submit" class="btn d-flex align-items-center px-4 py-2 text-white"
                                    style="background: linear-gradient(to right, #007bff, #007bff); font-weight: 500; border: none; transition: background 0.3s ease;">
                                    <i class="fas fa-search me-2"></i> Buscar
                                </button>

                                <!-- Botón Limpiar -->
                                @if (request('fecha'))
                                    <a href="{{ route('caja.listado') }}"
                                        class="btn btn-outline-secondary d-flex align-items-center px-4 py-2"
                                        style="border: 1px solid #ced4da; transition: background 0.3s ease;">
                                        <i class="fas fa-times me-2"></i> Limpiar
                                    </a>
                                @endif
                            </form>
                        </div>


                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr class="border-0">
                                        <th class="border-0 font-14 font-weight-medium text-black">Monto Apertura</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Fecha Apertura</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Monto Cierre</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Fecha Cierre</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCajas">
                                    @forelse ($cajas as $caja)
                                        <tr>
                                            <td class="py-3">S/ {{ number_format($caja->monto_apertura, 2) }}</td>
                                            <td class="py-3">
                                                {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="py-3">
                                                {{ $caja->monto_cierre !== null ? 'S/ ' . number_format($caja->monto_cierre, 2) : '-' }}
                                            </td>
                                            <td class="py-3">
                                                {{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i') : '-' }}
                                            </td>
                                            <td class="py-3">
                                                <span
                                                    style="background: {{ $caja->estado == 'abierta' ? '#6ff073' : '#d4d4d4' }};
                                                        color: {{ $caja->estado == 'abierta' ? '#159258' : '#555' }};
                                                        padding: 4px 8px;
                                                        border-radius: 4px;
                                                        font-size: 14px;">
                                                    {{ ucfirst($caja->estado) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-3 text-muted">No se encontraron registros de cajas.
                                            </td>
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
