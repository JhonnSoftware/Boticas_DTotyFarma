<?php

namespace App\Exports;

use App\Models\Ventas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Ventas::with(['cliente', 'pago', 'documento', 'usuario'])
            ->get()
            ->map(function ($venta) {
                return [
                    'Codigo' => $venta->codigo,
                    'Cliente' => optional($venta->cliente)->nombre . ' ' . optional($venta->cliente)->apellidos,
                    'Documento' => optional($venta->documento)->nombre,
                    'Pago' => optional($venta->pago)->nombre,
                    'Total' => $venta->total,
                    'IGV' => $venta->igv,
                    'Descuento' => $venta->descuento_total,
                    'Fecha' => $venta->fecha,
                    'Estado' => $venta->estado,
                    'Usuario' => optional($venta->usuario)->name,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Codigo', 'Cliente', 'Documento', 'Pago', 'Total', 'IGV',
            'Descuento', 'Fecha', 'Estado', 'Usuario'
        ];
    }
}
