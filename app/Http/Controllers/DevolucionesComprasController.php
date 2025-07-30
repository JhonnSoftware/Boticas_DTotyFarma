<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DevolucionesCompras;
use App\Models\Productos;
use App\Models\Compras;
use App\Models\Movimientos;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DevolucionesComprasExport;

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
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

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
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha', [
                    Carbon::parse($fechaInicio)->startOfDay(),
                    Carbon::parse($fechaFin)->endOfDay()
                ]);
            })
            ->when($fechaInicio && !$fechaFin, function ($query) use ($fechaInicio) {
                $query->whereDate('fecha', Carbon::parse($fechaInicio));
            })
            ->when(!$fechaInicio && $fechaFin, function ($query) use ($fechaFin) {
                $query->whereDate('fecha', Carbon::parse($fechaFin));
            })
            ->orderBy('fecha', 'desc')
            ->get();

        return view('devolucionesCompras.partials.tabla', compact('devoluciones'));
    }

    public function exportar($formato)
    {
        $devoluciones = DevolucionesCompras::with(['compra.proveedor', 'producto', 'usuario'])->latest()->get();

        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('exportaciones.devoluciones_compras_pdf', compact('devoluciones'));
            return $pdf->download('devoluciones_compras.pdf');
        }

        if ($formato === 'xlsx') {
            return Excel::download(new DevolucionesComprasExport, 'devoluciones_compras.xlsx');
        }

        if ($formato === 'csv') {
            return Excel::download(new DevolucionesComprasExport, 'devoluciones_compras.csv');
        }

        if ($formato === 'txt') {
            $contenido = '';
            foreach ($devoluciones as $d) {
                $contenido .= implode("\t", [
                    $d->compra->codigo ?? '',
                    $d->producto->descripcion ?? '',
                    $d->motivo,
                    $d->cantidad,
                    $d->fecha,
                    $d->usuario->name ?? '',
                ]) . "\n";
            }

            return response($contenido)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="devoluciones_compras.txt"');
        }

        return back()->with('error', 'Formato de exportación no válido.');
    }

}
