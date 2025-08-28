<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class CategoriaProductoImport implements
    OnEachRow,
    WithHeadingRow,
    SkipsEmptyRows,
    WithValidation,
    WithChunkReading,
    WithBatchInserts
{
    public function headingRow(): int
    {
        return 1; // la primera fila del Excel son los encabezados
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function onRow(Row $row): void
    {
        $r = $row->toArray();

        // Asegúrate de que tu Excel tenga exactamente estas columnas:
        // id_producto, id_categoria
        DB::table('categoria_producto')->updateOrInsert(
            [
                'id_producto'  => $r['id_producto'],
                'id_categoria' => $r['id_categoria'],
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.id_producto'  => ['required', 'integer', 'exists:productos,id'],
            '*.id_categoria' => ['required', 'integer', 'exists:categorias,id'],
        ];
    }
}
