@extends('layouts.plantilla')

@section('title', 'Módulo Apertura de Caja')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 20px;">
                    <div class="card-body">

                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <div class="card shadow-sm p-3" style="border-radius: 15px; max-width: 400px; width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span id="toggleLabel" class="fw-semibold text-muted">Modo: Pagos Físicos</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="togglePagos" checked
                                            style="width: 50px; height: 25px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Título --}}
                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title" style="font-size: 24px;">Apertura de Caja</h4>
                        </div>


                        {{-- Mensajes --}}
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Formulario --}}
                        {{-- Lógica condicional apertura / cierre --}}
                        @if (!$caja)
                            {{-- FORMULARIO DE APERTURA --}}
                            <form action="{{ route('caja.apertura.store') }}" method="POST"
                                onsubmit="return calcularTotal();">
                                @csrf

                                <div class="row text-center mb-4" style="font-size: 14px;">
                                    @php
                                        $denominaciones = [
                                            ['valor' => 0.1, 'imagen' => 'moneda_010.jpg'],
                                            ['valor' => 0.2, 'imagen' => 'moneda_020.jpeg'],
                                            ['valor' => 0.5, 'imagen' => 'moneda_050.jpg'],
                                            ['valor' => 1.0, 'imagen' => 'moneda_1.jpg'],
                                            ['valor' => 2.0, 'imagen' => 'moneda_2.jpg'],
                                            ['valor' => 5.0, 'imagen' => 'moneda_5.jpg'],
                                            ['valor' => 10.0, 'imagen' => 'billete_10.jpg'],
                                            ['valor' => 20.0, 'imagen' => 'billete_20.jpg'],
                                            ['valor' => 50.0, 'imagen' => 'billete_50.jpg'],
                                            ['valor' => 100.0, 'imagen' => 'billete_100.jpg'],
                                            ['valor' => 200.0, 'imagen' => 'billete_200.png'],
                                        ];
                                    @endphp

                                    @foreach ($denominaciones as $item)
                                        <div class="col-6 col-md-2 mb-3">
                                            <div class="card shadow-sm">
                                                <img src="{{ url('imagenes/dinero/' . $item['imagen']) }}"
                                                    alt="S/ {{ $item['valor'] }}"
                                                    style="height: 60px; object-fit: contain; margin-top: 10px;">
                                                <div class="card-body p-2">
                                                    <p class="mb-1"><strong>S/
                                                            {{ number_format($item['valor'], 2) }}</strong></p>
                                                    <input type="number" min="0" name="cantidad[]"
                                                        data-valor="{{ $item['valor'] }}"
                                                        class="form-control cantidad text-center" placeholder="0"
                                                        style="border-radius: 8px; font-size: 14px;">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-end mb-4">
                                    <h4>Total: <span id="totalMostrar">S/ 0.00</span></h4>
                                </div>

                                <input type="hidden" name="monto_apertura" id="monto_apertura">

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success"
                                        style="padding: 10px 25px; border-radius: 10px;">
                                        <i class="fas fa-cash-register me-1"></i> Abrir Caja
                                    </button>
                                </div>
                            </form>
                        @else
                            {{-- FORMULARIO DE CIERRE CON DENOMINACIONES --}}
                            <form id="formCierreCaja" action="{{ route('caja.cierre.store') }}" method="POST">

                                @csrf

                                <div id="seccionPagosFisicos">
                                    <p><strong>Monto de Apertura:</strong> S/ {{ number_format($caja->monto_apertura, 2) }}
                                    </p>

                                    <div class="row text-center mb-4" style="font-size: 14px;">
                                        @php
                                            $denominaciones = [
                                                ['valor' => 0.1, 'imagen' => 'moneda_010.jpg'],
                                                ['valor' => 0.2, 'imagen' => 'moneda_020.jpeg'],
                                                ['valor' => 0.5, 'imagen' => 'moneda_050.jpg'],
                                                ['valor' => 1.0, 'imagen' => 'moneda_1.jpg'],
                                                ['valor' => 2.0, 'imagen' => 'moneda_2.jpg'],
                                                ['valor' => 5.0, 'imagen' => 'moneda_5.jpg'],
                                                ['valor' => 10.0, 'imagen' => 'billete_10.jpg'],
                                                ['valor' => 20.0, 'imagen' => 'billete_20.jpg'],
                                                ['valor' => 50.0, 'imagen' => 'billete_50.jpg'],
                                                ['valor' => 100.0, 'imagen' => 'billete_100.jpg'],
                                                ['valor' => 200.0, 'imagen' => 'billete_200.png'],
                                            ];
                                        @endphp

                                        @foreach ($denominaciones as $item)
                                            <div class="col-6 col-md-2 mb-3">
                                                <div class="card shadow-sm">
                                                    <img src="{{ url('imagenes/dinero/' . $item['imagen']) }}"
                                                        alt="S/ {{ $item['valor'] }}"
                                                        style="height: 60px; object-fit: contain; margin-top: 10px;">
                                                    <div class="card-body p-2">
                                                        <p class="mb-1"><strong>S/
                                                                {{ number_format($item['valor'], 2) }}</strong></p>
                                                        <input type="number" min="0" name="cantidad_cierre[]"
                                                            data-valor="{{ $item['valor'] }}"
                                                            class="form-control cantidad-cierre text-center" placeholder="0"
                                                            style="border-radius: 8px; font-size: 14px;">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Campo oculto --}}
                                <input type="hidden" name="monto_cierre" id="monto_cierre">

                                {{-- PAGOS ELECTRÓNICOS --}}
                                <div id="seccionPagosElectronicos" style="display: none;">
                                    <div class="row g-3 text-center">

                                        {{-- YAPE --}}
                                        <div class="col-md-4">
                                            <div class="card h-100 shadow-sm border-0">
                                                <img src="{{ url('imagenes/dinero/yape.jpg') }}" alt="Yape"
                                                    style="height: 50px; object-fit: contain; margin-top: 10px;">
                                                <div class="card-body p-2">
                                                    <label for="monto_yape" class="form-label">Yape (S/)</label>
                                                    <div class="input-group">
                                                        <input type="number" name="monto_yape" id="monto_yape"
                                                            class="form-control text-center monto-electronico"
                                                            step="0.01" min="0" value="0">
                                                        <button type="button"
                                                            class="btn btn-outline-success btn-agregar-monto"
                                                            data-id="monto_yape">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        {{-- PLIN --}}
                                        <div class="col-md-4">
                                            <div class="card h-100 shadow-sm border-0">
                                                <img src="{{ url('imagenes/dinero/plin.png') }}" alt="PLIN"
                                                    style="height: 50px; object-fit: contain; margin-top: 10px;">
                                                <div class="card-body p-2">
                                                    <label for="monto_plin" class="form-label">Plin (S/)</label>
                                                    <div class="input-group">
                                                        <input type="number" name="monto_plin" id="monto_plin"
                                                            class="form-control text-center" step="0.01"
                                                            min="0" value="0">
                                                        <button type="button"
                                                            class="btn btn-outline-success btn-agregar-monto"
                                                            data-id="monto_plin">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- BBVA --}}
                                        <div class="col-md-4">
                                            <div class="card h-100 shadow-sm border-0">
                                                <img src="{{ url('imagenes/dinero/bbva.jpg') }}" alt="Tarjeta"
                                                    style="height: 50px; object-fit: contain; margin-top: 10px;">
                                                <div class="card-body p-2">
                                                    <label for="monto_bbva" class="form-label">BBVA (S/)</label>
                                                    <div class="input-group">
                                                        <input type="number" name="monto_bbva" id="monto_bbva"
                                                            class="form-control text-center" step="0.01"
                                                            min="0" value="0">
                                                        <button type="button"
                                                            class="btn btn-outline-success btn-agregar-monto"
                                                            data-id="monto_bbva">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- INTERBANK --}}
                                        <div class="col-md-6">
                                            <div class="card h-100 shadow-sm border-0">
                                                <img src="{{ url('imagenes/dinero/interbank.png') }}" alt="Transferencia"
                                                    style="height: 50px; object-fit: contain; margin-top: 10px;">
                                                <div class="card-body p-2">
                                                    <label for="monto_interbank" class="form-label">Interbank (S/)</label>
                                                    <div class="input-group">
                                                        <input type="number" name="monto_interbank" id="monto_interbank"
                                                            class="form-control text-center" step="0.01"
                                                            min="0" value="0">
                                                        <button type="button"
                                                            class="btn btn-outline-success btn-agregar-monto"
                                                            data-id="monto_interbank">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- OTROS --}}
                                        <div class="col-md-6">
                                            <div class="card h-100 shadow-sm border-0">
                                                <img src="{{ url('imagenes/dinero/otros.png') }}" alt="Otros"
                                                    style="height: 50px; object-fit: contain; margin-top: 10px;">
                                                <div class="card-body p-2">
                                                    <label for="monto_otros" class="form-label">Otros (S/)</label>
                                                    <div class="input-group">
                                                        <input type="number" name="monto_otros" id="monto_otros"
                                                            class="form-control text-center" step="0.01"
                                                            min="0" value="0">
                                                        <button type="button"
                                                            class="btn btn-outline-success btn-agregar-monto"
                                                            data-id="monto_otros">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- Total calculado --}}
                                <div class="text-end mb-4 mt-5">
                                    <h4>Total: <span id="totalMostrarCierre">S/ 0.00</span></h4>
                                </div>

                                <div class="text-center">
                                    <button type="button" id="btnCerrarCaja" class="btn btn-danger"
                                        style="padding: 10px 25px; border-radius: 10px;">
                                        <i class="fas fa-lock me-1"></i> Cerrar Caja
                                    </button>
                                </div>


                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        function calcularTotal() {
            let total = 0;
            document.querySelectorAll('.cantidad').forEach(input => {
                const valor = parseFloat(input.dataset.valor);
                const cantidad = parseInt(input.value) || 0;
                total += valor * cantidad;
            });

            document.getElementById('totalMostrar').textContent = `S/ ${total.toFixed(2)}`;
            document.getElementById('monto_apertura').value = total.toFixed(2);

            return true;
        }

        document.addEventListener('input', calcularTotal);
    </script>

    <script>
        // 🎯 NUEVA lógica para el cálculo manual
        let montosElectronicos = {
            yape: 0,
            plin: 0,
            bbva: 0,
            interbank: 0,
            otros: 0,
        };

        function calcularTotalManual() {
            let total = 0;

            // Pagos físicos
            document.querySelectorAll('.cantidad-cierre').forEach(input => {
                const valor = parseFloat(input.dataset.valor);
                const cantidad = parseInt(input.value) || 0;
                total += valor * cantidad;
            });

            // Pagos electrónicos
            for (const key in montosElectronicos) {
                total += montosElectronicos[key];
            }

            document.getElementById('totalMostrarCierre').textContent = `S/ ${total.toFixed(2)}`;
            document.getElementById('monto_cierre').value = total.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function() {

            const montosGuardados = localStorage.getItem('montosElectronicos');
            if (montosGuardados) {
                montosElectronicos = JSON.parse(montosGuardados);
                calcularTotalManual();
            }

            // Restaurar cantidades físicas
            const cantidadesGuardadas = JSON.parse(localStorage.getItem('cantidadesFisicas'));
            if (cantidadesGuardadas) {
                document.querySelectorAll('.cantidad-cierre').forEach((input, index) => {
                    if (cantidadesGuardadas.hasOwnProperty(index)) {
                        input.value = cantidadesGuardadas[index];
                    }
                });
            }


            // Escuchar click en los botones "+"
            document.querySelectorAll('.btn-agregar-monto').forEach(boton => {
                boton.addEventListener('click', function() {
                    const inputId = this.dataset.id;
                    const input = document.getElementById(inputId);
                    const valor = parseFloat(input.value);

                    if (!valor || valor <= 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Monto inválido',
                            text: 'Ingresa un monto mayor a cero.',
                            confirmButtonColor: '#f39c12'
                        });
                        return;
                    }

                    const tipo = inputId.replace('monto_', '');
                    montosElectronicos[tipo] += valor;
                    localStorage.setItem('montosElectronicos', JSON.stringify(montosElectronicos));

                    input.value = '';
                    calcularTotalManual();
                });
            });

            // Escuchar cambios en monedas/billetes
            document.querySelectorAll('.cantidad-cierre').forEach((input, index) => {
                input.addEventListener('input', function() {
                    const cantidades = JSON.parse(localStorage.getItem('cantidadesFisicas')) || {};
                    cantidades[index] = this.value;
                    localStorage.setItem('cantidadesFisicas', JSON.stringify(cantidades));

                    calcularTotalManual();
                });
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('togglePagos');
            const seccionFisicos = document.getElementById('seccionPagosFisicos');
            const seccionElectronicos = document.getElementById('seccionPagosElectronicos');
            const labelSuperior = document.getElementById('toggleLabel');
            const labelSwitch = document.querySelector('label[for="togglePagos"]');

            function actualizarToggle() {
                if (toggle.checked) {
                    seccionFisicos.style.display = 'block';
                    seccionElectronicos.style.display = 'none';
                    if (labelSuperior) labelSuperior.textContent = 'Modo: Pagos Físicos';
                    if (labelSwitch) labelSwitch.textContent = 'Mostrar Pagos Físicos';
                } else {
                    seccionFisicos.style.display = 'none';
                    seccionElectronicos.style.display = 'block';
                    if (labelSuperior) labelSuperior.textContent = 'Modo: Pagos No Físicos';
                    if (labelSwitch) labelSwitch.textContent = 'Mostrar Pagos No Físicos';
                }

                calcularTotalManual();
            }

            toggle.addEventListener('change', actualizarToggle);
            actualizarToggle();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnCerrar = document.getElementById('btnCerrarCaja');
            const formCierre = document.getElementById('formCierreCaja');

            btnCerrar.addEventListener('click', function() {
                calcularTotalManual();

                Swal.fire({
                    title: '¿Estás seguro de cerrar la caja?',
                    text: 'Esta acción registrará el cierre definitivo.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cerrar caja',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // 🧼 Limpiar datos locales
                        localStorage.removeItem('montosElectronicos');
                        localStorage.removeItem('cantidadesFisicas');

                        formCierre.submit();
                    }
                });
            });

        });
    </script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#198754'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Atención',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc3545'
            });
        @endif
    </script>

@endsection
