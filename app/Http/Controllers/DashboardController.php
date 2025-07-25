<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clientes;
use App\Models\Categorias;
use App\Models\Ventas;
use App\Models\Compras;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleVentas;
use App\Models\Productos;
use App\Models\DevolucionesVentas;
use App\Models\DevolucionesCompras;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $ventasPorMes = Ventas::select(
            DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes"),
            DB::raw("SUM(total) as total")
        )
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->take(6)
            ->get();

        $labels = $ventasPorMes->pluck('mes');   // ['2025-02', '2025-03', ...]
        $data = $ventasPorMes->pluck('total');

        // Obtener productos más vendidos
        $productosMasVendidos = DetalleVentas::select('id_producto', DB::raw('SUM(cantidad) as total_vendidos'))
            ->groupBy('id_producto')
            ->orderByDesc('total_vendidos')
            ->take(5) // o 10 si deseas
            ->get();

        $productosNombres = $productosMasVendidos->map(function ($detalle) {
            return $detalle->producto->descripcion ?? 'Producto Eliminado';
        });

        $productosCantidades = $productosMasVendidos->pluck('total_vendidos');


        // Contar productos con stock bajo o crítico
        $stockBajo = Productos::whereColumn('cantidad', '<', 'stock_minimo')->count();

        // Contar productos con stock normal
        $stockNormal = Productos::whereColumn('cantidad', '>=', 'stock_minimo')->count();

        // (Opcional) total productos si lo necesitas en otra parte
        $totalProductos = $stockBajo + $stockNormal;


        // --- Ingresos vs Egresos (barras agrupadas) ---
        $meses = collect([]);
        $ingresos = collect([]);
        $egresos = collect([]);

        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i)->format('Y-m');

            $ventasMes = Ventas::where(DB::raw("DATE_FORMAT(fecha, '%Y-%m')"), $mes)->sum('total');
            $comprasMes = Compras::where(DB::raw("DATE_FORMAT(fecha, '%Y-%m')"), $mes)->sum('total');

            $meses->push(Carbon::createFromFormat('Y-m', $mes)->translatedFormat('F Y'));
            $ingresos->push($ventasMes);
            $egresos->push($comprasMes);
        }

        // Conteo total de devoluciones por tipo
        $devolucionesVentas = DevolucionesVentas::count();
        $devolucionesCompras = DevolucionesCompras::count();
        $cantidadVentas = Ventas::count();     // total de registros de ventas
        $cantidadCompras = Compras::count();   // total de registros de compras

        // Top 5 productos con menos stock (activos o todos, tú decides)
        $productosStockBajoTop = Productos::orderBy('cantidad', 'asc')
            ->take(5)
            ->get();

        // Datos para tarjetas resumen
        $totalUsuarios    = User::count();
        $totalClientes    = Clientes::count();
        $totalProductos   = Productos::count();
        $totalCategorias  = Categorias::count();

        $ventasDelMes = Ventas::whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->where('estado', 'Activo')
            ->sum('total');

        $comprasDelMes = Compras::whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->where('estado', 'Activo')
            ->sum('total');


        $ventasAnuladas   = Ventas::where('estado', 'Anulado')->sum('total');
        $comprasAnuladas  = Compras::where('estado', 'Anulado')->sum('total');

        return view('dashboard', compact(
            'labels',
            'data',
            'productosNombres',
            'productosCantidades',
            'stockBajo',
            'stockNormal',
            'meses',
            'ingresos',
            'egresos',
            'devolucionesVentas',
            'devolucionesCompras',
            'cantidadVentas',
            'cantidadCompras',
            'productosMasVendidos',
            'productosStockBajoTop',
            'totalUsuarios',
            'totalClientes',
            'totalProductos',
            'totalCategorias',
            'ventasDelMes',
            'comprasDelMes',
            'ventasAnuladas',
            'comprasAnuladas'
        ));
    }

    public function datosEmpresa()
    {
        return view('datos_empresa');
    }
}
