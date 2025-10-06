<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            // Identificación
            $table->string('codigo')->unique();
            $table->text('descripcion');

            // Atributos base
            $table->string('presentacion');
            $table->string('laboratorio');

            // Lote y vencimiento
            $table->string('lote');
            $table->date('fecha_vencimiento');

            // Stock 
            $table->unsignedInteger('cantidad')->default(0);
            $table->unsignedInteger('cantidad_blister')->nullable()->default(null);
            $table->unsignedInteger('cantidad_caja')->nullable()->default(null);

            $table->unsignedInteger('unidades_por_blister')->nullable()->default(null);
            $table->unsignedInteger('unidades_por_caja')->nullable()->default(null);

            //Stock Minimo
            $table->unsignedInteger('stock_minimo')->default(0);

            // Descuento unitario (si aplica)
            $table->decimal('descuento', 10, 2)->nullable();   // ej. 10 = 10% o S/10
            $table->decimal('descuento_blister', 10, 2)->nullable();  // null = no aplica
            $table->decimal('descuento_caja', 10, 2)->nullable();

            // Precios de compra
            $table->decimal('precio_compra', 12, 2);
            $table->decimal('precio_compra_blister', 12, 2)->nullable();
            $table->decimal('precio_compra_caja', 12, 2)->nullable();

            // Precios de venta
            $table->decimal('precio_venta', 12, 2); // unidad
            $table->decimal('precio_venta_blister', 12, 2)->nullable();
            $table->decimal('precio_venta_caja', 12, 2)->nullable();

            // Imagen
            $table->string('foto')->nullable();

            // Otras relaciones
            $table->foreignId('id_proveedor')->constrained('proveedores');
            // $table->foreignId('id_categoria')->constrained('categorias');

            // Relaciones con catálogos
            $table->foreignId('id_clase')->nullable()->constrained('clases')->nullOnDelete();
            $table->foreignId('id_generico')->nullable()->constrained('genericos')->nullOnDelete();

            // Estado
            $table->string('estado', 20)->default('Activo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
