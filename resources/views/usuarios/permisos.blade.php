@extends('layouts.plantilla')

@section('content')
    <div class="container-fluid mt-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-white py-3 rounded"
                style="background: linear-gradient(90deg, #0A7ABF, #25A6D9); box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                Asignar Permisos a Usuarios
            </h2>
        </div>

        <form action="{{ route('usuarios.permisos.guardar') }}" method="POST">
            @csrf

            @foreach ($usuarios as $usuario)
                <div class="card mb-5 shadow" style="border: none; background-color: #F2F2F2;">
                    <div class="card-header text-white d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(90deg, #25A6D9, #0A7ABF); font-size: 1.1rem;">
                        <span><i class="fas fa-user-circle me-2"></i><strong>{{ $usuario->name }}</strong> -
                            {{ $usuario->email }}</span>
                        <span class="badge bg-white text-primary px-3 py-2 shadow-sm">ID: {{ $usuario->id }}</span>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ($modulos as $modulo)
                                @php
                                    $permisoInactivo = in_array(
                                        $modulo,
                                        $usuario->permisos->pluck('modulo')->toArray(),
                                    );
                                    $moduloNombre = ucfirst(str_replace('_', ' ', $modulo));
                                @endphp

                                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    <div class="border rounded p-3 text-center shadow-sm"
                                        style="background-color: white; transition: all 0.3s ease; border-left: 5px solid {{ !$permisoInactivo ? '#6EBF49' : '#ccc' }}">
                                        <label class="form-label d-block mb-2 text-dark" style="font-weight: 600;">
                                            {{ $moduloNombre }}
                                        </label>
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox"
                                                name="permisos[{{ $usuario->id }}][]" value="{{ $modulo }}"
                                                style="border-color: #6EBF49;" {{ !$permisoInactivo ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-center gap-3 mb-5">
                <button type="submit" class="btn text-white px-4 py-2 shadow" style="background-color: #6EBF49;">
                    <i class="fa fa-save me-1"></i> Guardar Permisos
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-danger px-4 py-2 shadow">
                    <i class="fa fa-times me-1"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Permisos actualizados!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#25A6D9',
                confirmButtonText: 'Aceptar',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        </script>
    @endif
@endsection
