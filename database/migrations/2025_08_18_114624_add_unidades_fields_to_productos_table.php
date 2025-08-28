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
        Schema::table('productos', function (Blueprint $table) {
            // Campo para definir cuántas unidades contiene un blister (opcional)
            $table->unsignedInteger('unidades_por_blister')
                  ->nullable()
                  ->after('cantidad_caja');

            // Campo para definir cuántas unidades contiene una caja (opcional)
            $table->unsignedInteger('unidades_por_caja')
                  ->nullable()
                  ->after('unidades_por_blister');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['unidades_por_blister', 'unidades_por_caja']);
        });
    }
};
