<?php

namespace App\Exports;

use App\Models\DevolucionesVentas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DevolucionesVentasExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DevolucionesVentas::with(['venta.cliente', 'producto', 'usuario'])->get()->map(function ($item) {
            return [
                'Codigo Venta' => $item->venta->codigo ?? '',
                'Cliente'      => $item->venta->cliente->nombre . ' ' . $item->venta->cliente->apellidos,
                'Producto'     => $item->producto->descripcion ?? '',
                'Cantidad'     => $item->cantidad,
                'Motivo'       => $item->motivo,
                'Fecha'        => $item->fecha,
                'Usuario'      => $item->usuario->name ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Codigo Venta', 'Cliente', 'Producto', 'Cantidad', 'Motivo', 'Fecha', 'Usuario'];
    }
}
