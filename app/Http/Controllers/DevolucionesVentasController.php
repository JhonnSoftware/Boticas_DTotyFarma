<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DevolucionesVentas;
use App\Models\Productos;
use App\Models\Ventas;
use App\Models\Movimientos;
use Carbon\Carbon;
use App\Exports\DevolucionesVentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

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

        return view('devoluciones.partials.tabla', compact('devoluciones'));
    }

    public function exportar(Request $request, $formato)
    {
        $devoluciones = DevolucionesVentas::with(['venta.cliente', 'producto', 'usuario'])->latest()->get();

        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('exportaciones.devoluciones_pdf', compact('devoluciones'));
            return $pdf->download('devoluciones_ventas.pdf');
        }

        if ($formato === 'xlsx') {
            return Excel::download(new DevolucionesVentasExport, 'devoluciones_ventas.xlsx');
        }

        if ($formato === 'csv') {
            return Excel::download(new DevolucionesVentasExport, 'devoluciones_ventas.csv');
        }

        if ($formato === 'txt') {
            $contenido = '';
            foreach ($devoluciones as $d) {
                $contenido .= implode("\t", [
                    $d->venta->codigo,
                    $d->producto->descripcion,
                    $d->cantidad,
                    $d->motivo,
                    $d->venta->cliente->nombre . ' ' . $d->venta->cliente->apellidos,
                    $d->usuario->name,
                    $d->fecha,
                ]) . "\n";
            }

            return response($contenido)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="devoluciones_ventas.txt"');
        }

        return back()->with('error', 'Formato de exportación no válido.');
    }
}
