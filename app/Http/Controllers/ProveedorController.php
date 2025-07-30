<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedores;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProveedoresExport;


class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedores::query();

        if ($request->has('buscar') && !empty($request->buscar)) {
            $busqueda = $request->buscar;
            $query->where('nombre', 'LIKE', "%$busqueda%")
                ->orWhere('ruc', 'LIKE', "%$busqueda%");
        }

        $proveedores = $query->get();

        return view('proveedores.index', compact('proveedores'));
    }

    public function buscar(Request $request)
    {
        $buscar = $request->input('buscar');

        $proveedores = Proveedores::where('nombre', 'LIKE', "%$buscar%")
            ->orWhere('ruc', 'LIKE', "%$buscar%")
            ->get();

        return view('proveedores.partials.tabla', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruc' => 'required|digits:11|unique:proveedores,ruc',
            'nombre' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Proveedores::create([
            'ruc' => $request->ruc,
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'direccion' => $request->direccion,
            'contacto' => $request->contacto,
            'estado' => $request->estado,
        ]);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    public function activar($id)
    {
        $proveedores = Proveedores::findOrFail($id);
        $proveedores->estado = 'Activo';
        $proveedores->save();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor reingresado correctamente.');
    }

    public function desactivar($id)
    {
        $proveedores = Proveedores::findOrFail($id);
        $proveedores->estado = 'Inactivo';
        $proveedores->save();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor desactivado correctamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'ruc' => ['required', 'digits:11', Rule::unique('proveedores', 'ruc')->ignore($id)],
            'nombre' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'contacto' => 'nullable|string|max:255',
        ]);

        $proveedores = Proveedores::findOrFail($id);

        $proveedores->update([
            'ruc' => $request->ruc,
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'direccion' => $request->direccion,
            'contacto' => $request->contacto,
        ]);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function exportar($formato)
    {
        $proveedores = Proveedores::all();

        switch ($formato) {
            case 'pdf':
                $pdf = Pdf::loadView('exportaciones.proveedores_pdf', compact('proveedores'));
                return $pdf->download('proveedores.pdf');

            case 'xlsx':
                return Excel::download(new ProveedoresExport, 'proveedores.xlsx');

            case 'csv':
                return Excel::download(new ProveedoresExport, 'proveedores.csv');

            case 'txt':
                $contenido = '';
                foreach ($proveedores as $p) {
                    $contenido .= "{$p->ruc}\t{$p->nombre}\t{$p->telefono}\t{$p->correo}\t{$p->direccion}\t{$p->contacto}\t{$p->estado}\n";
                }

                return response($contenido)
                    ->header('Content-Type', 'text/plain')
                    ->header('Content-Disposition', 'attachment; filename="proveedores.txt"');

            default:
                return back()->with('error', 'Formato no válido.');
        }
    }
}
