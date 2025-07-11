<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documentos;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Documentos::query();

        if ($request->has('buscar') && !empty($request->buscar)) {
            $busqueda = $request->buscar;
            $query->where('nombre', 'LIKE', "%$busqueda%");
        }

        $documentos = $query->get();

        return view('documentos.index', compact('documentos'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $documentos = Documentos::where('nombre', 'LIKE', "%$buscar%")->get();

        return view('documentos.partials.tabla', compact('documentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Documentos::create([
            'nombre' => $request->nombre,
            'estado' => $request->estado,
        ]);

        return redirect()->route('documentos.index')->with('success', 'Documento registrado exitosamente.');
    }

    public function activar($id)
    {
        $cliente = Documentos::findOrFail($id);
        $cliente->estado = 'Activo';
        $cliente->save();

        return redirect()->route('documentos.index')->with('success', 'Documento reingresado correctamente.');
    }

    public function desactivar($id)
    {
        $cliente = Documentos::findOrFail($id);
        $cliente->estado = 'Inactivo';
        $cliente->save();

        return redirect()->route('documentos.index')->with('success', 'Documento desactivado correctamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $cliente = Documentos::findOrFail($id);

        $cliente->update([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('documentos.index')->with('success', 'Documento actualizado correctamente.');
    }
}
