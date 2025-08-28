<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductosImport;

class ImportProductos extends Command
{
    /**
     * Uso:
     *  php artisan import:productos productos_totyfarma.xlsx
     *  php artisan import:productos storage/app/imports/productos_totyfarma.xlsx
     */
    protected $signature = 'import:productos {path : Ruta del archivo .xlsx/.xls/.csv (relativa o absoluta)}';
    protected $description = 'Importa productos desde un archivo Excel/CSV a la tabla productos';

    public function handle(): int
    {
        $inputPath = $this->argument('path');
        $path = $inputPath;

        if (!is_file($path)) {
            $alt = storage_path('app/imports/' . ltrim($inputPath, '/\\'));
            if (is_file($alt)) $path = $alt;
        }

        if (!is_file($path)) {
            $this->error("Archivo no encontrado. Probé:
- {$inputPath}
- " . storage_path('app/imports/' . ltrim($inputPath, '/\\')));
            return self::FAILURE;
        }

        $this->info("Importando productos desde: {$path}");

        try {
            Excel::import(new ProductosImport, $path);
            $this->info('✅ Productos importados correctamente.');
            return self::SUCCESS;

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $this->error('❌ Errores de validación:');
            foreach ($e->failures() as $f) {
                $this->line("  • Fila {$f->row()}: " . implode('; ', $f->errors()));
            }
            return self::FAILURE;

        } catch (\Throwable $e) {
            $this->error('❌ Error inesperado: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
