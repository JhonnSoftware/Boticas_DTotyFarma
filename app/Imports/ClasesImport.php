<?php

namespace App\Imports;

use App\Models\Clases;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ClasesImport implements
    ToModel,
    WithHeadingRow,
    SkipsEmptyRows,
    WithValidation,
    WithUpserts,
    WithChunkReading,
    WithBatchInserts
{
    public function headingRow(): int
    {
        return 1; // primera fila = encabezados
    }

    public function model(array $row)
    {
        // Acepta 'nombre' en varias capitalizaciones
        $nombre = trim((string)($row['nombre'] ?? $row['Nombre'] ?? $row['NOMBRE'] ?? ''));

        // Normaliza 'estado' (por defecto Activo)
        $estadoRaw = (string)($row['estado'] ?? $row['Estado'] ?? $row['ESTADO'] ?? '');
        $estado = strtoupper(trim($estadoRaw));
        if (!in_array($estado, ['ACTIVO', 'INACTIVO'])) {
            $estado = 'ACTIVO';
        }

        // Upsert por 'nombre'
        return new Clases([
            'nombre' => $nombre,
            'estado' => ucfirst(strtolower($estado)), // "Activo" / "Inactivo"
        ]);
    }

    /** Clave única para upsert */
    public function uniqueBy()
    {
        return 'nombre';
    }

    /** Validaciones por fila */
    public function rules(): array
    {
        return [
            '*.nombre' => ['required', 'string', 'max:255'],
            '*.estado' => ['nullable', Rule::in(['Activo','Inactivo','ACTIVO','INACTIVO','activo','inactivo'])],
        ];
    }

    public function chunkSize(): int { return 500; }
    public function batchSize(): int { return 500; }
}
