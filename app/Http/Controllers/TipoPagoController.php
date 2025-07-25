<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoPagos;
use Illuminate\Validation\Rule;

class TipoPagoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoPagos::query();

        if ($request->has('buscar') && !empty($request->buscar)) {
            $busqueda = $request->buscar;
            $query->where('nombre', 'LIKE', "%$busqueda%");
        }

        $tipopagos = $query->get();

        return view('tipopagos.index', compact('tipopagos'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $tipopagos = TipoPagos::where('nombre', 'LIKE', "%$buscar%")->get();

        return view('tipopagos.partials.tabla', compact('tipopagos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:tipopago,nombre',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        TipoPagos::create([
            'nombre' => $request->nombre,
            'estado' => $request->estado,
        ]);

        return redirect()->route('tipopagos.index')->with('success', 'Tipo pago registrado exitosamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100' , Rule::unique('tipopago', 'nombre')->ignore($id)],
        ]);

        $cliente = TipoPagos::findOrFail($id);

        $cliente->update([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('tipopagos.index')->with('success', 'Tipo pago actualizado correctamente.');
    }
    
    public function activar($id)
    {
        $cliente = TipoPagos::findOrFail($id);
        $cliente->estado = 'Activo';
        $cliente->save();

        return redirect()->route('tipopagos.index')->with('success', 'Tipo pago reingresado correctamente.');
    }

    public function desactivar($id)
    {
        $cliente = TipoPagos::findOrFail($id);
        $cliente->estado = 'Inactivo';
        $cliente->save();

        return redirect()->route('tipopagos.index')->with('success', 'Tipo pago desactivado correctamente.');
    }

}
