<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericosImport;

class ImportGenericos extends Command
{
    /**
     * Ejemplo de uso:
     *   php artisan import:genericos genericos_totyfarma.xlsx
     *   php artisan import:genericos storage/app/imports/genericos_totyfarma.xlsx
     */
    protected $signature = 'import:genericos 
                            {path : Ruta del archivo .xlsx/.xls/.csv (relativa o absoluta)}';

    protected $description = 'Importa genéricos desde un archivo Excel/CSV a la tabla genericos';

    public function handle(): int
    {
        $inputPath = $this->argument('path');

        // 1) Si pasó ruta absoluta o relativa existente, úsala
        $path = $inputPath;
        if (!is_file($path)) {
            // 2) Intentar dentro de storage/app/imports
            $alt = storage_path('app/imports/' . ltrim($inputPath, '/\\'));
            if (is_file($alt)) {
                $path = $alt;
            }
        }

        if (!is_file($path)) {
            $this->error("Archivo no encontrado. Probé: 
- {$inputPath}
- " . storage_path('app/imports/' . ltrim($inputPath, '/\\')));
            return self::FAILURE;
        }

        $this->info("Importando genéricos desde: {$path}");

        try {
            Excel::import(new GenericosImport, $path);
            $this->info('✅ Genéricos importados correctamente.');
            return self::SUCCESS;

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $this->error('❌ Se encontraron errores de validación:');
            foreach ($e->failures() as $failure) {
                $fila = $failure->row();
                $errores = implode('; ', $failure->errors());
                $this->line("  • Fila {$fila}: {$errores}");
            }
            return self::FAILURE;

        } catch (\Throwable $e) {
            $this->error('❌ Error inesperado durante la importación: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
