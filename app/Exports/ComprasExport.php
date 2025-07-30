<?php

namespace App\Exports;

use App\Models\Compras;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ComprasExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Compras::with(['proveedor', 'pago', 'documento', 'usuario'])->get()->map(function ($c) {
            return [
                'Codigo'      => $c->codigo,
                'Proveedor'   => $c->proveedor->nombre ?? '',
                'Documento'   => $c->documento->nombre ?? '',
                'Pago'        => $c->pago->nombre ?? '',
                'Total'       => $c->total,
                'Estado'      => $c->estado,
                'Fecha'       => $c->fecha,
                'Registrado por' => $c->usuario->name ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Codigo', 'Proveedor', 'Documento', 'Pago', 'Total', 'Estado', 'Fecha', 'Registrado por'];
    }
}

