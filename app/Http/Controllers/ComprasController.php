<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Compras;
use App\Models\DetalleCompras;
use App\Models\Proveedores;
use App\Models\Productos;
use App\Models\TipoPagos;
use App\Models\Documentos;
use App\Models\Movimientos;
use App\Exports\ComprasExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ComprasController extends Controller
{
    public function index()
    {
        $proveedores = Proveedores::all();
        $productos = Productos::all();
        $tipopagos = TipoPagos::all();
        $documentos = Documentos::all();

        // Generar el siguiente número de serie para la compra
        $serie = 'COMP';
        $ultimo = Compras::latest()->first();
        $numero = $ultimo ? str_pad($ultimo->id + 1, 5, '0', STR_PAD_LEFT) : '00001';
        $codigo = $serie . '-' . $numero;

        $compras = Compras::with(['proveedor', 'documento', 'pago', 'usuario'])->latest()->get();

        return view('compras.index', compact('compras', 'proveedores', 'productos', 'tipopagos', 'documentos', 'codigo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id',
            'id_documento' => 'required|exists:documento,id',
            'id_pago' => 'required|exists:tipopago,id',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0.01',
            'archivo_factura' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Validación de archivo
        ]);

        // Calcular totales
        $subtotal = 0;
        foreach ($request->productos as $item) {
            $subtotal += $item['cantidad'] * $item['precio_unitario'];
        }

        $igv = 0; // O reemplaza por cálculo de IGV si es necesario
        $total = $subtotal;

        // Subir archivo si existe
        $archivoNombre = null;
        if ($request->hasFile('archivo_factura')) {
            $archivo = $request->file('archivo_factura');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();
            $archivo->storeAs('public/orden_compra', $archivoNombre);
        }

        // Crear la compra
        $compra = Compras::create([
            'codigo' => 'COMP-' . str_pad(Compras::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'id_proveedor' => $request->id_proveedor,
            'id_documento' => $request->id_documento,
            'id_pago' => $request->id_pago,
            'fecha' => Carbon::now(),
            'estado' => 'Activo',
            'usuario_id' => Auth::id(),
            'total' => $total,
            'igv' => $igv,
            'archivo_factura' => $archivoNombre,
        ]);

        // Crear detalles
        foreach ($request->productos as $item) {
            DetalleCompras::create([
                'id_compra' => $compra->id,
                'id_producto' => $item['id_producto'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'sub_total' => $item['cantidad'] * $item['precio_unitario'],
            ]);

            // Actualizar stock
            $producto = Productos::find($item['id_producto']);
            if ($producto) {
                $stockAnterior = $producto->cantidad;
                $producto->cantidad += $item['cantidad'];
                $producto->save();

                // Registrar movimiento tipo Entrada
                Movimientos::create([
                    'id_producto'     => $producto->id,
                    'tipo_movimiento' => 'Entrada',
                    'origen'          => 'Compra',
                    'documento_ref'   => $compra->codigo,
                    'fecha'           => now(),
                    'cantidad'        => $item['cantidad'], // positivo porque es entrada
                    'stock_anterior'  => $stockAnterior,
                    'stock_actual'    => $producto->cantidad,
                    'observacion'     => 'Compra registrada',
                    'usuario_id'      => Auth::id(),
                ]);
            }
        }

        return redirect()->route('compras.index')->with('success', 'Compra registrada correctamente.');
    }

    public function historial()
    {
        $compras = Compras::with(['proveedor', 'pago', 'documento', 'usuario'])
            ->latest()
            ->get();

        return view('compras.historial', compact('compras'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $compras = Compras::with(['proveedor', 'pago', 'documento', 'usuario'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('codigo', 'LIKE', "%$buscar%")
                        ->orWhere('fecha', 'LIKE', "%$buscar%")
                        ->orWhereHas('proveedor', function ($q) use ($buscar) {
                            $q->where('nombre', 'LIKE', "%$buscar%");
                        })
                        ->orWhereHas('documento', function ($q) use ($buscar) {
                            $q->where('nombre', 'LIKE', "%$buscar%");
                        })
                        ->orWhereHas('usuario', function ($q) use ($buscar) {
                            $q->where('name', 'LIKE', "%$buscar%");
                        });
                });
            })
            ->when($fechaInicio, function ($query) use ($fechaInicio) {
                $query->whereDate('fecha', '>=', $fechaInicio);
            })
            ->when($fechaFin, function ($query) use ($fechaFin) {
                $query->whereDate('fecha', '<=', $fechaFin);
            })
            ->orderBy('fecha', 'desc')
            ->get();

        return view('compras.partials.tabla', compact('compras'));
    }

    public function exportar($formato)
    {
        $compras = Compras::with(['proveedor', 'pago', 'documento', 'usuario'])->latest()->get();

        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('exportaciones.compras_pdf', compact('compras'));
            return $pdf->download('compras.pdf');
        }

        if ($formato === 'xlsx') {
            return Excel::download(new ComprasExport, 'compras.xlsx');
        }

        if ($formato === 'csv') {
            return Excel::download(new ComprasExport, 'compras.csv');
        }

        if ($formato === 'txt') {
            $contenido = '';
            foreach ($compras as $c) {
                $contenido .= implode("\t", [
                    $c->codigo,
                    $c->proveedor->nombre ?? '',
                    $c->documento->nombre ?? '',
                    $c->pago->nombre ?? '',
                    $c->total,
                    $c->estado,
                    $c->fecha,
                    $c->usuario->name ?? ''
                ]) . "\n";
            }

            return response($contenido)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="compras.txt"');
        }

        return back()->with('error', 'Formato no válido.');
    }
}
