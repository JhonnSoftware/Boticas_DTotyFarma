<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genericos;
use Illuminate\Validation\Rule;

class GenericoController extends Controller
{
    public function index(Request $request)
    {
        $perPage  = $request->integer('per_page', 80);
        $busqueda = trim((string) $request->get('buscar', ''));

        $query = Genericos::select('id', 'nombre', 'estado');

        if ($busqueda !== '') {
            $query->where('nombre', 'LIKE', '%' . $busqueda . '%');
            // Si algún día cambias a prefijo, usa $busqueda.'%' para que el índice ayude
        }

        $genericos = $query->orderBy('nombre')->paginate($perPage)->withQueryString();

        return view('genericos.index', compact('genericos', 'busqueda', 'perPage'));
    }

    public function editPartial($id)
    {
        $generico = Genericos::select('id', 'nombre', 'estado')->findOrFail($id);

        // Renderiza SOLO el contenido del modal (header+form+footer)
        return view('genericos.partials.modal_editar', compact('generico'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $genericos = Genericos::where('nombre', 'LIKE', "%$buscar%")->get();

        return view('genericos.partials.tabla', compact('genericos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:genericos,nombre',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Genericos::create([
            'nombre' => $request->nombre,
            'estado' => $request->estado,
        ]);

        return redirect()->route('genericos.index')->with('success', 'Generico registrada exitosamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([

            'nombre' => ['required', 'string', 'max:100', Rule::unique('genericos', 'nombre')->ignore($id)],
        ]);

        $genericos = Genericos::findOrFail($id);

        $genericos->update([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('genericos.index')->with('success', 'Generico actualizado correctamente.');
    }

    public function activar($id)
    {
        $genericos = Genericos::findOrFail($id);
        $genericos->estado = 'Activo';
        $genericos->save();

        return redirect()->route('genericos.index')->with('success', 'Generico reingresado correctamente.');
    }

    public function desactivar($id)
    {
        $genericos = Genericos::findOrFail($id);
        $genericos->estado = 'Inactivo';
        $genericos->save();

        return redirect()->route('genericos.index')->with('success', 'Generico desactivado correctamente.');
    }
}
