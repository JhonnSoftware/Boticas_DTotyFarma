<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorias;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categorias::query();

        if ($request->has('buscar') && !empty($request->buscar)) {
            $busqueda = $request->buscar;
            $query->where('nombre', 'LIKE', "%$busqueda%");
        }

        $categorias = $query->get();

        return view('categorias.index', compact('categorias'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $categorias = Categorias::where('nombre', 'LIKE', "%$buscar%")->get();

        return view('categorias.partials.tabla', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Categorias::create([
            'nombre' => $request->nombre,
            'estado' => $request->estado,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoria registrada exitosamente.');
    }

    public function activar($id)
    {
        $cliente = Categorias::findOrFail($id);
        $cliente->estado = 'Activo';
        $cliente->save();

        return redirect()->route('categorias.index')->with('success', 'Categoria reingresada correctamente.');
    }

    public function desactivar($id)
    {
        $cliente = Categorias::findOrFail($id);
        $cliente->estado = 'Inactivo';
        $cliente->save();

        return redirect()->route('categorias.index')->with('success', 'Categoria desactivada correctamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $cliente = Categorias::findOrFail($id);

        $cliente->update([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoria actualizada correctamente.');
    }
}
