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

        // ===== Stock bajo / normal (solo unidades para mantener compatibilidad con tu UI actual) =====
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

        // ===== NUEVO: Top 5 productos con menor stock (considerando U/B/C) =====
        // ratio_min: peor ratio contra mínimo entre U/B/C. Si no hay mínimo definido, lo ignora (empuja al final).
        $productosStockBajoTop = Productos::select(
            'id',
            'descripcion',
            'foto',
            'cantidad',
            'cantidad_blister',
            'cantidad_caja',
            'stock_minimo',
            'stock_minimo_blister',
            'stock_minimo_caja',
            'fecha_vencimiento'
        )
            ->addSelect(DB::raw("
            NULLIF(cantidad / NULLIF(stock_minimo, 0), NULL)                 as r_u,
            NULLIF(cantidad_blister / NULLIF(stock_minimo_blister, 0), NULL) as r_b,
            NULLIF(cantidad_caja / NULLIF(stock_minimo_caja, 0), NULL)       as r_c,
            COALESCE(LEAST(
                NULLIF(cantidad / NULLIF(stock_minimo, 0), NULL),
                NULLIF(cantidad_blister / NULLIF(stock_minimo_blister, 0), NULL),
                NULLIF(cantidad_caja / NULLIF(stock_minimo_caja, 0), NULL)
            ), 9999) as ratio_min
        "))
            ->orderBy('ratio_min', 'asc')   // más críticos primero
            ->orderBy('cantidad', 'asc')    // desempate
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

        // ===== NUEVO: métricas extra con U/B/C =====

        // Conteos de stock bajo por presentación (si existe mínimo)
        $stockBajoU = Productos::whereNotNull('stock_minimo')
            ->whereColumn('cantidad', '<=', 'stock_minimo')->count();
        $stockBajoB = Productos::whereNotNull('stock_minimo_blister')
            ->whereColumn('cantidad_blister', '<=', 'stock_minimo_blister')->count();
        $stockBajoC = Productos::whereNotNull('stock_minimo_caja')
            ->whereColumn('cantidad_caja', '<=', 'stock_minimo_caja')->count();

        // Sumas de stock por presentación
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
            // existentes (no los tocamos)
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

            // nuevos (para potenciar el dashboard)
            'stockBajoU',
            'stockBajoB',
            'stockBajoC',
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
