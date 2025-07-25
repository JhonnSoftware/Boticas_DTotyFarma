@extends('layouts.plantilla') <!-- Esto extiende la plantilla base -->

@section('title', 'Modulo Usuarios') <!-- Cambia el título de la página -->

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

        .btnAgregarUsuario:hover {
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
                            <h4 class="card-title" style="font-size: 24px;">Lista de Usuarios</h4>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="entries-info">
                                Showing 10 entries
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch gap-2">
                                <div class="input-group">
                                    <form action="{{ route('usuarios.index') }}" method="GET" class="input-group">
                                        <input type="text" name="buscar" id="inputBusqueda" class="form-control"
                                            placeholder="Buscar por nombre..." value="{{ request('buscar') }}"
                                            style="border-radius: 10px 0 0 10px; padding: 15px; height: auto;">
                                        <button type="submit" class="btn btn-primary"
                                            style="border-radius: 0 10px 10px 0; background: #ffffff; color: #000; border: 1px solid #eaecef; padding:15px;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <button class="btnAgregarUsuario"
                                    style="white-space: nowrap; border: 1px solid #2275fc; border-radius: 10px; color: #2275fc; background: #fff; padding:15px; transition: background-color 0.3s ease, color 0.3s ease;"
                                    data-bs-toggle="modal" data-bs-target="#nuevoUsuario">
                                    <i class="fas fa-plus"></i> Nuevo Usuario
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr class="border-0">
                                        <th class="border-0 font-14 font-weight-medium text-black px-2">Nombre</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Email</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Contraseña</th>
                                        <th class="border-0 font-14 font-weight-medium text-black">Rol</th>
                                        <th class="border-0 font-14 font-weight-medium text-black rounded-end">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody id="tablaUsuarios">
                                    @foreach ($usuarios as $usuario)
                                        <tr>
                                            <td class="border-top-0 px-2 py-4">
                                                <div class="d-flex no-block align-items-center">
                                                    <div class="mr-3">
                                                        @if ($usuario->foto && Storage::exists('public/usuarios/' . $usuario->foto))
                                                            <img src="{{ url('storage/usuarios/' . $usuario->foto) }}"
                                                                alt="Foto" class="rounded-circle" width="45"
                                                                height="45">
                                                        @else
                                                            <img src="{{ url('imagenes/usuario_icon.jpg') }}" alt="Sin foto"
                                                                class="rounded-circle" width="45" height="45">
                                                        @endif

                                                    </div>
                                                    <div class="">
                                                        <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                            {{ $usuario->name }}</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $usuario->email }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $usuario->password }}</td>
                                            <td class="border-top-0 text-dark px-2 py-4">{{ $usuario->rol }}</td>

                                            <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
                                                <!-- Botón de Editar -->
                                                <a href="#" class="text-success me-2" data-bs-toggle="modal"
                                                    data-bs-target="#editarUsuario{{ $usuario->id }}">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                <!-- Botón de Eliminar solo si es tipo usuario -->
                                                @if ($usuario->rol === 'usuario')
                                                    <form id="formEliminar{{ $usuario->id }}"
                                                        action="{{ route('usuarios.destroy', $usuario->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-link text-danger p-0 border-0"
                                                            onclick="confirmarEliminacion({{ $usuario->id }}, '{{ $usuario->name }}')">
                                                            <i data-feather="trash-2"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                            </td>


                                        </tr>

                                        <!-- Modal de edición -->
                                        <div class="modal fade" id="editarUsuario{{ $usuario->id }}" tabindex="-1"
                                            aria-labelledby="editarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content shadow border-0 rounded-4"
                                                    style="background-color: #F2F2F2;">

                                                    <!-- Header -->
                                                    <div class="modal-header text-white px-4 py-3"
                                                        style="background-color: #0A7ABF;">
                                                        <h5 class="modal-title fw-bold"
                                                            id="editarUsuarioLabel{{ $usuario->id }}">
                                                            <i class="bi bi-pencil-square me-2"></i>Editar Usuario
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                    </div>

                                                    <!-- Formulario -->
                                                    <form action="{{ route('usuarios.actualizar', $usuario->id) }}"
                                                        method="POST" enctype="multipart/form-data"
                                                        class="needs-validation" novalidate>
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="modal-body px-4 py-4">
                                                            <div class="row g-3">

                                                                <!-- Nombre -->
                                                                <div class="col-md-6">
                                                                    <label for="name{{ $usuario->id }}"
                                                                        class="form-label fw-semibold text-dark">Nombre
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control rounded-pill shadow-sm"
                                                                        id="name{{ $usuario->id }}" name="name"
                                                                        value="{{ $usuario->name }}" required>
                                                                    <div class="invalid-feedback"
                                                                        id="nameError{{ $usuario->id }}">El nombre es
                                                                        obligatorio.</div>
                                                                </div>

                                                                <!-- Correo -->
                                                                <div class="col-md-6">
                                                                    <label for="email{{ $usuario->id }}"
                                                                        class="form-label fw-semibold text-dark">Correo
                                                                        electrónico <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="email"
                                                                        class="form-control rounded-pill shadow-sm"
                                                                        id="email{{ $usuario->id }}" name="email"
                                                                        value="{{ $usuario->email }}" required>
                                                                    <div class="invalid-feedback"
                                                                        id="emailError{{ $usuario->id }}">Ingresa un
                                                                        correo válido.</div>
                                                                </div>

                                                                <!-- Rol -->
                                                                <div class="col-md-6">
                                                                    <label for="rol{{ $usuario->id }}"
                                                                        class="form-label fw-semibold text-dark">Rol <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select rounded-pill shadow-sm"
                                                                        id="rol{{ $usuario->id }}" name="rol"
                                                                        required>
                                                                        <option value="" disabled>Seleccionar rol
                                                                        </option>
                                                                        <option value="usuario"
                                                                            {{ $usuario->rol === 'usuario' ? 'selected' : '' }}>
                                                                            Usuario</option>
                                                                        <option value="admin"
                                                                            {{ $usuario->rol === 'admin' ? 'selected' : '' }}>
                                                                            Administrador</option>
                                                                    </select>
                                                                    <div class="invalid-feedback"
                                                                        id="rolError{{ $usuario->id }}">Selecciona un
                                                                        rol.</div>
                                                                </div>

                                                                <!-- Contraseña -->
                                                                <div class="col-md-6">
                                                                    <label for="password{{ $usuario->id }}"
                                                                        class="form-label fw-semibold text-dark">Nueva
                                                                        contraseña (opcional)</label>
                                                                    <input type="password"
                                                                        class="form-control rounded-pill shadow-sm"
                                                                        id="password{{ $usuario->id }}" name="password">
                                                                    <div class="invalid-feedback"
                                                                        id="passwordError{{ $usuario->id }}">La
                                                                        contraseña debe ser válida.</div>
                                                                </div>

                                                                <!-- Confirmar contraseña -->
                                                                <div class="col-md-6">
                                                                    <label for="password_confirmation{{ $usuario->id }}"
                                                                        class="form-label fw-semibold text-dark">Confirmar
                                                                        contraseña</label>
                                                                    <input type="password"
                                                                        class="form-control rounded-pill shadow-sm"
                                                                        id="password_confirmation{{ $usuario->id }}"
                                                                        name="password_confirmation">
                                                                </div>

                                                                <!-- Foto -->
                                                                <div class="col-md-6">
                                                                    <label for="foto{{ $usuario->id }}"
                                                                        class="form-label fw-semibold text-dark">Foto
                                                                        (opcional)
                                                                    </label>
                                                                    <input type="file"
                                                                        class="form-control rounded-pill shadow-sm"
                                                                        id="foto{{ $usuario->id }}" name="foto"
                                                                        accept="image/*">
                                                                    @if ($usuario->foto && Storage::exists('public/usuarios/' . $usuario->foto))
                                                                        <div class="mt-2">
                                                                            <small class="text-muted">Foto
                                                                                actual:</small><br>
                                                                            <img src="{{ url('storage/usuarios/' . $usuario->foto) }}"
                                                                                alt="Foto actual"
                                                                                class="rounded shadow-sm" width="60"
                                                                                height="60">
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer bg-white rounded-bottom-4 px-4 py-3">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary rounded-pill px-4"
                                                                data-bs-dismiss="modal">
                                                                Cancelar
                                                            </button>
                                                            <button type="submit"
                                                                class="btn text-white rounded-pill px-4"
                                                                style="background-color: #25A6D9;">Guardar Cambios</button>
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

    <!-- Modal: Registrar nuevo usuario -->
    <div class="modal fade" id="nuevoUsuario" tabindex="-1" aria-labelledby="nuevoUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0" style="border-radius: 12px; background-color: #ffffff; overflow: hidden;">

                <!-- Encabezado -->
                <div class="modal-header text-white" style="background-color: #0A7ABF;">
                    <h5 class="modal-title fw-bold" id="nuevoUsuarioLabel">
                        <i class="bi bi-person-plus me-2"></i>Registrar nuevo usuario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body px-4 py-4" style="background-color: #F9F9F9;">
                    <form action="{{ route('usuarios.store') }}" method="POST" enctype="multipart/form-data"
                        class="needs-validation" novalidate>
                        @csrf

                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label for="name" class="form-label text-dark">Nombre <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3" id="name" name="name"
                                    required>
                                <div class="invalid-feedback">Por favor, ingresa un nombre.</div>
                            </div>

                            <!-- Correo -->
                            <div class="col-md-6">
                                <label for="email" class="form-label text-dark">Correo electrónico <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-3" id="email" name="email"
                                    required>
                                <div class="invalid-feedback">Por favor, ingresa un correo válido.</div>
                            </div>

                            <!-- Rol -->
                            <div class="col-md-6">
                                <label for="rol" class="form-label text-dark">Rol <span
                                        class="text-danger">*</span></label>
                                <select class="form-select rounded-3" id="rol" name="rol" required>
                                    <option value="" disabled selected>Seleccionar rol</option>
                                    <option value="usuario">Usuario</option>
                                    <option value="admin">Administrador</option>
                                </select>
                                <div class="invalid-feedback">Selecciona un rol para el usuario.</div>
                            </div>

                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <label for="password" class="form-label text-dark">Contraseña <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-3" id="password" name="password"
                                    required>
                                <div class="invalid-feedback">Ingresa una contraseña.</div>
                            </div>

                            <!-- Confirmación de contraseña -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label text-dark">Confirmar
                                    contraseña</label>
                                <input type="password" class="form-control rounded-3" id="password_confirmation"
                                    name="password_confirmation">
                            </div>

                            <!-- Foto -->
                            <div class="col-md-6">
                                <label for="foto" class="form-label text-dark">Foto de perfil (opcional)</label>
                                <input type="file" class="form-control rounded-3" id="foto" name="foto"
                                    accept="image/*">
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
                                <i class="bi bi-save me-1"></i>Guardar usuario
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
        document.addEventListener('DOMContentLoaded', () => {
            const forms = document.querySelectorAll('.needs-validation');

            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        form.classList.add('was-validated');

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
            const campos = document.querySelectorAll(
                'input[id^="name"], input[id^="email"], input[id^="password"], select[id^="rol"]');

            campos.forEach(input => {
                const tipo = input.id.startsWith('name') ? 'nameError' :
                    input.id.startsWith('email') ? 'emailError' :
                    input.id.startsWith('password') ? 'passwordError' :
                    input.id.startsWith('rol') ? 'rolError' : '';

                const id = input.id.replace(/[^\d]/g, '');
                const errorDiv = document.getElementById(tipo + id);

                input.addEventListener('input', () => {
                    if (input.validity.valid) {
                        input.classList.remove('is-invalid');
                        if (errorDiv) errorDiv.classList.add('d-none');
                    }
                });

                input.addEventListener('blur', () => {
                    if (!input.validity.valid) {
                        input.classList.add('is-invalid');
                        if (errorDiv) errorDiv.classList.remove('d-none');
                    }
                });
            });
        });
    </script>


    <script>
        function confirmarEliminacion(id, nombre) {
            Swal.fire({
                title: '¿Estás seguro?',
                html: `¿Deseas eliminar permanentemente al usuario <strong>${nombre}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`formEliminar${id}`).submit();
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#inputBusqueda').on('keyup', function() {
                var buscar = $(this).val();

                $.ajax({
                    url: "{{ route('usuarios.buscar') }}",
                    type: "GET",
                    data: {
                        buscar: buscar
                    },
                    success: function(data) {
                        $('#tablaUsuarios').html(data);
                        feather.replace();
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
