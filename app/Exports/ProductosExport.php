<?php

namespace App\Exports;

use App\Models\Productos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Productos::with(['proveedor', 'categoria'])->get()->map(function ($producto) {
            return [
                'Codigo' => $producto->codigo,
                'Descripcion' => $producto->descripcion,
                'Presentacion' => $producto->presentacion,
                'Laboratorio' => $producto->laboratorio,
                'Lote' => $producto->lote,
                'Cantidad' => $producto->cantidad,
                'Stock Minimo' => $producto->stock_minimo,
                'Descuento' => $producto->descuento,
                'Fecha de Vencimiento' => $producto->fecha_vencimiento,
                'Precio de Compra' => $producto->precio_compra,
                'Precio de Venta' => $producto->precio_venta,
                'Proveedor' => optional($producto->proveedor)->nombre,
                'Categoria' => optional($producto->categoria)->nombre,
                'Estado' => $producto->estado,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Codigo', 'Descripcion', 'Presentacion', 'Laboratorio', 'Lote', 'Cantidad', 'Stock Minimo', 'Descuento',
            'Fecha de Vencimiento', 'Precio de Compra', 'Precio de Venta', 'Proveedor', 'Categoria', 'Estado',
        ];
    }
}
