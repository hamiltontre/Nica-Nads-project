<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Modificar la columna estado para incluir 'justificado'
            $table->enum('estado', ['presente', 'ausente', 'justificado', 'libre'])
                  ->default('libre')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Revertir al estado original si es necesario
            $table->enum('estado', ['presente', 'ausente', 'libre'])
                  ->default('libre')
                  ->change();
        });
    }
};