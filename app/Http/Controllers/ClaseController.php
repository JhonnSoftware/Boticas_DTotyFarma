<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clases;
use Illuminate\Validation\Rule;

class ClaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Clases::query();

        if ($request->has('buscar') && !empty($request->buscar)) {
            $busqueda = $request->buscar;
            $query->where('nombre', 'LIKE', "%$busqueda%");
        }

        $clases = $query->get();

        return view('clases.index', compact('clases'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $clases = Clases::where('nombre', 'LIKE', "%$buscar%")->get();

        return view('clases.partials.tabla', compact('clases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:clases,nombre',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Clases::create([
            'nombre' => $request->nombre,
            'estado' => $request->estado,
        ]);

        return redirect()->route('clases.index')->with('success', 'Clase registrada exitosamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
          
            'nombre' => ['required', 'string', 'max:100' , Rule::unique('clases', 'nombre')->ignore($id)],
        ]);

        $clases = Clases::findOrFail($id);

        $clases->update([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('clases.index')->with('success', 'Clase actualizada correctamente.');
    }

    public function activar($id)
    {
        $clases = Clases::findOrFail($id);
        $clases->estado = 'Activo';
        $clases->save();

        return redirect()->route('clases.index')->with('success', 'Clase reingresada correctamente.');
    }

    public function desactivar($id)
    {
        $clases = Clases::findOrFail($id);
        $clases->estado = 'Inactivo';
        $clases->save();

        return redirect()->route('clases.index')->with('success', 'Clase desactivada correctamente.');
    }
}
