<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Clientes::query();

        if ($request->has('buscar') && !empty($request->buscar)) {
            $busqueda = $request->buscar;
            $query->where('nombre', 'LIKE', "%$busqueda%")
                ->orWhere('dni', 'LIKE', "%$busqueda%");
        }

        $clientes = $query->get();

        return view('clientes.index', compact('clientes'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $clientes = Clientes::where('nombre', 'LIKE', "%$buscar%")
            ->orWhere('dni', 'LIKE', "%$buscar%")
            ->orWhere('apellidos', 'LIKE', "%$buscar%")
            ->get();

        return view('clientes.partials.tabla', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|digits:8',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Clientes::create([
            'dni' => $request->dni,
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'estado' => $request->estado,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado exitosamente.');
    }

    public function activar($id)
    {
        $cliente = Clientes::findOrFail($id);
        $cliente->estado = 'Activo';
        $cliente->save();

        return redirect()->route('clientes.index')->with('success', 'Cliente reingresado correctamente.');
    }

    public function desactivar($id)
    {
        $cliente = Clientes::findOrFail($id);
        $cliente->estado = 'Inactivo';
        $cliente->save();

        return redirect()->route('clientes.index')->with('success', 'Cliente desactivado correctamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'dni' => 'required|digits:8',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente = Clientes::findOrFail($id);

        $cliente->update([
            'dni' => $request->dni,
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }
}
