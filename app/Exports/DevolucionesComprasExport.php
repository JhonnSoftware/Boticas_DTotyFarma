<?php

namespace App\Exports;

use App\Models\DevolucionesCompras;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DevolucionesComprasExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DevolucionesCompras::with(['compra.proveedor', 'producto', 'usuario'])->get()->map(function ($item) {
            return [
                'Codigo Compra' => $item->compra->codigo ?? '',
                'Proveedor'     => $item->compra->proveedor->nombre ?? '',
                'Producto'      => $item->producto->descripcion ?? '',
                'Cantidad'      => $item->cantidad,
                'Motivo'        => $item->motivo,
                'Fecha'         => $item->fecha,
                'Usuario'       => $item->usuario->name ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Codigo Compra', 'Proveedor', 'Producto', 'Cantidad', 'Motivo', 'Fecha', 'Usuario'];
    }
}
