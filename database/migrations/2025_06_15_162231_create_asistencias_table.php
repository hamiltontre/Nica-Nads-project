<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Primero verifica si la tabla atletas existe
        if (Schema::hasTable('atletas')) {
            Schema::create('asistencias', function (Blueprint $table) {
                $table->id();
                $table->foreignId('atleta_id')->constrained('atletas')->onDelete('cascade');
                $table->date('fecha'); // Cambiado a tipo date
                $table->enum('turno', ['mañana', 'tarde'])->default('tarde');
                $table->enum('estado', ['presente', 'ausente', 'libre'])->default('libre');
                $table->timestamps();
                
                $table->unique(['atleta_id', 'fecha', 'turno']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
};