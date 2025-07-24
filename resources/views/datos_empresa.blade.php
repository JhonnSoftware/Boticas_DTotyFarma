@extends('layouts.plantilla')

@section('title', 'Datos Empresa')

@section('content')
<div class="container-fluid">
    <div class="card mx-auto shadow" style="border-radius: 20px; max-width: 900px;">
        <div class="card-body">

            {{-- Logo circular centrado --}}
            <div class="d-flex justify-content-center mb-4">
                <div class="border border-3 rounded-circle p-1" style="width: 130px; height: 130px; border-color: #25A6D9;">
                    <img src="{{ url('imagenes/logo_empresa.png') }}" alt="Logo Empresa" class="w-100 h-100 object-fit-cover rounded-circle">
                </div>
            </div>

            {{-- Título y eslogan --}}
            <h3 class="text-center fw-bold mb-2" style="color: #0A7ABF;">Boticas D Toty Farma</h3>
            <p class="text-center fst-italic mb-4" style="color: #25A6D9;">"Cuidando tu salud y economía"</p>

            {{-- Dos columnas de información --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong style="color:#0A7ABF;">Razón Social:</strong> Boticas D Toty Farma (Yoselyn Huamán Valentín)</li>
                        <li class="list-group-item"><strong style="color:#0A7ABF;">Nombre Comercial:</strong> Boticas D Toty Farma</li>
                        <li class="list-group-item"><strong style="color:#0A7ABF;">RUC:</strong> 10471965311</li>
                        <li class="list-group-item"><strong style="color:#0A7ABF;">Email:</strong> yhoman2018@hotmail.com</li>
                        <li class="list-group-item"><strong style="color:#0A7ABF;">Horario:</strong> Lunes a Viernes de 7:00am - 4:00pm</li>
                    </ul>
                </div>

                <div class="col-md-6 mb-3">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong style="color:#0A7ABF;">Teléfono:</strong> 987631807</li>
                        <li class="list-group-item"><strong style="color:#0A7ABF;">Dirección Fiscal:</strong> Av. 7 de junio 361, Urb. Puente Sector 4, Santa Anita, Lima</li>
                        <li class="list-group-item"><strong style="color:#0A7ABF;">Responsable Sanitario:</strong> Rosmery Paucar López (C.Q.F. 47216782)</li>
                        <li class="list-group-item"><strong style="color:#0A7ABF;">Tipo de Empresa:</strong> Persona Natural</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
