<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('buscar') && !empty($request->buscar)) {
            $busqueda = $request->buscar;
            $query->where('name', 'LIKE', "%$busqueda%")
                  ->orWhere('email', 'LIKE', "%$busqueda%");
        }

        $usuarios = $query->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $usuarios = User::where('name', 'LIKE', "%$buscar%")
            ->orWhere('email', 'LIKE', "%$buscar%")
            ->get();

        return view('usuarios.partials.tabla', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'rol'      => 'required|string',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'rol'      => $request->rol,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado exitosamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'rol'      => 'required|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $usuario = User::findOrFail($id);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->rol = $request->rol;

        if (!empty($request->password)) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete(); // Elimina permanentemente

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado permanentemente.');
    }
}
