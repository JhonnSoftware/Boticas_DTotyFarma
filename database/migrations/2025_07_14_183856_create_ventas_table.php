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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('id_cliente')->nullable()->constrained('clientes');
            $table->decimal('total', 10, 2);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('descuento_total', 10, 2)->default(0);
            $table->dateTime('fecha');
            $table->enum('estado', ['Activo', 'Anulado', 'Devuelto'])->default('Activo');
            $table->foreignId('id_pago')->constrained('tipopago');
            $table->foreignId('id_documento')->constrained('documento');
            $table->foreignId('usuario_id')->constrained('users'); // usuario que registró la venta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
