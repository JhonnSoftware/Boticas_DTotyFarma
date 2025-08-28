@extends('layouts.plantilla')

@section('title', 'Alertas del Sistema')

@section('content')
    <div class="container-fluid">
        <!-- 
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('alertas.generar') }}" class="btn btn-outline-primary shadow-sm rounded-pill px-4 py-2">
                <i class="fas fa-sync-alt me-1"></i> Actualizar Alertas
            </a>
        </div>
        -->
        
        <div class="card shadow" style="border-radius: 20px;">
            
            <div class="card-header text-white d-flex align-items-center justify-content-between"
                 style="background-color: #0A7ABF; border-radius: 20px 20px 0 0;">
                <h4 class="mb-0">
                    <i class="fas fa-bell me-2"></i> Alertas del Sistema
                </h4>
            </div>

            <div class="card-body" style="background-color: #F2F2F2;">
                @if ($alertas->count() > 0)
                    <div class="list-group">
                        @foreach ($alertas as $alerta)
                            <div class="list-group-item mb-3 rounded shadow-sm"
                                 style="border-left: 5px solid {{ $alerta->leido ? '#6EBF49' : '#25A6D9' }}; background-color: #fff;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1" style="color: #0A7ABF;">{{ $alerta->titulo }}</h5>
                                        <p class="mb-1 text-muted">{{ $alerta->mensaje }}</p>
                                        <small class="text-secondary">{{ $alerta->created_at->format('d/m/Y h:i A') }}</small>
                                    </div>
                                    @if (!$alerta->leido)
                                        <form action="{{ route('alertas.marcarLeida', $alerta->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm text-white"
                                                    style="background-color: #25A6D9;">
                                                Marcar como leído
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-success text-white">Leído</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info rounded text-center">
                        No hay alertas en este momento.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
