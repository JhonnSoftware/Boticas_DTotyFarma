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
            $table->string('codigo');
            $table->text('descripcion');
            $table->string('presentacion');
            $table->string('laboratorio');
            $table->integer('lote');
            $table->integer('cantidad')->default(0);
            $table->integer('stock_minimo');
            $table->decimal('descuento');
            $table->date('fecha_vencimiento');
            $table->decimal('precio_compra', 8, 2);
            $table->decimal('precio_venta', 8, 2);
            $table->string('foto');
            $table->foreignId('id_proveedor')->constrained('proveedores');
            $table->foreignId('id_categoria')->constrained('categorias');
            $table->string('estado')->default('Activo'); // Activo por defecto
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
