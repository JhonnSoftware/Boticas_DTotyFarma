<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientesExport;

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
            'dni' => 'required|digits:8|unique:clientes,dni',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'nullable|string|max:100',
            'telefono' => 'nullable|digits:9',
            'direccion' => 'nullable|string|max:255',
            'estado' => 'required|in:Activo,Inactivo',
        ], [
            'dni.unique' => 'Ya existe un cliente con este DNI.',
            'telefono.digits' => 'El número de teléfono debe tener exactamente 9 dígitos.',
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

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'dni' => ['required', 'digits:8', Rule::unique('clientes', 'dni')->ignore($id)],
            'nombre' => 'required|string|max:100',
            'apellidos' => 'nullable|string|max:100',
            'telefono' => 'nullable|digits:9',
            'direccion' => 'nullable|string|max:255',
        ], [
            'dni.unique' => 'Ya existe un cliente con este DNI.',
            'telefono.digits' => 'El número de teléfono debe tener exactamente 9 dígitos.',
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

    public function exportar($formato)
    {
        $clientes = Clientes::all();

        switch ($formato) {
            case 'pdf':
                $pdf = Pdf::loadView('exportaciones.clientes_pdf', compact('clientes'));
                return $pdf->download('clientes.pdf');

            case 'xlsx':
                return Excel::download(new ClientesExport, 'clientes.xlsx');

            case 'csv':
                return Excel::download(new ClientesExport, 'clientes.csv');

            case 'txt':
                $contenido = '';
                foreach ($clientes as $cliente) {
                    $contenido .= "{$cliente->dni}\t{$cliente->nombre}\t{$cliente->apellidos}\t{$cliente->telefono}\t{$cliente->direccion}\t{$cliente->estado}\n";
                }

                return response($contenido)
                    ->header('Content-Type', 'text/plain')
                    ->header('Content-Disposition', 'attachment; filename="clientes.txt"');

            default:
                return back()->with('error', 'Formato no válido.');
        }
    }
}
