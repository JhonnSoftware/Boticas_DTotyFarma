<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alertas;
use Carbon\Carbon;
use App\Models\Productos;
use App\Models\DetalleVentas;
use App\Models\Cajas;

class AlertaController extends Controller
{
    public function index()
    {
        $alertas = Alertas::orderBy('created_at', 'desc')->get();
        return view('alertas.index', compact('alertas'));
    }

    public function marcarComoLeida($id)
    {
        $alerta = Alertas::findOrFail($id);
        $alerta->leido = true;
        $alerta->save();

        return redirect()->back()->with('success', 'Alerta marcada como leída.');
    }

    public function generarAlertasManual()
    {
        $this->generarAlertas(); // llama a tu lógica ya implementada

        return redirect()->back()->with('success', '🔔 Alertas generadas manualmente.');
    }


    public function generarAlertas()
    {
        // 1. Productos vencidos
        $productosVencidos = Productos::whereDate('fecha_vencimiento', '<', now())->get();
        foreach ($productosVencidos as $producto) {
            Alertas::firstOrCreate([
                'titulo' => 'Producto Vencido',
                'mensaje' => "¡Atención! El producto {$producto->descripcion} está vencido desde el {$producto->fecha_vencimiento->format('d/m/Y')}.",
            ]);
        }

        // 2. Productos próximos a vencer (en 30 días)
        $productosPorVencer = Productos::whereBetween('fecha_vencimiento', [now(), now()->addDays(30)])->get();
        foreach ($productosPorVencer as $producto) {
            Alertas::firstOrCreate([
                'titulo' => 'Producto por vencer',
                'mensaje' => "El producto {$producto->descripcion} vence en menos de 30 días.",
            ]);
        }

        // 3. Stock bajo
        $stockBajo = Productos::whereColumn('cantidad', '<=', 'stock_minimo')->get();
        foreach ($stockBajo as $producto) {
            Alertas::firstOrCreate([
                'titulo' => 'Stock bajo',
                'mensaje' => "Quedan menos de {$producto->cantidad} unidades de {$producto->descripcion}.",
            ]);
        }

        // 4. Productos sin movimiento en 60 días
        $productosSinMovimiento = Productos::whereDoesntHave('detalleVentas', function ($q) {
            $q->where('created_at', '>=', now()->subDays(60));
        })->get();

        foreach ($productosSinMovimiento as $producto) {
            Alertas::firstOrCreate([
                'titulo' => 'Producto sin movimiento',
                'mensaje' => "El producto {$producto->descripcion} no tiene ventas en los últimos 60 días.",
            ]);
        }

        // 5. Caja sin apertura hoy
        $aperturaHoy = Cajas::whereDate('fecha_apertura', today())->exists();
        if (!$aperturaHoy) {
            Alertas::firstOrCreate([
                'titulo' => 'Caja no aperturada',
                'mensaje' => "No se ha registrado la apertura de caja del día.",
            ]);
        }

        // 6. Caja sin cierre (estado abierta)
        $cajaSinCerrar = Cajas::where('estado', 'abierta')->whereNull('fecha_cierre')->exists();
        if ($cajaSinCerrar) {
            Alertas::firstOrCreate([
                'titulo' => 'Caja sin cerrar',
                'mensaje' => "La caja del turno anterior no ha sido cerrada.",
            ]);
        }

        return redirect()->back()->with('success', 'Alertas generadas correctamente.');
    }
}
