@extends('layouts.plantilla')

@section('title', 'Historial de Cajas')

@section('content')
    <style>
        .entries-info {
            color: #6c757d;
            font-size: 14px;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fc;
        }

        a.text-success:hover {
            opacity: .9;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 20px;">
                    <div class="card-body">

                        <h4 class="card-title mb-4">Historial de Cajas</h4>

                        {{-- Buscador --}}
                        <form id="formBusquedaCaja"
                            class="d-flex align-items-center gap-3 flex-wrap bg-white rounded-3 p-3 mb-4"
                            style="max-width: 700px;">
                            <div>
                                <label class="form-label mb-1 text-muted">Fecha:</label>
                                <input type="date" name="fecha" id="fechaCaja" class="form-control"
                                    value="{{ request('fecha') }}"
                                    style="min-width: 180px; border-radius: 10px; background-color: #F2F2F2;">
                            </div>
                            <div>
                                <label class="form-label d-none d-md-block mb-1 text-white">Buscar</label>
                                <button type="submit" class="btn px-4 py-2 rounded"
                                    style="background-color: #0A7ABF; color: white;">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                            <div>
                                <label class="form-label d-none d-md-block mb-1 text-white">Limpiar</label>
                                <button type="button" id="btnLimpiarCaja" class="btn px-4 py-2 rounded"
                                    style="background-color: #6EBF49; color: white;">
                                    <i class="fas fa-times-circle"></i> Limpiar
                                </button>
                            </div>
                        </form>

                        {{-- Tabla --}}
                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead style="background: #f8f9fc;">
                                    <tr>
                                        <th>Monto Apertura</th>
                                        <th>Fecha Apertura</th>
                                        <th>Monto Cierre</th>
                                        <th>Fecha Cierre</th>
                                        <th>Monto Info.</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCajas">
                                    @forelse ($cajas as $caja)
                                        @php
                                            $ap = (float) $caja->monto_apertura;
                                            $mc = is_null($caja->monto_cierre) ? null : (float) $caja->monto_cierre;
                                            $fechaAp = \Carbon\Carbon::parse($caja->fecha_apertura);
                                            $fechaCi = $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre) : null;
                                            $valAp = $fechaAp->format('Y-m-d\TH:i');
                                            $valCi = $fechaCi ? $fechaCi->format('Y-m-d\TH:i') : '';
                                        @endphp
                                        <tr>
                                            <td>S/ {{ number_format($caja->monto_apertura, 2) }}</td>
                                            <td>{{ $fechaAp->format('d/m/Y H:i') }}</td>
                                            <td>{{ $mc !== null ? 'S/ ' . number_format($mc, 2) : '-' }}</td>
                                            <td>{{ $fechaCi ? $fechaCi->format('d/m/Y H:i') : '-' }}</td>
                                            <td>
                                                @if (is_null($mc))
                                                    <span class="badge"
                                                        style="background:#6c757d; font-size:15px; padding:8px 14px; border-radius:8px;">
                                                        Pendiente
                                                    </span>
                                                @else
                                                    @php
                                                        $diff = $mc - $ap;
                                                        $abs = number_format(abs($diff), 2);
                                                    @endphp

                                                    @if ($diff > 0)
                                                        <span class="badge"
                                                            style="background:#28a745; font-size:15px; padding:8px 14px; border-radius:8px;">
                                                            Cierre superior por S/ {{ $abs }}
                                                        </span>
                                                    @elseif ($diff < 0)
                                                        <span class="badge"
                                                            style="background:#dc3545; font-size:15px; padding:8px 14px; border-radius:8px;">
                                                            Apertura superior por S/ {{ $abs }}
                                                        </span>
                                                    @else
                                                        <span class="badge"
                                                            style="background:#007bff; font-size:15px; padding:8px 14px; border-radius:8px;">
                                                            Iguales
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    style="background: {{ $caja->estado == 'abierta' ? '#6ff073' : '#d4d4d4' }};
                                                         color: {{ $caja->estado == 'abierta' ? '#159258' : '#555' }};
                                                         padding: 4px 8px; border-radius: 4px;">
                                                    {{ ucfirst($caja->estado) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="text-success btnEditarCaja"
                                                    data-id="{{ $caja->id }}"
                                                    data-update-url="{{ route('caja.update', $caja->id) }}"
                                                    data-monto_apertura="{{ number_format($caja->monto_apertura, 2, '.', '') }}"
                                                    data-fecha_apertura="{{ $valAp }}"
                                                    data-monto_cierre="{{ $mc !== null ? number_format($mc, 2, '.', '') : '' }}"
                                                    data-fecha_cierre="{{ $valCi }}"
                                                    data-estado="{{ $caja->estado }}" aria-label="Editar">
                                                    <i data-feather="edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-muted">No se encontraron registros.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal editar (moverlo al body si da problemas de aria-hidden) --}}
    <div class="modal fade" id="modalEditarCaja" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-3">
                <div class="modal-header" style="background: linear-gradient(135deg, #0A7ABF, #25A6D9);">
                    <h5 class="modal-title text-white">Editar Caja</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarCaja">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="cajaId">

                        <div class="mb-3">
                            <label>Monto Apertura</label>
                            <input type="number" step="0.01" class="form-control" id="monto_apertura" required>
                        </div>
                        <div class="mb-3">
                            <label>Fecha Apertura</label>
                            <input type="datetime-local" class="form-control" id="fecha_apertura" required>
                        </div>
                        <div class="mb-3">
                            <label>Monto Cierre</label>
                            <input type="number" step="0.01" class="form-control" id="monto_cierre">
                        </div>
                        <div class="mb-3">
                            <label>Fecha Cierre</label>
                            <input type="datetime-local" class="form-control" id="fecha_cierre">
                        </div>
                        <div class="mb-3">
                            <label>Estado</label>
                            <select class="form-select" id="estado" required>
                                <option value="abierta">Abierta</option>
                                <option value="cerrada">Cerrada</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarCaja">
                        <span class="save-text">Guardar cambios</span>
                        <span class="save-spinner d-none">
                            <span class="spinner-border spinner-border-sm"></span> Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // === Control unificado de modal ===
        const MODAL_ID = 'modalEditarCaja';
        let modalBsInstance = null;
        let modalIsBs4 = false;

        function openCajaModal() {
            const el = document.getElementById(MODAL_ID);
            if (!el) return;

            if (window.bootstrap && bootstrap.Modal) {
                try {
                    if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                        modalBsInstance = bootstrap.Modal.getOrCreateInstance(el);
                    } else if (typeof bootstrap.Modal.getInstance === 'function') {
                        modalBsInstance = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
                    } else {
                        modalBsInstance = new bootstrap.Modal(el);
                    }
                    modalBsInstance.show();
                    modalIsBs4 = false;
                    return;
                } catch (e) {}
            }

            if (window.jQuery && typeof jQuery(el).modal === 'function') {
                jQuery(el).modal('show');
                modalIsBs4 = true;
                return;
            }

            el.classList.add('show');
            el.style.display = 'block';
            document.body.classList.add('modal-open');
        }

        function closeCajaModal() {
            const el = document.getElementById(MODAL_ID);
            if (!el) return;

            if (!modalIsBs4 && window.bootstrap && bootstrap.Modal) {
                try {
                    const inst =
                        modalBsInstance ||
                        (typeof bootstrap.Modal.getInstance === 'function' ? bootstrap.Modal.getInstance(el) : null) ||
                        (typeof bootstrap.Modal.getOrCreateInstance === 'function' ? bootstrap.Modal.getOrCreateInstance(el) : null) ||
                        new bootstrap.Modal(el);
                    inst.hide();
                } catch (e) {}
            } else if (modalIsBs4 && window.jQuery && typeof jQuery(el).modal === 'function') {
                jQuery(el).modal('hide');
            } else {
                el.classList.remove('show');
                el.style.display = 'none';
            }

            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                const wrap = document.getElementById('main-wrapper');
                if (wrap && wrap.hasAttribute('aria-hidden')) wrap.removeAttribute('aria-hidden');
            }, 50);
        }

        // CSRF
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });

        let currentUpdateUrl = null;

        function hookEditButtons() {
            document.querySelectorAll('.btnEditarCaja').forEach(a => {
                a.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    document.getElementById('cajaId').value = a.dataset.id;
                    document.getElementById('monto_apertura').value = a.dataset.monto_apertura || '';
                    document.getElementById('fecha_apertura').value = a.dataset.fecha_apertura || '';
                    document.getElementById('monto_cierre').value = a.dataset.monto_cierre || '';
                    document.getElementById('fecha_cierre').value = a.dataset.fecha_cierre || '';
                    document.getElementById('estado').value = a.dataset.estado || 'abierta';
                    currentUpdateUrl = a.dataset.updateUrl || null;

                    openCajaModal();
                });
            });

            if (window.feather && typeof feather.replace === 'function') feather.replace();
        }

        document.addEventListener('DOMContentLoaded', hookEditButtons);

        // Guardar cambios
        document.getElementById('btnGuardarCaja').addEventListener('click', function() {
            if (!currentUpdateUrl) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se encontró la URL de actualización' });
                return;
            }
            const btn = this;
            btn.disabled = true;
            btn.querySelector('.save-text').classList.add('d-none');
            btn.querySelector('.save-spinner').classList.remove('d-none');

            $.ajax({
                url: currentUpdateUrl,
                type: 'POST',
                data: {
                    _method: 'PUT',
                    monto_apertura: document.getElementById('monto_apertura').value,
                    fecha_apertura: document.getElementById('fecha_apertura').value,
                    monto_cierre: document.getElementById('monto_cierre').value || null,
                    fecha_cierre: document.getElementById('fecha_cierre').value || null,
                    estado: document.getElementById('estado').value,
                },
                success: function() {
                    closeCajaModal();
                    $('#formBusquedaCaja').submit();
                    Swal.fire({
                        icon: 'success',
                        title: 'Actualizado',
                        text: 'Caja modificada correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    let msg = 'No se pudo actualizar.';
                    if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                },
                complete: function() {
                    btn.disabled = false;
                    btn.querySelector('.save-text').classList.remove('d-none');
                    btn.querySelector('.save-spinner').classList.add('d-none');
                }
            });
        });

        // Buscar cajas
        $('#formBusquedaCaja').on('submit', function(e) {
            e.preventDefault();
            $.get("{{ route('caja.buscar') }}", { fecha: $('#fechaCaja').val() }, function(html) {
                $('#tablaCajas').html(html);
                hookEditButtons();
            });
        });

        $('#btnLimpiarCaja').click(function() {
            $('#fechaCaja').val('');
            $('#formBusquedaCaja').submit();
        });

        // Feather inicial
        document.addEventListener('DOMContentLoaded', function() {
            if (window.feather && typeof feather.replace === 'function') feather.replace();
        });

        // Enter dentro del modal
        document.getElementById('formEditarCaja').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('btnGuardarCaja').click();
            }
        });
    </script>
@endsection
