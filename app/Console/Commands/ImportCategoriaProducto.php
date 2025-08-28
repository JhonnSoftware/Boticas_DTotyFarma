<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CategoriaProductoImport;

class ImportCategoriaProducto extends Command
{
    protected $signature = 'import:categoria_producto {path : Ruta del .xlsx, p.ej. storage/app/categoria_producto_totyfarma.xlsx}';
    protected $description = 'Importa la tabla pivote categoria_producto desde un archivo Excel';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("No se encontró el archivo en: {$path}");
            $this->line('Ejemplos:');
            $this->line('- storage/app/categoria_producto_totyfarma.xlsx');
            $this->line('- /full/path/a/categoria_producto_totyfarma.xlsx');
            return self::FAILURE;
        }

        Excel::import(new CategoriaProductoImport, $path);
        $this->info('Importación de categoria_producto completada ✅');
        return self::SUCCESS;
    }
}
