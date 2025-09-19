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
use App\Models\Categorias;
use App\Models\Clases;
use App\Models\Genericos;
use App\Exports\ComprasExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class ComprasController extends Controller
{
    public function index()
    {
        $proveedores = Proveedores::orderBy('nombre')->get();
        $productos   = Productos::latest()->get();
        $tipopagos   = TipoPagos::all();
        $documentos  = Documentos::all();

        //Generar el siguiente número de serie para la compra
        $serie  = 'COMP';
        $ultimo = Compras::latest('id')->first();
        $numero = $ultimo ? str_pad($ultimo->id + 1, 5, '0', STR_PAD_LEFT) : '00001';
        $codigo = $serie . '-' . $numero;

        //Catálogos para el modal de "nuevo producto"
        $categorias = Categorias::select('id', 'nombre')->orderBy('nombre')->get();
        $clases     = Clases::select('id', 'nombre')->orderBy('nombre')->get();
        $genericos  = Genericos::select('id', 'nombre')->orderBy('nombre')->get();

        // Igual que en ProductosController:
        $ultimoProducto = Productos::latest('id')->first();
        $nextId = $ultimoProducto ? $ultimoProducto->id + 1 : 1;
        $nuevoCodigo = 'P000-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $compras = Compras::with(['proveedor', 'documento', 'pago', 'usuario'])->latest()->get();

        return view('compras.index', compact(
            'compras',
            'proveedores',
            'productos',
            'tipopagos',
            'documentos',
            'codigo',
            'categorias',
            'clases',
            'genericos',
            'nuevoCodigo'
        ));
    }


    public function store(Request $request)
    {
        // 1) Validación
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id',
            'id_documento' => 'required|exists:documento,id',
            'id_pago'      => 'required|exists:tipopago,id',

            'productos'    => 'required|array|min:1',

            // por ítem:
            'productos.*.id_producto'        => 'required|exists:productos,id',
            'productos.*.cantidad_unidad'    => 'nullable|integer|min:0',
            'productos.*.cantidad_blister'   => 'nullable|integer|min:0',
            'productos.*.cantidad_caja'      => 'nullable|integer|min:0',

            'productos.*.precio_unidad'      => 'nullable|numeric|min:0',
            'productos.*.precio_blister'     => 'nullable|numeric|min:0',
            'productos.*.precio_caja'        => 'nullable|numeric|min:0',

            'productos.*.lote'               => 'nullable|string|max:100',
            'productos.*.laboratorio'        => 'nullable|string|max:150',
            'productos.*.fecha_vencimiento'  => 'nullable|date',

            'archivo_factura' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // 2) Subir archivo si existe
        $archivoNombre = null;
        if ($request->hasFile('archivo_factura')) {
            $archivo = $request->file('archivo_factura');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();
            $archivo->storeAs('public/orden_compra', $archivoNombre);
        }

        // 3) Calcular totales a partir del desglose U/B/C
        $total = 0;

        foreach ($request->productos as $item) {
            $nU = (int)($item['cantidad_unidad']  ?? 0);
            $nB = (int)($item['cantidad_blister'] ?? 0);
            $nC = (int)($item['cantidad_caja']    ?? 0);

            $pU = (float)($item['precio_unidad']  ?? 0);
            $pB = (float)($item['precio_blister'] ?? 0);
            $pC = (float)($item['precio_caja']    ?? 0);

            $subU = $nU * $pU;
            $subB = $nB * $pB;
            $subC = $nC * $pC;

            $total += ($subU + $subB + $subC);
        }

        // Si quieres IGV=18%:
        $igv = 0; // cámbialo si corresponde
        $subtotal = $total - $igv;

        // 4) Crear la compra
        $compra = Compras::create([
            'codigo'         => 'COMP-' . str_pad(Compras::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'id_proveedor'   => $request->id_proveedor,
            'id_documento'   => $request->id_documento,
            'id_pago'        => $request->id_pago,
            'fecha'          => Carbon::now(),
            'estado'         => 'Activo',
            'usuario_id'     => Auth::id(),
            'total'          => $total,
            'igv'            => $igv,
            'archivo_factura' => $archivoNombre,
        ]);

        // 5) Crear detalles + actualizar producto + movimientos
        foreach ($request->productos as $item) {
            $producto = Productos::find($item['id_producto']);
            if (!$producto) {
                // si por alguna razón no existe, saltamos
                continue;
            }

            $nU = (int)($item['cantidad_unidad']  ?? 0);
            $nB = (int)($item['cantidad_blister'] ?? 0);
            $nC = (int)($item['cantidad_caja']    ?? 0);

            $pU = is_numeric($item['precio_unidad']  ?? null)  ? (float)$item['precio_unidad']  : null;
            $pB = is_numeric($item['precio_blister'] ?? null)  ? (float)$item['precio_blister'] : null;
            $pC = is_numeric($item['precio_caja']    ?? null)  ? (float)$item['precio_caja']    : null;

            $subU = ($pU ?? 0) * $nU;
            $subB = ($pB ?? 0) * $nB;
            $subC = ($pC ?? 0) * $nC;

            $subTotalLinea = $subU + $subB + $subC;

            // Cantidad total: si tu detalle guarda una sola "cantidad", usamos la suma
            $cantidadTotal = $nU + $nB + $nC;

            // Precio unitario promedio para dejar consistente (subTotal / cantidad)
            $precioPromedio = $cantidadTotal > 0 ? ($subTotalLinea / $cantidadTotal) : 0;

            // 5.1) Guardar/actualizar atributos editables del producto
            // Solo si vienen informados (no pisamos con vacío)
            if (!empty($item['lote'])) {
                $producto->lote = $item['lote'];
            }
            if (!empty($item['laboratorio'])) {
                $producto->laboratorio = $item['laboratorio'];
            }
            if (!empty($item['fecha_vencimiento'])) {
                // Normalizamos a Y-m-d
                try {
                    $producto->fecha_vencimiento = Carbon::parse($item['fecha_vencimiento'])->format('Y-m-d');
                } catch (\Throwable $e) {
                    // si falla el parseo, lo ignoramos
                }
            }

            // 5.2) Actualizar stock
            $stockAnterior = $producto->cantidad ?? 0;

            // Si tu tabla tiene columnas por presentación:
            $tieneBlister = Schema::hasColumn('productos', 'cantidad_blister');
            $tieneCaja    = Schema::hasColumn('productos', 'cantidad_caja');

            if ($tieneBlister || $tieneCaja) {
                // sumamos por presentación si existen
                if ($tieneBlister) {
                    $producto->cantidad_blister = ($producto->cantidad_blister ?? 0) + $nB;
                }
                if ($tieneCaja) {
                    $producto->cantidad_caja = ($producto->cantidad_caja ?? 0) + $nC;
                }
                // mantenemos 'cantidad' para unidades sueltas
                $producto->cantidad = ($producto->cantidad ?? 0) + $nU;
            } else {
                // Sin columnas B/C: convertimos a unidades
                $uxb = (int)($producto->unidades_por_blister ?? 0);
                $uxc = (int)($producto->unidades_por_caja ?? 0);

                $unidadesDesdeB = ($uxb > 0) ? ($nB * $uxb) : 0;
                $unidadesDesdeC = ($uxc > 0) ? ($nC * $uxc) : 0;

                $producto->cantidad = ($producto->cantidad ?? 0) + $nU + $unidadesDesdeB + $unidadesDesdeC;
            }

            $producto->save();

            // 5.3) Detalle (compatibilidad con tu esquema actual)
            DetalleCompras::create([
                'id_compra'      => $compra->id,
                'id_producto'    => $producto->id,
                'cantidad'       => $cantidadTotal,
                'precio_unitario' => $precioPromedio,
                'sub_total'      => $subTotalLinea,
            ]);

            // 5.4) Movimiento
            $desglose = "U: {$nU}";
            if ($nB > 0) $desglose .= " | B: {$nB}";
            if ($nC > 0) $desglose .= " | C: {$nC}";

            Movimientos::create([
                'id_producto'     => $producto->id,
                'tipo_movimiento' => 'Entrada',
                'origen'          => 'Compra',
                'documento_ref'   => $compra->codigo,
                'fecha'           => now(),
                'cantidad'        => $cantidadTotal, // para el registro; si usas unidades netas, puedes adaptar
                'stock_anterior'  => $stockAnterior,
                'stock_actual'    => $producto->cantidad,
                'observacion'     => "Compra registrada ({$desglose})",
                'usuario_id'      => Auth::id(),
            ]);
        }

        return redirect()
            ->route('compras.index')
            ->with('success', 'Compra registrada correctamente.');
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
