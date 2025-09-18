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
                <span class="badge" style="background:#6c757d; font-size:15px; padding:8px 14px; border-radius:8px;">
                    Pendiente
                </span>
            @else
                @php
                    $diff = $mc - $ap; // positivo: cierre mayor; negativo: apertura mayor
                    $abs = number_format(abs($diff), 2);
                @endphp

                @if ($diff > 0)
                    <span class="badge" style="background:#28a745; font-size:15px; padding:8px 14px; border-radius:8px;">
                        Cierre superior por S/ {{ $abs }}
                    </span>
                @elseif ($diff < 0)
                    <span class="badge" style="background:#dc3545; font-size:15px; padding:8px 14px; border-radius:8px;">
                        Apertura superior por S/ {{ $abs }}
                    </span>
                @else
                    <span class="badge" style="background:#007bff; font-size:15px; padding:8px 14px; border-radius:8px;">
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
            {{-- === Ícono estilo "clientes": solo anchor + feather edit === --}}
            <a href="#" class="text-success btnEditarCaja" data-id="{{ $caja->id }}"
                data-update-url="{{ route('caja.update', $caja->id) }}"
                data-monto_apertura="{{ number_format($caja->monto_apertura, 2, '.', '') }}"
                data-fecha_apertura="{{ $valAp }}"
                data-monto_cierre="{{ $mc !== null ? number_format($mc, 2, '.', '') : '' }}"
                data-fecha_cierre="{{ $valCi }}" data-estado="{{ $caja->estado }}" aria-label="Editar">
                <i data-feather="edit"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-muted">No se encontraron registros.</td>
    </tr>
@endforelse
