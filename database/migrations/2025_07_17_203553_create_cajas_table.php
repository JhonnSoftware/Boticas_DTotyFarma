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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto_apertura', 10, 2);    
            $table->timestamp('fecha_apertura')->default(now());
            $table->decimal('monto_cierre', 10, 2)->nullable();   
            $table->timestamp('fecha_cierre')->nullable();     
            $table->string('estado')->default('abierta'); 
            $table->unsignedBigInteger('usuario_id');      
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
