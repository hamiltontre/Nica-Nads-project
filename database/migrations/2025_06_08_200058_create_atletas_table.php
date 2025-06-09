<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('atletas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->integer('edad');
            $table->boolean('becado')->default(false);
            $table->enum('grupo', ['Federados', 'Novatos', 'Juniors', 'Principiantes']);
            $table->string('foto')->nullable(); // Almacena la ruta de la foto
             $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Relación con el usuario que lo creó
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('atletas');
    }
};