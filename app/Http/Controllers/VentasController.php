<?php

namespace App\Http\Controllers;

use App\Models\Ventas;
use App\Models\DetalleVentas;
use App\Models\Clientes;
use App\Models\Productos;
use App\Models\TipoPagos;
use App\Models\Documentos;
use App\Models\Movimientos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class VentasController extends Controller
{
    public function index()
    {
        $clientes = Clientes::all();
        $productos = Productos::all();
        $pagos = TipoPagos::all();
        $documentos = Documentos::all();

        // Generar el siguiente número de serie (esto puede ser más dinámico luego)
        $serie = 'TI001';
        $ultimo = Ventas::latest()->first();
        $numero = $ultimo ? str_pad($ultimo->id + 1, 6, '0', STR_PAD_LEFT) : '000001';

        return view('ventas.index', compact('clientes', 'productos', 'pagos', 'documentos', 'serie', 'numero'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required|exists:clientes,id',
            'id_documento' => 'required|exists:documento,id',
            'id_pago' => 'required|exists:tipopago,id',
            'productos' => 'required|array|min:1',
            'productos.*' => 'exists:productos,id',
            'cantidades' => 'required|array',
            'precios' => 'required|array',
            'descuentos' => 'required|array',
        ]);

        // Validar stock disponible antes de registrar la venta
        foreach ($request->productos as $i => $id_producto) {
            $producto = Productos::find($id_producto);
            $cantidadSolicitada = $request->cantidades[$i];

            if (!$producto) {
                return back()->with('error', "El producto con ID $id_producto no existe.");
            }

            if ($producto->cantidad < $cantidadSolicitada) {
                return back()->with('error', "Stock insuficiente para el producto '{$producto->descripcion}'. Stock actual: {$producto->cantidad}, solicitado: {$cantidadSolicitada}.");
            }
        }

        // Calcular totales
        $subtotal = 0;
        $descuento_total = 0;

        foreach ($request->productos as $i => $id_producto) {
            $cantidad = $request->cantidades[$i];
            $precio = $request->precios[$i];
            $descuento = $request->descuentos[$i];
            $subtotal += ($cantidad * $precio);
            $descuento_total += $descuento;
        }

        $total_final = $subtotal - $descuento_total;
        $igv = 0; // Puedes ajustarlo luego si aplicas impuestos

        // Crear la venta
        $venta = Ventas::create([
            'codigo' => 'TI001-' . str_pad(Ventas::max('id') + 1, 6, '0', STR_PAD_LEFT),
            'id_cliente' => $request->id_cliente,
            'id_documento' => $request->id_documento,
            'id_pago' => $request->id_pago,
            'fecha' => Carbon::now(),
            'estado' => 'Activo',
            'usuario_id' => Auth::id(),
            'total' => $total_final,
            'igv' => $igv,
            'descuento_total' => $descuento_total,
        ]);

        // Crear los detalles de venta
        foreach ($request->productos as $i => $id_producto) {
            $cantidad = $request->cantidades[$i];

            // Crear detalle de venta
            DetalleVentas::create([
                'id_venta' => $venta->id,
                'id_producto' => $id_producto,
                'cantidad' => $cantidad,
                'precio' => $request->precios[$i],
                'sub_total' => ($cantidad * $request->precios[$i]) - $request->descuentos[$i],
            ]);

            // Actualizar stock del producto
            $producto = Productos::find($id_producto);
            if ($producto) {
                $stockAnterior = $producto->cantidad;
                $producto->cantidad -= $cantidad;
                $producto->save();

                Movimientos::create([
                    'id_producto'     => $producto->id,
                    'tipo_movimiento' => 'Salida',
                    'origen'          => 'Venta',
                    'documento_ref'   => $venta->codigo,
                    'fecha'           => now(),
                    'cantidad'        => -$cantidad, // salida = cantidad negativa
                    'stock_anterior'  => $stockAnterior,
                    'stock_actual'    => $producto->cantidad,
                    'observacion'     => 'Venta registrada',
                    'usuario_id'      => Auth::id(),
                ]);
            }
        }

        $documento = Documentos::find($request->id_documento);

        if ($documento && strtolower($documento->nombre) === 'voucher') {
            // Renderizar el PDF desde la vista
            $pdf = Pdf::loadView('ventas.voucher_pdf', ['venta' => $venta])->setPaper([0, 0, 250, 800], 'portrait');;

            // Guardarlo automáticamente
            $nombreArchivo = 'voucher_' . $venta->codigo . '.pdf';
            $ruta = storage_path('app/public/vouchers/' . $nombreArchivo);
            $pdf->save($ruta);

            // Agregar URL accesible públicamente al PDF
            $urlPDF = url("storage/vouchers/$nombreArchivo");

            return redirect()
                ->route('ventas.index')
                ->with([
                    'success' => 'Venta registrada correctamente con Voucher.',
                    'imprimir' => $venta->id,
                    'codigo_venta' => $venta->codigo,
                    'voucher_url' => $urlPDF, // <-- AQUI
                ]);
        }


        return redirect()->route('ventas.index')->with('success', 'Venta registrada correctamente.');
    }

    public function voucher($id)
    {
        $venta = Ventas::with('cliente', 'documento', 'usuario', 'detalles.producto')->findOrFail($id);
        return view('ventas.voucher', compact('venta'));
    }

    public function historial()
    {
        $ventas = Ventas::with(['cliente', 'pago', 'documento', 'usuario'])
            ->latest()
            ->get();

        return view('ventas.historial', compact('ventas'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $ventas = Ventas::with(['cliente', 'pago', 'documento', 'usuario'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('codigo', 'LIKE', "%$buscar%")
                        ->orWhereHas('cliente', function ($q) use ($buscar) {
                            $q->where('nombre', 'LIKE', "%$buscar%")
                                ->orWhere('apellidos', 'LIKE', "%$buscar%");
                        })
                        ->orWhereHas('documento', function ($q) use ($buscar) {
                            $q->where('nombre', 'LIKE', "%$buscar%");
                        })
                        ->orWhereHas('usuario', function ($q) use ($buscar) {
                            $q->where('name', 'LIKE', "%$buscar%");
                        })
                        ->orWhere('fecha', 'LIKE', "%$buscar%");
                });
            })
            ->orderBy('fecha', 'desc') // se aplica siempre
            ->get();

        return view('ventas.partials.tabla', compact('ventas'));
    }
}
