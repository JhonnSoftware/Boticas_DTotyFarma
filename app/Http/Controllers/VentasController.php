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
use App\Exports\VentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class VentasController extends Controller
{
    public function index()
    {
        $clientes   = Clientes::select('id', 'nombre', 'apellidos', 'dni')->orderBy('nombre')->get();
        $pagos      = TipoPagos::select('id', 'nombre')->orderBy('nombre')->get();
        $documentos = Documentos::select('id', 'nombre')->orderBy('nombre')->get();

        // =============================
        //   PRODUCTOS AGRUPADOS POR LOTE
        // =============================

        // === AGRUPAR POR NOMBRE Y LABORATORIO ===
        $productos = Productos::select([
            'productos.id',
            'productos.descripcion',
            'productos.presentacion',
            'productos.laboratorio',
            'productos.lote',
            'productos.fecha_vencimiento',
            'productos.cantidad',
            'productos.unidades_por_blister',
            'productos.unidades_por_caja',
            'productos.precio_venta',
            'productos.precio_venta_blister',
            'productos.precio_venta_caja',
            'productos.descuento',
            'productos.descuento_blister',
            'productos.descuento_caja',
            'productos.foto',
            'productos.id_generico'
        ])
            ->with(['generico:id,nombre', 'categorias:id,nombre'])
            ->where('productos.cantidad', '>', 0)                 // solo con stock
            // ->whereDate('productos.fecha_vencimiento','>=', now()) // opcional: no mostrar vencidos
            ->orderBy('productos.descripcion')
            ->orderBy('productos.fecha_vencimiento')              // primero los que vencen antes
            ->get()
            ->groupBy(function ($p) {
                // clave estable: nombre + laboratorio (limpio/minúsculas)
                return mb_strtolower(trim($p->descripcion)) . '|' . mb_strtolower(trim($p->laboratorio ?? ''));
            })
            ->map(function ($grupo) {
                // FEFO + desempates: fecha_venc asc, luego lote asc, luego id asc
                return $grupo->sortBy([
                    ['fecha_vencimiento', 'asc'],
                    ['lote', 'asc'],
                    ['id', 'asc'],
                ])->first();
            })
            ->values();


        // =============================
        //   SERIE / NÚMERO DE VENTA
        // =============================

        $serie  = 'TI001';
        $ultimo = Ventas::latest()->first();
        $numero = $ultimo ? str_pad($ultimo->id + 1, 6, '0', STR_PAD_LEFT) : '000001';

        return view('ventas.index', compact('clientes', 'productos', 'pagos', 'documentos', 'serie', 'numero'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cliente'       => 'required|exists:clientes,id',
            // OJO: ajusta los nombres de tabla si tus tablas son "documentos"/"tipopagos"
            'id_documento'     => 'required|exists:documento,id',
            'id_pago'          => 'required|exists:tipopago,id',

            'productos'        => 'required|array|min:1',
            'productos.*'      => 'exists:productos,id',

            'cantidades'       => 'required|array',
            'cantidades.*'     => 'numeric|min:1',

            'precios'          => 'required|array',
            'precios.*'        => 'numeric|min:0',

            'descuentos'       => 'required|array',
            'descuentos.*'     => 'numeric|min:0',

            'unidades_venta'   => 'required|array',
            'unidades_venta.*' => 'in:unidad,blister,caja',
        ]);

        // ===== Helpers: ratios y conversión a unidades =====
        $ratio = function (Productos $p): array {
            $upb = $p->unidades_por_blister ? (int)$p->unidades_por_blister : null; // unidades por blíster
            $uxc = $p->unidades_por_caja    ? (int)$p->unidades_por_caja    : null; // unidades por caja
            return [$upb, $uxc];
        };

        $toUnits = function (int $qty, string $unitKind, Productos $p) use ($ratio): int {
            [$upb, $uxc] = $ratio($p);
            switch ($unitKind) {
                case 'unidad':
                    // Romper caja permitido: 1 unidad = 1 unidad
                    return $qty;
                case 'blister':
                    if (!$upb) abort(400, "El producto '{$p->descripcion}' no maneja blíster.");
                    return $qty * $upb;
                case 'caja':
                    if (!$uxc) abort(400, "El producto '{$p->descripcion}' no maneja caja.");
                    return $qty * $uxc;
                default:
                    return $qty;
            }
        };

        // ===== Validación de stock por presentación =====
        foreach ($request->productos as $i => $id_producto) {
            $p = Productos::find($id_producto);
            if (!$p) {
                return back()->with('error', "El producto con ID $id_producto no existe.");
            }

            $qtyUI    = (int)($request->cantidades[$i] ?? 0);
            $unitKind = (string)($request->unidades_venta[$i] ?? 'unidad');
            [$upb, $uxc] = $ratio($p);

            $totalU   = (int)($p->cantidad ?? 0);                 // total en unidades (incluye cajas)
            $cajas    = ($uxc && $uxc > 0) ? intdiv($totalU, $uxc) : 0;
            $sueltas  = ($uxc && $uxc > 0) ? ($totalU % $uxc)      : $totalU;
            $blisters = ($upb && $upb > 0) ? intdiv($sueltas, $upb) : 0; // blísteres desde sueltas

            if ($unitKind === 'caja') {
                if (!$uxc) return back()->with('error', "El producto '{$p->descripcion}' no tiene unidades por caja definidas.");
                if ($qtyUI > $cajas) return back()->with('error', "No hay cajas suficientes de '{$p->descripcion}'. Disponibles: $cajas.");
            } elseif ($unitKind === 'blister') {
                if (!$upb) return back()->with('error', "El producto '{$p->descripcion}' no tiene unidades por blíster definidas.");
                if ($qtyUI > $blisters) return back()->with('error', "No hay blísteres suficientes de '{$p->descripcion}'. Disponibles: $blisters.");
            } else { // unidad — permitir romper caja => validar contra totalU
                if ($qtyUI > $totalU) {
                    return back()->with('error', "No hay unidades suficientes de '{$p->descripcion}'. Disponibles: $totalU.");
                }
            }

            // Validación final en unidades base (anti-carrera)
            $qtyUnits = $toUnits($qtyUI, $unitKind, $p);
            if ($totalU < $qtyUnits) {
                return back()->with('error', "Stock insuficiente para '{$p->descripcion}'.");
            }
        }

        // ===== Calcular totales (según lo que viene del UI) =====
        $subtotal        = 0.0;
        $descuento_total = 0.0;

        foreach ($request->productos as $i => $id_producto) {
            $cantidad  = (float)$request->cantidades[$i];
            $precio    = (float)$request->precios[$i];
            $descuento = (float)$request->descuentos[$i];

            $subtotal        += ($cantidad * $precio);
            $descuento_total += $descuento;
        }

        $total_final = $subtotal - $descuento_total;
        $igv = 0; // ajusta si corresponde

        // ===== Transacción que crea la venta y descuenta stock =====
        $venta = DB::transaction(function () use ($request, $toUnits, $total_final, $descuento_total) {
            $nuevoId = (Ventas::max('id') ?? 0) + 1;

            // Crear la venta
            $venta = Ventas::create([
                'codigo'          => 'TI001-' . str_pad($nuevoId, 6, '0', STR_PAD_LEFT),
                'id_cliente'      => $request->id_cliente,
                'id_documento'    => $request->id_documento,
                'id_pago'         => $request->id_pago,
                'fecha'           => Carbon::now(),
                'estado'          => 'Activo',
                'usuario_id'      => Auth::id(),
                'total'           => $total_final,
                'igv'             => 0,
                'descuento_total' => $descuento_total,
            ]);

            // Detalles + actualización de stock
            foreach ($request->productos as $i => $id_producto) {
                // Bloquear fila para evitar condiciones de carrera
                $producto = Productos::lockForUpdate()->find($id_producto);
                if (!$producto) {
                    throw new \Exception("El producto con ID {$id_producto} no existe (durante la transacción).");
                }

                $cantidadUI  = (int)$request->cantidades[$i];
                $precioUI    = (float)$request->precios[$i];
                $descuentoUI = (float)$request->descuentos[$i];
                $unidadUI    = (string)($request->unidades_venta[$i] ?? 'unidad');

                // Convertir a unidades base
                $cantidadEnUnidades = $toUnits($cantidadUI, $unidadUI, $producto);

                // Crear detalle
                DetalleVentas::create([
                    'id_venta'    => $venta->id,
                    'id_producto' => $id_producto,
                    'cantidad'    => $cantidadUI, // cantidad en la presentación elegida
                    'precio'      => $precioUI,   // precio por esa presentación
                    'sub_total'   => ($cantidadUI * $precioUI) - $descuentoUI,
                ]);

                // Descontar SIEMPRE del stock base (unidades)
                $stockAnterior = (int)$producto->cantidad;
                $nuevoStockU   = $stockAnterior - $cantidadEnUnidades;
                if ($nuevoStockU < 0) {
                    throw new \Exception("Stock insuficiente (condición de carrera) en '{$producto->descripcion}'.");
                }

                // Recalcular contadores derivados segun ratios fijos (si existen)
                $upb = $producto->unidades_por_blister ? (int)$producto->unidades_por_blister : null;
                $uxc = $producto->unidades_por_caja    ? (int)$producto->unidades_por_caja    : null;

                $producto->cantidad         = $nuevoStockU;                     // unidades
                $producto->cantidad_blister = $upb ? intdiv($nuevoStockU, $upb) : null; // derivados
                $producto->cantidad_caja    = $uxc ? intdiv($nuevoStockU, $uxc) : null; // derivados
                $producto->save();

                // Registrar movimiento
                Movimientos::create([
                    'id_producto'     => $producto->id,
                    'tipo_movimiento' => 'Salida',
                    'origen'          => 'Venta',
                    'documento_ref'   => $venta->codigo,
                    'fecha'           => now(),
                    'cantidad'        => -$cantidadEnUnidades, // negativa (UNIDADES)
                    'stock_anterior'  => $stockAnterior,
                    'stock_actual'    => $nuevoStockU,
                    'observacion'     => "Venta ({$unidadUI}) => -{$cantidadEnUnidades} unid",
                    'usuario_id'      => Auth::id(),
                ]);
            }

            return $venta;
        });

        // Voucher si corresponde
        $documento = Documentos::find($request->id_documento);
        if ($documento && strtolower($documento->nombre) === 'voucher') {
            $pdf = Pdf::loadView('ventas.voucher_pdf', ['venta' => $venta])
                ->setPaper([0, 0, 250, 800], 'portrait');

            $nombreArchivo = 'voucher_' . $venta->codigo . '.pdf';
            $ruta = storage_path('app/public/vouchers/' . $nombreArchivo);
            $pdf->save($ruta);

            $urlPDF = url("storage/vouchers/$nombreArchivo");

            return redirect()
                ->route('ventas.index')
                ->with([
                    'success'      => 'Venta registrada correctamente con Voucher.',
                    'imprimir'     => $venta->id,
                    'codigo_venta' => $venta->codigo,
                    'voucher_url'  => $urlPDF,
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
        $buscar      = $request->input('buscar');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');

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
            ->when($fechaInicio, function ($query) use ($fechaInicio) {
                $query->whereDate('fecha', '>=', $fechaInicio);
            })
            ->when($fechaFin, function ($query) use ($fechaFin) {
                $query->whereDate('fecha', '<=', $fechaFin);
            })
            ->orderBy('fecha', 'desc')
            ->get();

        return view('ventas.partials.tabla', compact('ventas'));
    }

    public function exportar(Request $request, $formato)
    {
        $ventas = Ventas::with(['cliente', 'pago', 'documento', 'usuario'])->get();

        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('exportaciones.ventas_pdf', compact('ventas'));
            return $pdf->download('ventas.pdf');
        }

        if ($formato === 'xlsx') {
            return Excel::download(new VentasExport, 'ventas.xlsx');
        }

        if ($formato === 'csv') {
            return Excel::download(new VentasExport, 'ventas.csv');
        }

        if ($formato === 'txt') {
            $contenido = '';
            foreach ($ventas as $v) {
                $contenido .= implode("\t", [
                    $v->codigo,
                    $v->cliente->nombre . ' ' . $v->cliente->apellidos,
                    $v->documento->nombre,
                    $v->pago->nombre,
                    $v->total,
                    $v->igv,
                    $v->descuento_total,
                    $v->fecha,
                    $v->estado,
                    $v->usuario->name,
                ]) . "\n";
            }

            return response($contenido)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="ventas.txt"');
        }

        return back()->with('error', 'Formato de exportación no válido.');
    }
}
