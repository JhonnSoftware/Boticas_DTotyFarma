@foreach ($productos as $producto)
    <tr>
        <td class="border-top-0 px-2 py-4">
            <div class="d-flex no-block align-items-center">
                <div class="mr-3">
                    <img src="{{ url('imagenes/producto_icon1.png') }}" alt="user" class="rounded-circle" width="45"
                        height="45" />
                </div>
                <div class="">
                    <h5 class="text-dark mb-0 font-16 font-weight-medium">
                        {{ $producto->codigo }}</h5>
                </div>
            </div>
        </td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->descripcion }}</td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->presentacion }}</td>
        <!--
                                                                    <td class="border-top-0 text-dark px-2 py-4">{{ $producto->laboratorio }}</td>
                                                                    <td class="border-top-0 text-dark px-2 py-4">{{ $producto->lote }}</td>
                                                                    <td class="border-top-0 text-dark px-2 py-4">{{ $producto->cantidad }}</td>
                                                                    <td class="border-top-0 text-dark px-2 py-4">{{ $producto->stock_minimo }}
                                                                    </td>
                                                                    <td class="border-top-0 text-dark px-2 py-4">{{ $producto->descuento }}</td>
                                                                    <td class="border-top-0 text-dark px-2 py-4">
                                                                        {{ $producto->fecha_vencimiento }}
                                                                    </td>
                                                                    -->
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->precio_venta }}
        </td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->precio_venta_blister }}
        </td>
        <td class="border-top-0 text-dark px-2 py-4">{{ $producto->precio_venta_caja }}
        </td>
        <td>
            <img src="{{ url($producto->foto) }}" alt="Foto del producto" width="70">

        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <span
                style="background: {{ $producto->estado == 'Activo' ? '#6ff073' : '#f06f6f' }};
                                                            color: {{ $producto->estado == 'Activo' ? '#159258' : '#780909' }};
                                                            padding: 4px; border-radius:4px;">
                {{ $producto->estado }}
            </span>
        </td>
        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">
            <!--
                                                <a href="#" class="text-primary me-2"><i
                                                        data-feather="eye"></i></a> -->

            <a href="#" class="text-success" data-bs-toggle="modal"
                data-bs-target="#editarProducto{{ $producto->id }}">
                <i data-feather="edit"></i>
            </a>


            @if ($producto->estado === 'Activo')
                <form action="{{ route('productos.desactivar', $producto->id) }}" method="POST"
                    style="display:inline;" id="form-desactivar-{{ $producto->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-danger"
                        onclick="event.preventDefault(); confirmarDesactivacion({{ $producto->id }});">
                        <i data-feather="trash-2"></i>
                    </a>
                </form>
            @else
                <form action="{{ route('productos.activar', $producto->id) }}" method="POST" style="display:inline;"
                    id="form-activar-{{ $producto->id }}">
                    @csrf
                    @method('PUT')
                    <a href="#" class="text-success"
                        onclick="event.preventDefault(); confirmarActivacion({{ $producto->id }});">
                        <i data-feather="refresh-ccw"></i>
                    </a>
                </form>
            @endif
        </td>
    </tr>
    
@endforeach
