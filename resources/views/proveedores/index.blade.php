@extends('layouts.plantilla') <!-- Esto extiende la plantilla base -->

@section('title', 'Modulo Proveedores') <!-- Cambia el título de la página -->

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

        .btnAgregarProveedor:hover {
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
                            <h4 class="card-title" style="font-size: 24px;">Lista de Proveedores</h4>
                        </div>
                        <div class="d-flex justify-content-end align-items-center flex-wrap gap-2 mb-3">

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch gap-2">
                                
                                <div class="input-group">
                                    <form action="{{ route('proveedores.index') }}" method="GET" class="input-group">
                                        <input type="text" name="buscar" id="inputBusqueda" class="form-control"
                                            placeholder="Buscar por RUC o nombre..." value="{{ request('buscar') }}"
                                            style="border-radius: 10px 0 0 10px; padding: 15px; height: auto;">
                                        <button type="submit" class="btn btn-primary"
                                            style="border-radius: 0 10px 10px 0; background: #ffffff; color: #000; border: 1px solid #eaecef; padding:15px;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="padding: 15px; border-radius: 10px;">
                                        <i class="fas fa-download me-1"></i> Exportar
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <li><a class="dropdown-item"
                                                href="{{ route('proveedores.exportar', ['formato' => 'pdf']) }}">
                                                <i class="far fa-file-pdf me-2 text-danger"></i>Exportar PDF</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('proveedores.exportar', ['formato' => 'xlsx']) }}">
                                                <i class="far fa-file-excel me-2 text-success"></i>Exportar Excel</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('proveedores.exportar', ['formato' => 'csv']) }}">
                                                <i class="fas fa-file-csv me-2 text-primary"></i>Exportar CSV</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('proveedores.exportar', ['formato' => 'txt']) }}">
                                                <i class="far fa-file-alt me-2 text-muted"></i>Exportar TXT</a></li>
                                    </ul>
                                </div>


                                <button class="btnAgregarProveedor"
                                    style="white-space: nowrap; border: 1px solid #2275fc; border-radius: 10px; color: #2275fc; background: #fff; padding:15px; transition: background-color 0.3s ease, color 0.3s ease;"
                                    data-bs-toggle="modal" data-bs-target="#nuevoProveedor">
                                    <i class="fas fa-plus"></i> Nuevo Proveedor
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr class="border-0">
                                        <th class="border-0 font-14 font-weight-medium text-black px-2">Nombre</th>
                                        <th class="border-0 font-14 font-weight-medium text-black rounded-start">RUC</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Telefono</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Correo</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Direccion</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Contacto</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Estado</th>
                                        <th class="border-0 font-14 font-weight-medium text-black rounded-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaProveedores">
                                    @foreach ($proveedores as $proveedor)
                                        <tr>
                                            <td class="border-top-0 px-2 py-4">
                                                <div class="d-flex no-block align-items-center">
                                                    <div class="mr-3">
                                                        <img src="{{ url('imagenes/proveedor_icon.jpg') }}" alt="user"
                                                            class="rounded-circle" width="45" height="45" />
                                                    </div>
                                                    <div class="">
                                                        <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                            {{ $proveedor->nombre }}</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->ruc }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->telefono }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->correo }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->direccion }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $proveedor->contacto }}</td>
                                            <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
                                                <span
                                                    style="background: {{ $proveedor->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                            color: {{ $proveedor->estado == 'Activo' ? '#159258' : '#780909' }};
                                                            padding: 4px; border-radius:4px;">
                                                    {{ $proveedor->estado }}
                                                </span>
                                            </td>
                                            <td class="font-weight-medium text-dark border-top-0 px-2 py-4">

                                                <a href="#" class="text-success" data-bs-toggle="modal"
                                                    data-bs-target="#editarProveedor{{ $proveedor->id }}">
                                                    <i data-feather="edit"></i>
                                                </a>


                                                @if ($proveedor->estado === 'Activo')
                                                    <form action="{{ route('proveedores.desactivar', $proveedor->id) }}"
                                                        method="POST" style="display:inline;"
                                                        id="form-desactivar-{{ $proveedor->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <a href="#" class="text-danger"
                                                            onclick="event.preventDefault(); confirmarDesactivacion({{ $proveedor->id }});">
                                                            <i data-feather="trash-2"></i>
                                                        </a>
                                                    </form>
                                                @else
                                                    <form action="{{ route('proveedores.activar', $proveedor->id) }}"
                                                        method="POST" style="display:inline;"
                                                        id="form-activar-{{ $proveedor->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <a href="#" class="text-success"
                                                            onclick="event.preventDefault(); confirmarActivacion({{ $proveedor->id }});">
                                                            <i data-feather="refresh-ccw"></i>
                                                        </a>
                                                    </form>
                                                @endif
                                            </td>

                                        </tr>

                                        <!-- Modal: Editar proveedor -->
                                        <div class="modal fade" id="editarProveedor{{ $proveedor->id }}" tabindex="-1"
                                            aria-labelledby="editarProveedorLabel{{ $proveedor->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0 shadow"
                                                    style="border-radius: 12px; background-color: #ffffff; overflow: hidden;">

                                                    <!-- Encabezado -->
                                                    <div class="modal-header text-white py-3 px-4"
                                                        style="background-color: #0A7ABF;">
                                                        <h5 class="modal-title fw-semibold mb-0"
                                                            id="editarProveedorLabel{{ $proveedor->id }}">
                                                            <i class="bi bi-pencil-square me-2"></i>Editar proveedor
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                    </div>

                                                    <form action="{{ route('proveedores.actualizar', $proveedor->id) }}"
                                                        method="POST" class="needs-validation was-validated" novalidate>
                                                        @csrf
                                                        @method('PUT')

                                                        <!-- Cuerpo -->
                                                        <div class="modal-body px-4 py-4"
                                                            style="background-color: #F9F9F9;">
                                                            <div class="row g-3">
                                                                <!-- Nombre -->
                                                                <div class="col-md-6">
                                                                    <label for="nombre{{ $proveedor->id }}"
                                                                        class="form-label fw-semibold text-dark">Nombre</label>
                                                                    <input type="text" class="form-control rounded-3"
                                                                        id="nombre{{ $proveedor->id }}" name="nombre"
                                                                        value="{{ $proveedor->nombre }}" required>
                                                                    <div class="invalid-feedback"
                                                                        id="nombreError{{ $proveedor->id ?? '' }}">
                                                                        Se debe registrar un nombre del proveedor.
                                                                    </div>
                                                                </div>

                                                                <!-- RUC -->
                                                                <div class="col-md-6">
                                                                    <label for="ruc{{ $proveedor->id }}"
                                                                        class="form-label fw-semibold text-dark">RUC</label>
                                                                    <div class="position-relative">
                                                                        <input type="text"
                                                                            class="form-control rounded-3"
                                                                            id="ruc{{ $proveedor->id }}" name="ruc"
                                                                            value="{{ $proveedor->ruc }}" maxlength="11"
                                                                            pattern="\d{11}" inputmode="numeric" required>
                                                                        <div
                                                                            class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                                                            <i
                                                                                class="bi bi-check-circle-fill text-success"></i>
                                                                        </div>
                                                                        <div class="invalid-feedback"
                                                                            id="rucError{{ $proveedor->id ?? '' }}">
                                                                            El RUC debe contener exactamente 11 dígitos
                                                                            numéricos.
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Teléfono -->
                                                                <div class="col-md-6">
                                                                    <label for="telefono{{ $proveedor->id }}"
                                                                        class="form-label fw-semibold text-dark">Teléfono</label>
                                                                    <div class="position-relative">
                                                                        <input type="text"
                                                                            class="form-control rounded-3"
                                                                            id="telefono{{ $proveedor->id }}"
                                                                            name="telefono"
                                                                            value="{{ $proveedor->telefono }}"
                                                                            maxlength="9" pattern="\d{9}"
                                                                            inputmode="numeric">
                                                                        <div
                                                                            class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                                                            <i
                                                                                class="bi bi-check-circle-fill text-success"></i>
                                                                        </div>
                                                                        <div class="invalid-feedback">
                                                                            El teléfono debe tener exactamente 9 dígitos
                                                                            numéricos (opcional).
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Correo -->
                                                                <div class="col-md-6">
                                                                    <label for="correo{{ $proveedor->id }}"
                                                                        class="form-label fw-semibold text-dark">Correo</label>
                                                                    <div class="position-relative">
                                                                        <input type="email"
                                                                            class="form-control rounded-3"
                                                                            id="correo{{ $proveedor->id }}"
                                                                            name="correo"
                                                                            value="{{ $proveedor->correo }}">
                                                                        <div
                                                                            class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                                                            <i
                                                                                class="bi bi-check-circle-fill text-success"></i>
                                                                        </div>
                                                                        <div class="invalid-feedback">
                                                                            Por favor ingrese un correo electrónico válido
                                                                            (opcional)
                                                                            .
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Dirección -->
                                                                <div class="col-12">
                                                                    <label for="direccion{{ $proveedor->id }}"
                                                                        class="form-label fw-semibold text-dark">Dirección</label>
                                                                    <div class="position-relative">
                                                                        <input type="text"
                                                                            class="form-control rounded-3"
                                                                            id="direccion{{ $proveedor->id }}"
                                                                            name="direccion"
                                                                            value="{{ $proveedor->direccion }}">
                                                                        <div
                                                                            class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                                                            <i
                                                                                class="bi bi-check-circle-fill text-success"></i>
                                                                        </div>
                                                                        <div class="invalid-feedback">
                                                                            Por favor ingrese la dirección del proveedor
                                                                            (opcional).
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Contacto -->
                                                                <div class="col-md-6">
                                                                    <label for="contacto{{ $proveedor->id }}"
                                                                        class="form-label fw-semibold text-dark">Contacto</label>
                                                                    <div class="position-relative">
                                                                        <input type="text"
                                                                            class="form-control rounded-3"
                                                                            id="contacto{{ $proveedor->id }}"
                                                                            name="contacto"
                                                                            value="{{ $proveedor->contacto }}">
                                                                        <div
                                                                            class="valid-feedback position-absolute end-0 top-50 translate-middle-y pe-3">
                                                                            <i
                                                                                class="bi bi-check-circle-fill text-success"></i>
                                                                        </div>
                                                                        <div class="invalid-feedback">
                                                                            Por favor ingrese el nombre del contacto del
                                                                            proveedor (opcional).
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Footer -->
                                                        <div class="modal-footer mt-3 border-0 px-4 d-flex justify-content-end bg-white"
                                                            style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary rounded-3 px-4 me-2"
                                                                data-bs-dismiss="modal">
                                                                Cancelar
                                                            </button>
                                                            <button type="submit" class="btn text-white rounded-3 px-4"
                                                                style="background-color: #25A6D9;">
                                                                Guardar Cambios
                                                            </button>
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

    <!-- Modal: Registrar nuevo proveedor -->
    <div class="modal fade" id="nuevoProveedor" tabindex="-1" aria-labelledby="nuevoProveedorLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0" style="border-radius: 12px; background-color: #ffffff; overflow: hidden;">

                <!-- Encabezado -->
                <div class="modal-header text-white" style="background-color: #0A7ABF;">
                    <h5 class="modal-title fw-bold" id="nuevoProveedorLabel">
                        <i class="bi bi-person-plus me-2"></i>Registrar nuevo proveedor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body px-4 py-4" style="background-color: #F9F9F9;">
                    <form action="{{ route('proveedores.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf

                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label for="nombre" class="form-label text-dark">Nombre</label>
                                <input type="text" class="form-control rounded-3" id="nombre" name="nombre"
                                    required>
                                <div class="invalid-feedback">
                                    Se debe registrar un nombre del proveedor.
                                </div>
                            </div>

                            <!-- RUC -->
                            <div class="col-md-6">
                                <label for="ruc" class="form-label text-dark">RUC</label>
                                <input type="text" class="form-control rounded-3" id="ruc" name="ruc"
                                    maxlength="11" pattern="\d*" inputmode="numeric" required>
                                <div class="invalid-feedback">
                                    El RUC debe tener exactamente 11 dígitos numéricos.
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="telefono" class="form-label text-dark">Teléfono</label>
                                <input type="text" class="form-control rounded-3" id="telefono" name="telefono"
                                    maxlength="9" pattern="\d*" inputmode="numeric">
                            </div>

                            <!-- Correo -->
                            <div class="col-md-6">
                                <label for="correo" class="form-label text-dark">Correo</label>
                                <input type="email" class="form-control rounded-3" id="correo" name="correo">
                            </div>

                            <!-- Dirección -->
                            <div class="col-12">
                                <label for="direccion" class="form-label text-dark">Dirección</label>
                                <input type="text" class="form-control rounded-3" id="direccion" name="direccion">
                            </div>

                            <!-- Contacto -->
                            <div class="col-md-6">
                                <label for="contacto" class="form-label text-dark">Contacto</label>
                                <input type="text" class="form-control rounded-3" id="contacto" name="contacto">
                            </div>

                            <!-- Estado -->
                            <input type="hidden" name="estado" value="Activo">
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer mt-4 border-0 px-0 d-flex justify-content-end"
                            style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                            <button type="button" class="btn btn-outline-secondary rounded-3 me-2"
                                data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn text-white rounded-3" style="background-color: #25A6D9;">
                                <i class="bi bi-save me-1"></i>Guardar proveedor
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
                    url: "{{ route('proveedores.buscar') }}",
                    type: "GET",
                    data: {
                        buscar: buscar
                    },
                    success: function(data) {
                        $('#tablaProveedores').html(data);
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
                text: "El proveedor será marcado como inactivo",
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
                title: '¿Reingresar proveedor?',
                text: "El proveedor será marcado como activo nuevamente.",
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
        document.addEventListener('DOMContentLoaded', () => {
            const forms = document.querySelectorAll('.needs-validation');

            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault(); // Detiene el envío
                        event.stopPropagation();

                        form.classList.add('was-validated');

                        // Mostrar el modal si está cerrado
                        const modalElement = form.closest('.modal');
                        if (modalElement) {
                            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                            modal.show();
                        }
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const camposRuc = document.querySelectorAll('input[id^="ruc"]');

            camposRuc.forEach(input => {
                const errorId = 'rucError' + input.id.replace('ruc', '');
                const errorDiv = document.getElementById(errorId);

                input.addEventListener('input', function() {
                    // Limpia errores si ya está bien
                    if (input.validity.valid) {
                        input.classList.remove('is-invalid');
                        if (errorDiv) errorDiv.style.display = 'none';
                    }
                });

                input.addEventListener('blur', function() {
                    if (!input.validity.valid) {
                        input.classList.add('is-invalid');
                        if (errorDiv) errorDiv.style.display = 'block';
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const camposNombre = document.querySelectorAll('input[id^="nombre"]');

            camposNombre.forEach(input => {
                const errorId = 'nombreError' + input.id.replace('nombre', '');
                const errorDiv = document.getElementById(errorId);

                input.addEventListener('input', function() {
                    if (input.validity.valid) {
                        input.classList.remove('is-invalid');
                        if (errorDiv) errorDiv.style.display = 'none';
                    }
                });

                input.addEventListener('blur', function() {
                    if (!input.validity.valid) {
                        input.classList.add('is-invalid');
                        if (errorDiv) errorDiv.style.display = 'block';
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
