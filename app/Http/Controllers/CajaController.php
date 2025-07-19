<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cajas;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function aperturaForm()
    {
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
            'usuario_id' => Auth::id(),
            'estado' => 'abierta',
        ]);

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
            'estado' => 'cerrada',
        ]);

        return redirect()->route('caja.apertura.form')->with('success', 'Caja cerrada correctamente.');
    }
    public function listarCajas()
    {
        $cajas = Cajas::where('usuario_id', auth()->id())
            ->orderBy('fecha_apertura', 'desc')
            ->get();

        return view('cajas.listado', compact('cajas'));
    }

    public function listado(Request $request)
    {
        $query = Cajas::where('usuario_id', auth()->id());

        if ($request->filled('fecha')) {
            $fecha = $request->fecha;
            $query->whereBetween('fecha_apertura', [
                $fecha . ' 00:00:00',
                $fecha . ' 23:59:59',
            ]);
        }

        $cajas = $query->orderBy('fecha_apertura', 'desc')->get();

        return view('admin.cajas.listado', compact('cajas'));
    }
}
