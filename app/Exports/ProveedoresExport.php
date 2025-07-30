<?php

namespace App\Exports;

use App\Models\Proveedores;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProveedoresExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Proveedores::select('ruc', 'nombre', 'telefono', 'correo', 'direccion', 'contacto', 'estado')->get();
    }

    public function headings(): array
    {
        return ['RUC', 'Nombre', 'Telefono', 'Correo', 'Direccion', 'Contacto', 'Estado'];
    }
}
