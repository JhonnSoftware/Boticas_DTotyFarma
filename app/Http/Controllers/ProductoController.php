<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Categorias;
use App\Models\Clases;
use App\Models\Genericos;
use App\Models\Alertas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Exports\ProductosExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Productos::query();

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('descripcion', 'like', "%{$b}%")
                    ->orWhere('presentacion', 'like', "%{$b}%")
                    ->orWhere('laboratorio', 'like', "%{$b}%")
                    ->orWhere('lote', 'like', "%{$b}%")
                    ->orWhere('fecha_vencimiento', 'like', "%{$b}%")
                    ->orWhere('precio_compra', 'like', "%{$b}%")
                    ->orWhere('precio_venta', 'like', "%{$b}%");
            });
        }

        // ===== “No paginado” clásico, pero limitado por bloque =====
        $perBlock = (int) $request->query('perBlock', 200); // cuántas filas mostrar por carga
        $page     = max(1, (int) $request->query('page', 1));
        $offset   = ($page - 1) * $perBlock;

        // clonar para contar total sin afectar skip/take
        $baseForCount = (clone $query);
        $total = $baseForCount->count();

        // Trae solo el bloque actual (ASC = menor → mayor)
        $productos = $query->orderBy('id', 'asc')
            ->skip($offset)
            ->take($perBlock)
            ->get();

        $hasMore = ($offset + $productos->count()) < $total;

        // Catálogos (1 sola vez)
        $proveedores = Proveedores::select('id', 'nombre')->orderBy('nombre')->get();
        $categorias  = Categorias::select('id', 'nombre')->orderBy('nombre')->get();
        $clases      = Clases::orderBy('nombre')->get();
        $genericos   = Genericos::orderBy('nombre')->get();

        // Indicadores
        $productosStockBajo = Productos::whereColumn('cantidad', '<', 'stock_minimo')
            ->get(['id', 'codigo', 'descripcion', 'cantidad', 'stock_minimo']);

        $ultimoProducto = Productos::latest('id')->first();
        $nextId = $ultimoProducto ? $ultimoProducto->id + 1 : 1;
        $nuevoCodigo = 'P000-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $agotadosUnidad = Productos::where('cantidad', 0)->count();

        return view('productos.index', [
            'productos'          => $productos,
            'proveedores'        => $proveedores,
            'categorias'         => $categorias,
            'clases'             => $clases,
            'genericos'          => $genericos,
            'productosStockBajo' => $productosStockBajo,
            'nuevoCodigo'        => $nuevoCodigo,
            'agotadosUnidad'     => $agotadosUnidad,
            // para el “load more”
            'page'               => $page,
            'perBlock'           => $perBlock,
            'total'              => $total,
            'hasMore'            => $hasMore,
        ]);
    }

    public function editPartial($id)
    {
        if (!request()->ajax() && request()->header('X-Requested-With') !== 'XMLHttpRequest') {
            abort(404);
        }

        $producto    = Productos::with(['categorias'])->findOrFail($id);
        $proveedores = Proveedores::orderBy('nombre')->get(['id', 'nombre']);
        $categorias  = Categorias::orderBy('nombre')->get(['id', 'nombre']);
        $clases      = Clases::orderBy('nombre')->get(['id', 'nombre']);
        $genericos   = Genericos::orderBy('nombre')->get(['id', 'nombre']);

        // 👉 Unidades base = total - (blíster×ratio_blíster) - (caja×ratio_caja)
        $uXB = (int) ($producto->unidades_por_blister ?? 0);
        $uXC = (int) ($producto->unidades_por_caja ?? 0);

        $cantidadBase = (int) ($producto->cantidad ?? 0)
            - ((int) ($producto->cantidad_blister ?? 0)) * $uXB
            - ((int) ($producto->cantidad_caja ?? 0)) * $uXC;

        if ($cantidadBase < 0) $cantidadBase = 0;

        return view('productos.partials.modal_editar', compact(
            'producto',
            'proveedores',
            'categorias',
            'clases',
            'genericos',
            'cantidadBase' // <-- ¡importante!
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'descripcion' => 'required|string',
            'presentacion' => 'required|string|max:100',
            'laboratorio' => 'required|string|max:100',
            'lote' => 'required|string',
            'unidades_por_blister' => 'nullable|integer|min:0',
            'unidades_por_caja'    => 'nullable|integer|min:0',

            // cantidades ingresadas por el usuario (pueden combinarse)
            'cantidad'         => 'required|integer|min:0', // unidades
            'cantidad_blister' => 'nullable|integer|min:0',
            'cantidad_caja'    => 'nullable|integer|min:0',

            'stock_minimo' => 'required|integer|min:0',

            'descuento'         => 'required|numeric|min:0|max:100',
            'descuento_blister' => 'nullable|numeric|min:0|max:100',
            'descuento_caja'    => 'nullable|numeric|min:0|max:100',

            'fecha_vencimiento' => 'required|date|after_or_equal:today',

            'precio_compra'         => 'required|numeric|min:0',
            'precio_compra_blister' => 'nullable|numeric|min:0',
            'precio_compra_caja'    => 'nullable|numeric|min:0',
            'precio_venta'          => 'required|numeric|min:0',
            'precio_venta_blister'  => 'nullable|numeric|min:0',
            'precio_venta_caja'     => 'nullable|numeric|min:0',

            'foto'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'id_proveedor' => 'required|exists:proveedores,id',
            'id_clase'     => 'nullable|exists:clases,id',
            'id_generico'  => 'nullable|exists:genericos,id',
            'estado'       => 'required|in:Activo,Inactivo',

            'categorias'   => ['required', 'array', 'min:1'],
            'categorias.*' => ['integer', Rule::exists('categorias', 'id')],
        ]);

        // ===== Foto =====
        $rutaFoto = 'imagenes/productos/producto_defecto.jpg';
        if ($request->hasFile('foto')) {
            $fotoNombre = time() . '.' . $request->file('foto')->extension();
            $request->file('foto')->move(public_path('imagenes/productos'), $fotoNombre);
            $rutaFoto = 'imagenes/productos/' . $fotoNombre;
        }

        // ===== Ratios (0 o vacío -> null) =====
        $uXB = ($request->filled('unidades_por_blister') && (int)$request->unidades_por_blister > 0)
            ? (int)$request->unidades_por_blister : null;
        $uXC = ($request->filled('unidades_por_caja') && (int)$request->unidades_por_caja > 0)
            ? (int)$request->unidades_por_caja : null;

        // ===== Cantidades ingresadas =====
        $uUni = (int) $request->input('cantidad', 0);            // unidades
        $uBli = (int) $request->input('cantidad_blister', 0);    // blister
        $uCaj = (int) $request->input('cantidad_caja', 0);       // caja

        // ===== Total en unidades base =====
        $totalUnidades = $uUni
            + ($uXB ? ($uBli * $uXB) : 0)
            + ($uXC ? ($uCaj * $uXC) : 0);

        // ===== Precios derivados si no se ingresaron =====
        $pvb = $request->filled('precio_venta_blister')
            ? (float) $request->precio_venta_blister
            : ($uXB ? (float)$request->precio_venta * $uXB : null);

        $pvc = $request->filled('precio_venta_caja')
            ? (float) $request->precio_venta_caja
            : ($uXC ? (float)$request->precio_venta * $uXC : null);

        $pcb = $request->filled('precio_compra_blister')
            ? (float) $request->precio_compra_blister
            : ($uXB ? (float)$request->precio_compra * $uXB : null);

        $pcc = $request->filled('precio_compra_caja')
            ? (float) $request->precio_compra_caja
            : ($uXC ? (float)$request->precio_compra * $uXC : null);

        // ===== Crear producto =====
        $producto = Productos::create([
            'codigo'            => $request->codigo,
            'descripcion'       => $request->descripcion,
            'presentacion'      => $request->presentacion,
            'laboratorio'       => $request->laboratorio,
            'lote'              => $request->lote,
            'fecha_vencimiento' => $request->fecha_vencimiento,

            // ratios
            'unidades_por_blister' => $uXB,
            'unidades_por_caja'    => $uXC,

            // stock base (total en unidades) + las cantidades tal como las ingresó el usuario
            'cantidad'         => $totalUnidades,
            'cantidad_blister' => ($request->filled('cantidad_blister') && $uXB) ? $uBli : null,
            'cantidad_caja'    => ($request->filled('cantidad_caja') && $uXC) ? $uCaj : null,

            // mínimo
            'stock_minimo' => $request->stock_minimo,

            // descuentos
            'descuento'         => $request->descuento,
            'descuento_blister' => $request->descuento_blister ?: null,
            'descuento_caja'    => $request->descuento_caja ?: null,

            // precios
            'precio_compra'         => $request->precio_compra,
            'precio_compra_blister' => $pcb,
            'precio_compra_caja'    => $pcc,
            'precio_venta'          => $request->precio_venta,
            'precio_venta_blister'  => $pvb,
            'precio_venta_caja'     => $pvc,

            'foto'         => $rutaFoto,
            'id_proveedor' => $request->id_proveedor,
            'id_clase'     => $request->id_clase ?: null,
            'id_generico'  => $request->id_generico ?: null,
            'estado'       => $request->estado,
        ]);

        // categorías m:n
        $producto->categorias()->sync($request->categorias);

        // alertas coherentes con el stock y vencimiento
        $this->syncProductAlerts($producto);

        return back()->with('success', 'Producto registrado correctamente.');
    }

    public function activar($id)
    {
        $productos = Productos::findOrFail($id);
        $productos->estado = 'Activo';
        $productos->save();

        return redirect()->route('productos.index')->with('success', 'Producto reingresado correctamente.');
    }

    public function desactivar($id)
    {
        $productos = Productos::findOrFail($id);
        $productos->estado = 'Inactivo';
        $productos->save();

        return redirect()->route('productos.index')->with('success', 'Producto desactivado correctamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $producto = Productos::findOrFail($id);

        $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('productos', 'codigo')->ignore($producto->id),
            ],
            'descripcion'  => 'required|string',
            'presentacion' => 'required|string|max:100',
            'laboratorio'  => 'required|string|max:100',
            'lote'              => 'required|string|max:100',
            'fecha_vencimiento' => 'required|date',

            // cantidades ingresadas (pueden combinarse)
            'cantidad'         => 'required|integer|min:0', // unidades
            'cantidad_blister' => 'nullable|integer|min:0',
            'cantidad_caja'    => 'nullable|integer|min:0',

            'stock_minimo' => 'required|integer|min:0',

            'unidades_por_blister'   => 'nullable|integer|min:0',
            'unidades_por_caja'      => 'nullable|integer|min:0',

            'descuento'         => 'required|numeric|min:0|max:100',
            'descuento_blister' => 'nullable|numeric|min:0|max:100',
            'descuento_caja'    => 'nullable|numeric|min:0|max:100',

            'precio_compra'          => 'required|numeric|min:0',
            'precio_compra_blister'  => 'nullable|numeric|min:0',
            'precio_compra_caja'     => 'nullable|numeric|min:0',
            'precio_venta'           => 'required|numeric|min:0',
            'precio_venta_blister'   => 'nullable|numeric|min:0',
            'precio_venta_caja'      => 'nullable|numeric|min:0',

            'id_proveedor' => 'required|exists:proveedores,id',
            'id_clase'     => 'nullable|exists:clases,id',
            'id_generico'  => 'nullable|exists:genericos,id',
            'categorias'   => ['required', 'array', 'min:1'],
            'categorias.*' => ['integer', Rule::exists('categorias', 'id')],
            'estado'       => 'required|in:Activo,Inactivo',

            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Tomamos todos los campos que se actualizan
        $datos = $request->only([
            'codigo',
            'descripcion',
            'presentacion',
            'laboratorio',
            'lote',
            'fecha_vencimiento',
            'unidades_por_blister',
            'unidades_por_caja',
            'cantidad',
            'cantidad_blister',
            'cantidad_caja',
            'stock_minimo',
            'descuento',
            'descuento_blister',
            'descuento_caja',
            'precio_compra',
            'precio_compra_blister',
            'precio_compra_caja',
            'precio_venta',
            'precio_venta_blister',
            'precio_venta_caja',
            'id_proveedor',
            'id_clase',
            'id_generico',
            'estado',
        ]);

        // ===== Normalizar opcionales a null (PERO no toques cantidad_blister/caja aquí) =====
        foreach (
            [
                'unidades_por_blister',
                'unidades_por_caja',
                'descuento_blister',
                'descuento_caja',
                'precio_compra_blister',
                'precio_compra_caja',
                'precio_venta_blister',
                'precio_venta_caja',
                'id_clase',
                'id_generico'
            ] as $campo
        ) {
            if (!isset($datos[$campo]) || $datos[$campo] === '' || (is_numeric($datos[$campo]) && (float)$datos[$campo] == 0)) {
                $datos[$campo] = null;
            }
        }

        // ===== Ratios válidos (0 o vacío -> null) =====
        $uXB = $datos['unidades_por_blister'] ? (int)$datos['unidades_por_blister'] : null;
        $uXC = $datos['unidades_por_caja']    ? (int)$datos['unidades_por_caja']    : null;

        // ===== Cantidades ingresadas =====
        $uUni = (int) $request->input('cantidad', 0);          // unidades
        $uBli = (int) $request->input('cantidad_blister', 0);  // blister
        $uCaj = (int) $request->input('cantidad_caja', 0);     // caja

        // ===== Total en unidades base a partir de lo que el usuario puso =====
        $datos['cantidad'] = $uUni
            + ($uXB ? ($uBli * $uXB) : 0)
            + ($uXC ? ($uCaj * $uXC) : 0);

        // ===== Guardar blister/caja tal como las ingresó el usuario (si hay ratio) =====
        $datos['cantidad_blister'] = ($request->filled('cantidad_blister') && $uXB) ? $uBli : null;
        $datos['cantidad_caja']    = ($request->filled('cantidad_caja') && $uXC) ? $uCaj : null;

        // ===== Derivar precios si quedaron vacíos (opcional pero útil) =====
        if (empty($datos['precio_venta_blister']) && $uXB) {
            $datos['precio_venta_blister'] = (float)$datos['precio_venta'] * $uXB;
        }
        if (empty($datos['precio_venta_caja']) && $uXC) {
            $datos['precio_venta_caja'] = (float)$datos['precio_venta'] * $uXC;
        }
        if (empty($datos['precio_compra_blister']) && $uXB) {
            $datos['precio_compra_blister'] = (float)$datos['precio_compra'] * $uXB;
        }
        if (empty($datos['precio_compra_caja']) && $uXC) {
            $datos['precio_compra_caja'] = (float)$datos['precio_compra'] * $uXC;
        }

        // ===== Foto =====
        if ($request->hasFile('foto')) {
            if ($producto->foto && $producto->foto !== 'imagenes/productos/producto_defecto.jpg') {
                $rutaAnterior = public_path($producto->foto);
                if (file_exists($rutaAnterior)) @unlink($rutaAnterior);
            }
            $nombre = time() . '.' . $request->file('foto')->extension();
            $request->file('foto')->move(public_path('imagenes/productos'), $nombre);
            $datos['foto'] = 'imagenes/productos/' . $nombre;
        }

        // Actualizar y sync
        $producto->update($datos);
        $producto->categorias()->sync($request->categorias);

        // Recalcular/limpiar alertas según nuevo stock y vencimiento
        $this->syncProductAlerts($producto);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function detalle(Request $request)
    {
        $perPage = (int) $request->query('perPage', 10);
        $filter  = $request->query('filter', 'all');  // 'all','bajo','3meses','6meses','9meses','10mas','vencido'
        $q       = trim($request->query('q', ''));

        $hoy = Carbon::today();
        $m3  = $hoy->copy()->addMonthsNoOverflow(3);
        $m6  = $hoy->copy()->addMonthsNoOverflow(6);
        $m9  = $hoy->copy()->addMonthsNoOverflow(9);

        $query = Productos::query()
            // Selecciona solo lo que muestras en la tarjeta (ajusta si necesitas algo extra)
            ->select([
                'id',
                'codigo',
                'descripcion',
                'presentacion',
                'laboratorio',
                'lote',
                'fecha_vencimiento',
                'cantidad',
                'cantidad_blister',
                'cantidad_caja',
                'stock_minimo',
                'unidades_por_blister',
                'unidades_por_caja',
                'precio_venta',
                'precio_venta_blister',
                'precio_venta_caja',
                'precio_compra',
                'precio_compra_blister',
                'precio_compra_caja',
                'descuento',
                'descuento_blister',
                'descuento_caja',
                'estado',
                'foto',
                'id_proveedor',
                'id_clase',
                'id_generico'
            ])
            // Eager loading acotado
            ->with([
                'proveedor:id,nombre',
                'categorias:id,nombre',   // en la tarjeta solo usas nombre
                'clase:id,nombre',
                'generico:id,nombre',
            ]);

        // --- Búsqueda global (descripcion/presentacion/laboratorio/lote/codigo) ---
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('descripcion', 'like', "%{$q}%")
                    ->orWhere('presentacion', 'like', "%{$q}%")
                    ->orWhere('laboratorio', 'like', "%{$q}%")
                    ->orWhere('lote', 'like', "%{$q}%")
                    ->orWhere('codigo', 'like', "%{$q}%");
            });
        }

        // --- Filtros por vencimiento/stock ---
        switch ($filter) {
            case 'vencido':
                $query->whereDate('fecha_vencimiento', '<', $hoy);
                break;
            case '3meses':
                $query->whereBetween('fecha_vencimiento', [$hoy, $m3]);
                break;
            case '6meses':
                $query->whereBetween('fecha_vencimiento', [$m3->copy()->addDay(), $m6]);
                break;
            case '9meses':
                $query->whereBetween('fecha_vencimiento', [$m6->copy()->addDay(), $m9]);
                break;
            case '10mas':
                $query->whereDate('fecha_vencimiento', '>', $m9);
                break;
            case 'bajo':
                $query->whereColumn('cantidad', '<', 'stock_minimo');
                break;
            case 'all':
            default:
                // sin condición extra
                break;
        }

        $productos = $query
            ->orderBy('descripcion')
            ->paginate($perPage)        // o ->simplePaginate($perPage) si tu COUNT(*) sufre
            ->withQueryString();

        // Catálogos para formularios/modales
        $proveedores = Proveedores::select('id', 'nombre')->orderBy('nombre')->get();
        $categorias  = Categorias::select('id', 'nombre')->orderBy('nombre')->get();
        $clases      = Clases::select('id', 'nombre')->orderBy('nombre')->get();
        $genericos   = Genericos::select('id', 'nombre')->orderBy('nombre')->get();

        // Modal de stock bajo (limita para no cargar cientos)
        $productosStockBajo = Productos::whereColumn('cantidad', '<', 'stock_minimo')
            ->limit(50)
            ->get(['id', 'codigo', 'descripcion', 'cantidad']);

        return view('productos.detalleProductos', compact(
            'productos',
            'productosStockBajo',
            'perPage',
            'proveedores',
            'categorias',
            'clases',
            'genericos'
        ));
    }

    public function tablaProductos(Request $request)
    {
        $q            = trim($request->get('q', ''));
        $laboratorio  = $request->get('laboratorio');
        $presentacion = $request->get('presentacion');
        $categoriaId  = $request->get('categoria');

        $productos = Productos::with(['proveedor', 'categorias'])
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($x) use ($q) {
                    $x->where('descripcion', 'like', "%{$q}%")
                        ->orWhere('codigo', 'like', "%{$q}%")
                        ->orWhere('lote', 'like', "%{$q}%")
                        ->orWhere('laboratorio', 'like', "%{$q}%")
                        ->orWhere('presentacion', 'like', "%{$q}%");
                });
            })
            ->when($laboratorio,  fn($qb) => $qb->where('laboratorio',  $laboratorio))
            ->when($presentacion, fn($qb) => $qb->where('presentacion', $presentacion))
            ->when($categoriaId,  fn($qb) => $qb->whereHas('categorias', fn($q2) => $q2->where('categorias.id', $categoriaId)))
            ->orderBy('fecha_vencimiento', 'asc')
            ->paginate(20); // <- IMPORTANTE

        // Catálogos
        $categorias     = Categorias::orderBy('nombre')->get(['id', 'nombre']);
        $laboratorios   = Productos::whereNotNull('laboratorio')->select('laboratorio')->distinct()->orderBy('laboratorio')->pluck('laboratorio');
        $presentaciones = Productos::whereNotNull('presentacion')->select('presentacion')->distinct()->orderBy('presentacion')->pluck('presentacion');

        // No hace falta appends aquí, ya lo hace la vista con withQueryString()
        return view('productos.tablaProductos', compact(
            'productos',
            'categorias',
            'laboratorios',
            'presentaciones',
            'q',
            'laboratorio',
            'presentacion',
            'categoriaId'
        ));
    }

    public function exportar($formato)
    {
        // Carga relaciones correctas (m:n categorias) y datos útiles
        $productos = Productos::with(['proveedor', 'categorias', 'clase', 'generico'])->get();

        switch ($formato) {
            case 'pdf':
                // Asegúrate que en la vista PDF uses $p->categorias->pluck('nombre')->implode(', ')
                $pdf = Pdf::loadView('exportaciones.productos_pdf', compact('productos'));
                return $pdf->download('productos.pdf');

            case 'xlsx':
                // Si quieres incluir los nuevos campos en Excel, actualiza ProductosExport
                return Excel::download(new ProductosExport, 'productos.xlsx');

            case 'csv':
                // Igual que xlsx: ProductosExport debe mapear los nuevos campos si los quieres ahí
                return Excel::download(new ProductosExport, 'productos.csv');

            case 'txt':
                // Cabeceras (tab-delimited)
                $headers = [
                    'Codigo',
                    'Descripcion',
                    'Presentacion',
                    'Laboratorio',
                    'Lote',
                    'FechaVenc',
                    'Cant_Unid',
                    'Cant_Blister',
                    'Cant_Caja',
                    'Min_Unid',
                    'Desc_Unid_%',
                    'Desc_Blister_%',
                    'Desc_Caja_%',
                    'PCompra_Unid',
                    'PCompra_Blister',
                    'PCompra_Caja',
                    'PVenta_Unid',
                    'PVenta_Blister',
                    'PVenta_Caja',
                    'Proveedor',
                    'Categorias',
                    'Clase',
                    'Generico',
                    'Estado'
                ];

                $lineas = [implode("\t", $headers)];

                foreach ($productos as $p) {
                    $lineas[] = implode("\t", [
                        $p->codigo ?? '',
                        $p->descripcion ?? '',
                        $p->presentacion ?? '',
                        $p->laboratorio ?? '',
                        $p->lote ?? '',
                        $p->fecha_vencimiento ? \Carbon\Carbon::parse($p->fecha_vencimiento)->format('Y-m-d') : '',

                        $p->cantidad ?? '',
                        $p->cantidad_blister ?? '',
                        $p->cantidad_caja ?? '',

                        $p->stock_minimo ?? '',

                        $p->descuento ?? '',
                        $p->descuento_blister ?? '',
                        $p->descuento_caja ?? '',

                        $p->precio_compra ?? '',
                        $p->precio_compra_blister ?? '',
                        $p->precio_compra_caja ?? '',

                        $p->precio_venta ?? '',
                        $p->precio_venta_blister ?? '',
                        $p->precio_venta_caja ?? '',

                        optional($p->proveedor)->nombre ?? '',
                        $p->categorias?->pluck('nombre')->implode(', ') ?? '',
                        optional($p->clase)->nombre ?? '',
                        optional($p->generico)->nombre ?? '',
                        $p->estado ?? '',
                    ]);
                }

                $contenido = implode("\n", $lineas);

                return response($contenido)
                    ->header('Content-Type', 'text/plain; charset=UTF-8')
                    ->header('Content-Disposition', 'attachment; filename="productos.txt"');

            default:
                return back()->with('error', 'Formato no válido.');
        }
    }

    /**
     * Genera y limpia alertas de un producto (sin scheduler).
     * - Vencido: 1 alerta persistente (una sola referencia por producto).
     * - Por vencer (≤30 días): 1 alerta por día.
     * - Stock bajo (cantidad <= stock_minimo): 1 alerta por día.
     */
    private function syncProductAlerts(Productos $p): void
    {
        $hoy = now()->toDateString();

        $fv = $p->fecha_vencimiento ? ($p->fecha_vencimiento instanceof Carbon ? $p->fecha_vencimiento : Carbon::parse($p->fecha_vencimiento)) : null;

        // ===== 1) Producto VENCIDO =====
        $refVencido = "prod_vencido_{$p->id}";
        if ($fv && $fv->isPast()) {
            Alertas::firstOrCreate(
                ['referencia' => $refVencido],
                [
                    'titulo'      => 'Producto Vencido',
                    'mensaje'     => "¡Atención! El producto {$p->descripcion} está vencido desde {$fv->format('d/m/Y')}.",
                    'id_producto' => $p->id,
                    'leido'       => false,
                ]
            );
        } else {
            Alertas::where('referencia', $refVencido)->delete();
        }

        // ===== 2) Producto POR VENCER (≤30 días) =====
        $refPorVencer = "prod_porvencer_{$p->id}_{$hoy}";
        if ($fv && !$fv->isPast()) {
            $dias = now()->startOfDay()->diffInDays($fv->startOfDay(), false);
            if ($dias >= 0 && $dias <= 30) {
                Alertas::firstOrCreate(
                    ['referencia' => $refPorVencer],
                    [
                        'titulo'      => 'Producto por vencer',
                        'mensaje'     => "El producto {$p->descripcion} vence el {$fv->format('d/m/Y')} (en {$dias} día(s)).",
                        'id_producto' => $p->id,
                        'leido'       => false,
                    ]
                );
            }
        }

        // Limpieza suave de "por vencer" antiguas (opcional)
        Alertas::where('id_producto', $p->id)
            ->where('titulo', 'Producto por vencer')
            ->whereDate('created_at', '<', now()->subDays(40))
            ->delete();

        // ===== 3) STOCK BAJO (cantidad <= stock_minimo) =====
        $refStockBajo = "stock_bajo_{$p->id}_{$hoy}";
        $tieneMinimos = $p->cantidad !== null && $p->stock_minimo !== null;
        if ($tieneMinimos && $p->cantidad <= $p->stock_minimo) {
            Alertas::firstOrCreate(
                ['referencia' => $refStockBajo],
                [
                    'titulo'      => 'Stock bajo',
                    'mensaje'     => "Quedan {$p->cantidad} unidades de {$p->descripcion} (mínimo: {$p->stock_minimo}).",
                    'id_producto' => $p->id,
                    'leido'       => false,
                ]
            );
        } else {
            // Si recuperaste stock, borra alertas vigentes de stock bajo de este producto
            Alertas::where('id_producto', $p->id)->where('titulo', 'Stock bajo')->delete();
        }
    }
}
