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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // ejemplo: COMP-00001
            $table->foreignId('id_proveedor')->constrained('proveedores');
            $table->decimal('total', 10, 2);
            $table->decimal('igv', 10, 2)->default(0);
            $table->dateTime('fecha');
            $table->enum('estado', ['Activo', 'Anulado', 'Devuelto'])->default('Activo');
            $table->foreignId('id_pago')->constrained('tipopago');
            $table->foreignId('id_documento')->constrained('documento');
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('archivo_factura')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
