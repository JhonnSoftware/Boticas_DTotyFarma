<?php

namespace App\Exports;

use App\Models\Productos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Productos::with(['proveedor', 'categorias', 'clase', 'generico'])
            ->get()
            ->map(function ($p) {
                return [
                    // Orden debe coincidir con headings()
                    $p->codigo ?? '',
                    $p->descripcion ?? '',
                    $p->presentacion ?? '',
                    $p->laboratorio ?? '',
                    $p->lote ?? '',
                    $p->fecha_vencimiento ? \Carbon\Carbon::parse($p->fecha_vencimiento)->format('Y-m-d') : '',

                    $p->cantidad ?? 0,
                    $p->cantidad_blister ?? null,
                    $p->cantidad_caja ?? null,

                    $p->stock_minimo ?? 0,
                    $p->stock_minimo_blister ?? null,
                    $p->stock_minimo_caja ?? null,

                    $p->descuento ?? 0,
                    $p->descuento_blister ?? null,
                    $p->descuento_caja ?? null,

                    $p->precio_compra ?? 0,
                    $p->precio_compra_blister ?? null,
                    $p->precio_compra_caja ?? null,

                    $p->precio_venta ?? 0,
                    $p->precio_venta_blister ?? null,
                    $p->precio_venta_caja ?? null,

                    optional($p->proveedor)->nombre ?? '',
                    $p->categorias?->pluck('nombre')->implode(', ') ?? '',
                    optional($p->clase)->nombre ?? '',
                    optional($p->generico)->nombre ?? '',
                    $p->estado ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Codigo',
            'Descripcion',
            'Presentacion',
            'Laboratorio',
            'Lote',
            'FechaVenc',

            'Cant_Unid',
            'Cant_Blister',
            'Cant_Caja',

            'Min_Unid',
            'Min_Blister',
            'Min_Caja',

            'Desc_Unid_%',
            'Desc_Blister_%',
            'Desc_Caja_%',

            'PCompra_Unid',
            'PCompra_Blister',
            'PCompra_Caja',

            'PVenta_Unid',
            'PVenta_Blister',
            'PVenta_Caja',

            'Proveedor',
            'Categorias',
            'Clase',
            'Generico',
            'Estado',
        ];
    }
}
