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
        // ===== Ventas por mes (6 últimos) =====
        $ventasPorMes = Ventas::select(
                DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes"),
                DB::raw("SUM(total) as total")
            )
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->take(6)
            ->get();

        $labels = $ventasPorMes->pluck('mes');
        $data   = $ventasPorMes->pluck('total');

        // ===== Productos más vendidos =====
        $productosMasVendidos = DetalleVentas::select('id_producto', DB::raw('SUM(cantidad) as total_vendidos'))
            ->groupBy('id_producto')
            ->orderByDesc('total_vendidos')
            ->take(5)
            ->get();

        $productosNombres    = $productosMasVendidos->map(fn($d) => $d->producto->descripcion ?? 'Producto Eliminado');
        $productosCantidades = $productosMasVendidos->pluck('total_vendidos');

        // ===== Stock bajo / normal (solo unidades) =====
        $stockBajo   = Productos::whereColumn('cantidad', '<', 'stock_minimo')->count();
        $stockNormal = Productos::whereColumn('cantidad', '>=', 'stock_minimo')->count();

        // ===== Ingresos vs Egresos (6 últimos meses) =====
        $meses    = collect([]);
        $ingresos = collect([]);
        $egresos  = collect([]);

        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i)->format('Y-m');
            $ventasMes  = Ventas::where(DB::raw("DATE_FORMAT(fecha, '%Y-%m')"), $mes)->sum('total');
            $comprasMes = Compras::where(DB::raw("DATE_FORMAT(fecha, '%Y-%m')"), $mes)->sum('total');

            $meses->push(Carbon::createFromFormat('Y-m', $mes)->translatedFormat('F Y'));
            $ingresos->push($ventasMes);
            $egresos->push($comprasMes);
        }

        // ===== Devoluciones y conteos =====
        $devolucionesVentas  = DevolucionesVentas::count();
        $devolucionesCompras = DevolucionesCompras::count();
        $cantidadVentas      = Ventas::count();
        $cantidadCompras     = Compras::count();

        // ===== Top 5 productos con menor stock (solo unidades) =====
        // ratio_min = cantidad / stock_minimo (si hay mínimo; si no, 9999 para ir al final)
        $productosStockBajoTop = Productos::select(
                'id',
                'descripcion',
                'foto',
                'cantidad',
                'stock_minimo',
                'fecha_vencimiento'
            )
            ->addSelect(DB::raw("
                COALESCE(
                    NULLIF(cantidad, 0) / NULLIF(stock_minimo, 0),
                    9999
                ) as ratio_min
            "))
            ->orderBy('ratio_min', 'asc') // más críticos primero
            ->orderBy('cantidad', 'asc')
            ->take(5)
            ->get();

        // ===== Datos para tarjetas resumen =====
        $totalUsuarios   = User::count();
        $totalClientes   = Clientes::count();
        $totalProductos  = Productos::count();
        $totalCategorias = Categorias::count();

        $ventasDelMes = Ventas::whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->where('estado', 'Activo')
            ->sum('total');

        $comprasDelMes = Compras::whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->where('estado', 'Activo')
            ->sum('total');

        $ventasAnuladas  = Ventas::where('estado', 'Anulado')->sum('total');
        $comprasAnuladas = Compras::where('estado', 'Anulado')->sum('total');

        // ===== Métricas extra (sin mínimos por blíster/caja) =====

        // Conteo de stock bajo por unidades (único mínimo existente)
        $stockBajoU = Productos::whereNotNull('stock_minimo')
            ->whereColumn('cantidad', '<=', 'stock_minimo')
            ->count();

        // Sumas de stock por presentación (sirven aunque ya no haya mínimos por B/C)
        $stockSumU = (int) Productos::sum('cantidad');
        $stockSumB = (int) Productos::sum('cantidad_blister');
        $stockSumC = (int) Productos::sum('cantidad_caja');

        // Valor de inventario con precios de compra por presentación
        $valorInventario = (float) Productos::selectRaw("
            SUM(
                COALESCE(cantidad, 0) * COALESCE(precio_compra, 0) +
                COALESCE(cantidad_blister, 0) * COALESCE(precio_compra_blister, 0) +
                COALESCE(cantidad_caja, 0) * COALESCE(precio_compra_caja, 0)
            ) as valor
        ")->value('valor');

        // Próximos a vencer (≤ 30 días)
        $proximosAVencer = Productos::whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '>=', now()->startOfDay())
            ->whereDate('fecha_vencimiento', '<=', now()->addDays(30)->endOfDay())
            ->count();

        return view('dashboard', compact(
            // existentes
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
            'comprasAnuladas',

            // nuevos (sin mínimos B/C)
            'stockBajoU',
            'stockSumU',
            'stockSumB',
            'stockSumC',
            'valorInventario',
            'proximosAVencer'
        ));
    }

    public function datosEmpresa()
    {
        return view('datos_empresa');
    }
}
