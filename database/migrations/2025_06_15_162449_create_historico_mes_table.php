<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historico_mes', function (Blueprint $table) {
            $table->id();
            $table->string('mes');
            $table->integer('anio');
            $table->json('datos'); // Almacenará los resúmenes por atleta
            $table->timestamps();
            
            $table->unique(['mes', 'anio']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('historico_mes');
    }
};