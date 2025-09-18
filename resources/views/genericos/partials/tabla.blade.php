@foreach ($genericos as $generico)
<tr>
  <td class="border-top-0 px-2 py-4">
    <div class="d-flex no-block align-items-center">
      <div class="mr-3">
        <img src="{{ url('imagenes/categoria_icon3.png') }}" alt="user"
             class="rounded-circle" width="45" height="45" loading="lazy"/>
      </div>
      <div>
        <h5 class="text-dark mb-0 font-16 font-weight-medium">{{ $generico->nombre }}</h5>
      </div>
    </div>
  </td>
  <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
    <span style="background: {{ $generico->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                  color: {{ $generico->estado == 'Activo' ? '#159258' : '#780909' }};
                  padding:4px;border-radius:4px;">
      {{ $generico->estado }}
    </span>
  </td>
  <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
    <a href="#" class="text-success btn-editar-generico" data-id="{{ $generico->id }}">
      <i data-feather="edit"></i>
    </a>

    @if ($generico->estado === 'Activo')
      <form action="{{ route('genericos.desactivar', $generico->id) }}" method="POST" style="display:inline;"
            id="form-desactivar-{{ $generico->id }}">
        @csrf @method('PUT')
        <a href="#" class="text-danger" onclick="event.preventDefault(); confirmarDesactivacion({{ $generico->id }});">
          <i data-feather="trash-2"></i>
        </a>
      </form>
    @else
      <form action="{{ route('genericos.activar', $generico->id) }}" method="POST" style="display:inline;"
            id="form-activar-{{ $generico->id }}">
        @csrf @method('PUT')
        <a href="#" class="text-success" onclick="event.preventDefault(); confirmarActivacion({{ $generico->id }});">
          <i data-feather="refresh-ccw"></i>
        </a>
      </form>
    @endif
  </td>
</tr>
@endforeach
