<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alertas;
use App\Models\Productos;
use App\Models\DetalleVentas;
use App\Models\Cajas;
use Carbon\Carbon;

class GenerarAlertas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generar-alertas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Productos vencidos
        $productosVencidos = Productos::whereDate('fecha_vencimiento', '<', now())->get();
        foreach ($productosVencidos as $producto) {
            Alertas::firstOrCreate([
                'titulo' => 'Producto Vencido',
                'mensaje' => "¡Atención! El producto {$producto->descripcion} está vencido desde el {$producto->fecha_vencimiento->format('d/m/Y')}.",
            ]);
        }

        // 2. Productos por vencer
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

        // 5. Caja no aperturada
        $aperturaHoy = Cajas::whereDate('fecha_apertura', today())->exists();
        if (!$aperturaHoy) {
            Alertas::firstOrCreate([
                'titulo' => 'Caja no aperturada',
                'mensaje' => "No se ha registrado la apertura de caja del día.",
            ]);
        }

        // 6. Caja sin cerrar
        $cajaSinCerrar = Cajas::where('estado', 'abierta')->whereNull('fecha_cierre')->exists();
        if ($cajaSinCerrar) {
            Alertas::firstOrCreate([
                'titulo' => 'Caja sin cerrar',
                'mensaje' => "La caja del turno anterior no ha sido cerrada.",
            ]);
        }

        $this->info('✔️ Alertas generadas automáticamente.');
    }
}
