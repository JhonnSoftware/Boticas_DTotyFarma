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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_producto');
            $table->string('tipo_movimiento'); // Entrada, Salida, Ajuste, Devolución
            $table->string('origen')->nullable(); // Venta, Compra, Devolución, Ajuste
            $table->string('documento_ref')->nullable(); // Ej: B001-12345
            $table->dateTime('fecha'); // Fecha del movimiento (editable por el usuario)
            $table->integer('cantidad'); // Positivo (entrada) o negativo (salida)
            $table->integer('stock_anterior');
            $table->integer('stock_actual');
            $table->text('observacion')->nullable();

            $table->unsignedBigInteger('usuario_id');
            $table->timestamps();

            $table->foreign('id_producto')->references('id')->on('productos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
