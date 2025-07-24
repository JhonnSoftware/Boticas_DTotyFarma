<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DevolucionesCompras;
use App\Models\Productos;
use App\Models\Compras;
use App\Models\Movimientos;

class DevolucionesComprasController extends Controller
{
    public function index()
    {
        $devoluciones = DevolucionesCompras::with(['compra.proveedor', 'producto', 'usuario'])->latest()->get();
        return view('devolucionesCompras.index', compact('devoluciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_compra' => 'required|exists:compras,id',
            'motivo' => 'required|string|max:255',
            'id_producto' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $compra = Compras::findOrFail($request->id_compra);

        if ($compra->estado !== 'Activo') {
            return redirect()->back()->with('error', 'Esta compra no puede ser devuelta.');
        }

        // Registrar devolución
        DevolucionesCompras::create([
            'id_compra' => $request->id_compra,
            'id_producto' => $request->id_producto,
            'usuario_id' => Auth::id(),
            'motivo' => $request->motivo,
            'cantidad' => $request->cantidad,
            'fecha' => now(),
        ]);

        $producto = Productos::find($request->id_producto);

        if ($producto) {
            $stockAnterior = $producto->cantidad;
            $producto->cantidad -= $request->cantidad;
            $producto->save();

            // Registrar movimiento en el Kardex
            Movimientos::create([
                'id_producto'     => $producto->id,
                'tipo_movimiento' => 'Salida',
                'origen'          => 'Devolución',
                'documento_ref'   => $compra->codigo,
                'fecha'           => now(),
                'cantidad'        => -$request->cantidad, // salida = cantidad negativa
                'stock_anterior'  => $stockAnterior,
                'stock_actual'    => $producto->cantidad,
                'observacion'     => 'Devolución a proveedor: ' . $request->motivo,
                'usuario_id'      => Auth::id(),
            ]);
        }

        // Cambiar estado
        $compra->estado = 'Devuelto';
        $compra->save();

        return redirect()->route('compras.historial')->with('success', 'Compra devuelta correctamente.');
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $devoluciones = DevolucionesCompras::with(['compra.proveedor', 'producto', 'usuario'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->whereHas('compra', function ($q) use ($buscar) {
                        $q->where('codigo', 'LIKE', "%$buscar%")
                            ->orWhere('fecha', 'LIKE', "%$buscar%")
                            ->orWhereHas('proveedor', function ($q) use ($buscar) {
                                $q->where('nombre', 'LIKE', "%$buscar%");
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

        return view('devolucionesCompras.partials.tabla', compact('devoluciones'));
    }
}
