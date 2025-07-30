<?php

namespace App\Exports;

use App\Models\Clientes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientesExport implements FromCollection, WithHeadings
{
    /**
     * Retorna los datos
     */
    public function collection()
    {
        return Clientes::select('dni', 'nombre', 'apellidos', 'telefono', 'direccion', 'estado')->get();
    }

    /**
     * Encabezados del Excel
     */
    public function headings(): array
    {
        return [
            'DNI',
            'Nombre',
            'Apellidos',
            'Telefono',
            'Direccion',
            'Estado'
        ];
    }
}
