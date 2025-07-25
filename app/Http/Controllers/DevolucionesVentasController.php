<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DevolucionesVentas;
use App\Models\Productos;
use App\Models\Ventas;
use App\Models\Movimientos;

class DevolucionesVentasController extends Controller
{
    public function index()
    {
        $devoluciones = DevolucionesVentas::with(['venta.cliente', 'producto', 'usuario'])->latest()->get();
        return view('devoluciones.index', compact('devoluciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'motivo' => 'required|string|max:255',
            'id_producto' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $venta = Ventas::findOrFail($request->venta_id);

        // Validamos si ya está devuelta o anulada
        if ($venta->estado !== 'Activo') {
            return redirect()->back()->with('error', 'Esta venta no puede ser devuelta.');
        }

        // Registrar devolución
        DevolucionesVentas::create([
            'id_venta' => $request->venta_id,
            'id_producto' => $request->id_producto,
            'usuario_id' => Auth::id(),
            'motivo' => $request->motivo,
            'cantidad' => $request->cantidad,
            'fecha' => now(),
        ]);

        // ✅ Aumentar stock (cantidad) del producto devuelto
        $producto = Productos::find($request->id_producto);

        if ($producto) {
            $stockAnterior = $producto->cantidad;
            $producto->cantidad += $request->cantidad;
            $producto->save();

            // Registrar el movimiento en el Kardex
            Movimientos::create([
                'id_producto'     => $producto->id,
                'tipo_movimiento' => 'Entrada',
                'origen'          => 'Devolución',
                'documento_ref'   => $venta->codigo,
                'fecha'           => now(),
                'cantidad'        => $request->cantidad,
                'stock_anterior'  => $stockAnterior,
                'stock_actual'    => $producto->cantidad,
                'observacion'     => 'Devolución por motivo: ' . $request->motivo,
                'usuario_id'      => Auth::id(),
            ]);
        }

        // Cambiar el estado de la venta
        $venta->estado = 'Devuelto';
        $venta->save();

        return redirect()->route('ventas.historial')->with('success', 'Venta devuelta correctamente.');
    }



    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $devoluciones = DevolucionesVentas::with(['venta.cliente', 'producto', 'usuario'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->whereHas('venta', function ($q) use ($buscar) {
                        $q->where('codigo', 'LIKE', "%$buscar%")
                            ->orWhere('fecha', 'LIKE', "%$buscar%")
                            ->orWhereHas('cliente', function ($q) use ($buscar) {
                                $q->where('nombre', 'LIKE', "%$buscar%")
                                    ->orWhere('apellidos', 'LIKE', "%$buscar%");
                            });
                    })
                        ->orWhereHas('producto', function ($q) use ($buscar) {
                            $q->where('descripcion', 'LIKE', "%$buscar%");
                        })
                        ->orWhereHas('usuario', function ($q) use ($buscar) {
                            $q->where('name', 'LIKE', "%$buscar%");
                        })
                        ->orWhere('motivo', 'LIKE', "%$buscar%");
                });
            })
            ->orderBy('fecha', 'desc')
            ->get();

        return view('devoluciones.partials.tabla', compact('devoluciones'));
    }
}
