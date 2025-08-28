<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cajas;
use App\Models\Alertas;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function aperturaForm()
    {
        $this->checkDailyCajaAlerts();

        $caja = Cajas::where('usuario_id', auth()->id())
            ->where('estado', 'abierta')
            ->first();

        return view('cajas.apertura', compact('caja'));
    }

    // Guarda los datos de la apertura
    public function aperturaStore(Request $request)
    {
        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
        ]);

        // Verificar si ya hay una caja abierta por el mismo usuario
        $cajaAbierta = Cajas::where('estado', 'abierta')
            ->where('usuario_id', Auth::id())
            ->first();

        if ($cajaAbierta) {
            return redirect()->back()->with('error', 'Ya tienes una caja abierta.');
        }

        // Crear nueva caja
        Cajas::create([
            'monto_apertura' => $request->monto_apertura,
            'fecha_apertura' => now(),
            'usuario_id'     => Auth::id(),
            'estado'         => 'abierta',
        ]);

        $this->syncCajaAlerts(); // <-- agrega esto

        return redirect()->route('caja.apertura.form')->with('success', 'Caja abierta correctamente.');
    }

    public function cierreStore(Request $request)
    {
        $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
        ]);

        $caja = Cajas::where('usuario_id', auth()->id())
            ->where('estado', 'abierta')
            ->first();

        if (!$caja) {
            return redirect()->route('caja.apertura.form')->with('error', 'No hay caja abierta.');
        }

        $caja->update([
            'monto_cierre' => $request->monto_cierre,
            'fecha_cierre' => now(),
            'estado'       => 'cerrada',
        ]);

        $this->syncCajaAlerts(); // <-- agrega esto

        return redirect()->route('caja.apertura.form')->with('success', 'Caja cerrada correctamente.');
    }

    public function listarCajas()
    {
        $this->checkDailyCajaAlerts();

        $cajas = Cajas::where('usuario_id', auth()->id())
            ->orderBy('fecha_apertura', 'desc')
            ->get();

        return view('cajas.listado', compact('cajas'));
    }

    public function listado(Request $request)
    {
        $this->checkDailyCajaAlerts();

        $query = Cajas::where('usuario_id', auth()->id());

        if ($request->filled('fecha')) {
            $fecha = $request->fecha;
            $query->whereBetween('fecha_apertura', [
                $fecha . ' 00:00:00',
                $fecha . ' 23:59:59',
            ]);
        }

        $cajas = $query->orderBy('fecha_apertura', 'desc')->get();

        // Verifica si la petición es AJAX y devuelve solo el fragmento
        if ($request->ajax()) {
            return view('cajas.partials.tabla', compact('cajas'));
        }

        // Si no es AJAX, devuelve la vista principal
        return view('cajas.listado', compact('cajas'));
    }

    public function buscar(Request $request)
    {

        $this->checkDailyCajaAlerts();

        $query = Cajas::where('usuario_id', auth()->id());

        if ($request->filled('fecha')) {
            $fecha = $request->fecha;
            $query->whereBetween('fecha_apertura', [
                $fecha . ' 00:00:00',
                $fecha . ' 23:59:59',
            ]);
        }

        $cajas = $query->orderBy('fecha_apertura', 'desc')->get();

        return view('cajas.partials.tabla', compact('cajas'));
    }

    /**
     * Genera / limpia alertas de caja (globales) sin scheduler.
     * - Caja no aperturada (hoy): 1 alerta/día si NADIE abrió caja hoy.
     * - Caja sin cerrar: 1 alerta/día si existe alguna caja abierta sin cierre.
     */
    private function syncCajaAlerts(): void
    {
        $hoy = now()->toDateString();

        // === A) Caja no aperturada hoy ===
        $aperturaHoy = Cajas::whereDate('fecha_apertura', $hoy)->exists();

        if (!$aperturaHoy) {
            Alertas::firstOrCreate(
                ['referencia' => "caja_no_aperturada_{$hoy}"],
                [
                    'titulo'  => 'Caja no aperturada',
                    'mensaje' => 'No se ha registrado la apertura de caja del día.',
                    'leido'   => false,
                    // 'id_producto' => null // (implícito)
                ]
            );
        } else {
            // Si hoy ya hubo una apertura, elimina la alerta (si existía)
            Alertas::where('referencia', "caja_no_aperturada_{$hoy}")->delete();
        }

        // === B) Caja sin cerrar DE DÍAS ANTERIORES ===
        $hayAbiertasPrevias = Cajas::where('estado', 'abierta')
            ->whereNull('fecha_cierre')
            ->whereDate('fecha_apertura', '<', $hoy)
            ->exists();

        if ($hayAbiertasPrevias) {
            Alertas::firstOrCreate(
                ['referencia' => "caja_sin_cerrar_{$hoy}"],
                [
                    'titulo'  => 'Caja sin cerrar',
                    'mensaje' => 'La caja del turno anterior no ha sido cerrada.',
                    'leido'   => false,
                ]
            );
        } else {
            Alertas::where('referencia', "caja_sin_cerrar_{$hoy}")->delete();

            // (Opcional) Limpia histórico viejo
            Alertas::where('titulo', 'Caja sin cerrar')
                ->whereDate('created_at', '<', now()->subDays(7))
                ->delete();
        }
    }

    private function checkDailyCajaAlerts(): void
    {
        $key = 'daily_caja_alerts_' . now()->toDateString();
        Cache::remember($key, now()->endOfDay()->addSecond(), function () {
            $this->syncCajaAlerts();
            return true;
        });
    }
}
