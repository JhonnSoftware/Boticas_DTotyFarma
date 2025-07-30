<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Categorias;
use Illuminate\Support\Facades\Storage;
use App\Exports\ProductosExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Productos::query();

        if ($request->has('buscar') && !empty($request->buscar)) {
            $busqueda = $request->buscar;
            $query->where('descripcion', 'LIKE', "%$busqueda%")
                ->orWhere('presentacion', 'LIKE', "%$busqueda%")
                ->orWhere('laboratorio', 'LIKE', "%$busqueda%")
                ->orWhere('lote', 'LIKE', "%$busqueda%")
                ->orWhere('fecha_vencimiento', 'LIKE', "%$busqueda%")
                ->orWhere('precio_compra', 'LIKE', "%$busqueda%")
                ->orWhere('precio_venta', 'LIKE', "%$busqueda%");
        }

        $productos = $query->get();
        $proveedores = Proveedores::all();
        $categorias = Categorias::all();
        $productosStockBajo = Productos::whereColumn('cantidad', '<', 'stock_minimo')->get();

        // Obtener último producto registrado
        $ultimoProducto = Productos::latest('id')->first();
        $nextId = $ultimoProducto ? $ultimoProducto->id + 1 : 1;
        // Formatear el nuevo código
        $nuevoCodigo = 'P000-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('productos.index', compact('productos', 'proveedores', 'categorias', 'productosStockBajo', 'nuevoCodigo'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $productos = Productos::where('descripcion', 'LIKE', "%$buscar%")
            ->orWhere('presentacion', 'LIKE', "%$buscar%")
            ->orWhere('laboratorio', 'LIKE', "%$buscar%")
            ->get();

        $proveedores = Proveedores::all();
        $categorias = Categorias::all();

        return view('productos.partials.tabla', compact('productos', 'proveedores', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'descripcion' => 'required|string',
            'presentacion' => 'required|string|max:100',
            'laboratorio' => 'required|string|max:100',
            'lote' => 'required|integer',
            'cantidad' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'descuento' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date|after_or_equal:today',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'id_proveedor' => 'required|exists:proveedores,id',
            'id_categoria' => 'required|exists:categorias,id',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        // Procesar la imagen
        if ($request->hasFile('foto')) {
            $fotoNombre = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('imagenes'), $fotoNombre);
            $rutaFoto = 'imagenes/' . $fotoNombre;
        } else {
            $rutaFoto = 'imagenes/producto_defecto.jpg'; // Ruta relativa a la imagen por defecto
        }

        Productos::create([
            'codigo' => $request->codigo,
            'descripcion' => $request->descripcion,
            'presentacion' => $request->presentacion,
            'laboratorio' => $request->laboratorio,
            'lote' => $request->lote,
            'cantidad' => $request->cantidad,
            'stock_minimo' => $request->stock_minimo,
            'descuento' => $request->descuento,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'precio_compra' => $request->precio_compra,
            'precio_venta' => $request->precio_venta,
            'foto' => $rutaFoto,
            'id_proveedor' => $request->id_proveedor,
            'id_categoria' => $request->id_categoria,
            'estado' => $request->estado,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto registrado correctamente.');
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
        $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo,' . $id,
            'descripcion' => 'required|string',
            'presentacion' => 'required|string|max:100',
            'laboratorio' => 'required|string|max:100',
            'lote' => 'required|integer',
            'cantidad' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'descuento' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'id_proveedor' => 'required|exists:proveedores,id',
            'id_categoria' => 'required|exists:categorias,id',
            'estado' => 'required|in:Activo,Inactivo',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validación para imagen opcional
        ]);

        $producto = Productos::findOrFail($id);

        $datos = $request->only([
            'codigo',
            'descripcion',
            'presentacion',
            'laboratorio',
            'lote',
            'cantidad',
            'stock_minimo',
            'descuento',
            'fecha_vencimiento',
            'precio_compra',
            'precio_venta',
            'id_proveedor',
            'id_categoria',
            'estado'
        ]);

        if ($request->hasFile('foto')) {
            // Elimina la anterior si no es la imagen por defecto
            if ($producto->foto !== 'imagenes/producto_defecto.jpg' && file_exists(public_path($producto->foto))) {
                unlink(public_path($producto->foto));
            }

            // Guarda la nueva foto en la carpeta 'imagenes'
            $fotoNombre = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('imagenes'), $fotoNombre);

            // Guarda la ruta relativa
            $datos['foto'] = 'imagenes/' . $fotoNombre;
        }

        $producto->update($datos);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function detalle()
    {
        $productos = Productos::with('categoria', 'proveedor')->get(); // ← Eager loading
        return view('productos.detalleProductos', compact('productos'));
    }

    public function exportar($formato)
    {
        $productos = Productos::with(['proveedor', 'categoria'])->get();

        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('exportaciones.productos_pdf', compact('productos'));
            return $pdf->download('productos.pdf');
        }

        if ($formato === 'xlsx') {
            return Excel::download(new ProductosExport, 'productos.xlsx');
        }

        if ($formato === 'csv') {
            return Excel::download(new ProductosExport, 'productos.csv');
        }

        if ($formato === 'txt') {
            $contenido = '';
            foreach ($productos as $p) {
                $contenido .= implode("\t", [
                    $p->codigo,
                    $p->descripcion,
                    $p->presentacion,
                    $p->laboratorio,
                    $p->lote,
                    $p->cantidad,
                    $p->stock_minimo,
                    $p->descuento,
                    $p->fecha_vencimiento,
                    $p->precio_compra,
                    $p->precio_venta,
                    optional($p->proveedor)->nombre,
                    optional($p->categoria)->nombre,
                    $p->estado
                ]) . "\n";
            }
            return response($contenido)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="productos.txt"');
        }

        return back()->with('error', 'Formato no válido.');
    }
}
