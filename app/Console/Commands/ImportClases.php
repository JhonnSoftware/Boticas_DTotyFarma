<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClasesImport;

class ImportClases extends Command
{
    /**
     * Ejemplos:
     *  php artisan import:clases clases_totyfarma.xlsx
     *  php artisan import:clases storage/app/imports/clases_totyfarma.xlsx
     */
    protected $signature = 'import:clases 
                            {path : Ruta del archivo .xlsx/.xls/.csv (relativa o absoluta)}';

    protected $description = 'Importa clases desde un archivo Excel/CSV a la tabla clases';

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

        $this->info("Importando clases desde: {$path}");

        try {
            Excel::import(new ClasesImport, $path);
            $this->info('✅ Clases importadas correctamente.');
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
