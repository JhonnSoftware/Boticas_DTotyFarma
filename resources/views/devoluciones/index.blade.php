@extends('layouts.plantilla')

@section('title', 'Historial de Devoluciones Ventas')

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

        #formBusquedaCompleta .btn:hover {
            filter: brightness(1.1);
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 20px;">
                    <div class="card-body">

                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title" style="font-size: 24px;">Historial de Devoluciones Ventas</h4>
                        </div>

                        <form id="formBusquedaCompleta"
                            class="bg-white rounded-3 p-3 d-flex flex-wrap gap-3 align-items-center justify-content-start mb-4">

                            {{-- 🔍 Campo de texto --}}
                            <div class="input-group" style="min-width: 240px;">
                                <input type="text" name="buscar" id="inputBusqueda" class="form-control"
                                    placeholder="Buscar por producto, usuario, motivo..."
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
                                            <a class="dropdown-item" href="{{ route('devoluciones.exportar', 'pdf') }}">
                                                <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('devoluciones.exportar', 'xlsx') }}">
                                                <i class="fas fa-file-excel text-success me-2"></i> Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('devoluciones.exportar', 'csv') }}">
                                                <i class="fas fa-file-csv text-primary me-2"></i> CSV
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('devoluciones.exportar', 'txt') }}">
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
                                        <th class="border-0 font-14 font-weight-medium text-black px-2">Código de Venta</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Cliente</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Producto Devuelto</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Cantidad</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Motivo</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Fecha de Devolución</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Registrado por</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaHistorialDevolucionesVentas">
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
                const buscar = $(this).val();
                const fechaInicio = $('#fechaInicio').val();
                const fechaFin = $('#fechaFin').val();

                $.ajax({
                    url: "{{ route('devoluciones.buscar') }}",
                    type: "GET",
                    data: {
                        buscar: buscar,
                        fecha_inicio: fechaInicio,
                        fecha_fin: fechaFin
                    },
                    success: function(data) {
                        $('#tablaHistorialDevolucionesVentas').html(data);
                    },
                });
            });

            // También puedes agregar este extra para que al cambiar fechas se actualice sin escribir texto
            $('#fechaInicio, #fechaFin').on('change', function() {
                $('#inputBusqueda').trigger('keyup');
            });
        });

        $('#btnLimpiarFechas').on('click', function() {
            $('#inputBusqueda').val('');
            $('#fechaInicio').val('');
            $('#fechaFin').val('');

            $.ajax({
                url: "{{ route('devoluciones.buscar') }}",
                type: "GET",
                success: function(data) {
                    $('#tablaHistorialDevolucionesVentas').html(data);
                },
                error: function() {
                    alert("Error al limpiar búsqueda.");
                }
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
