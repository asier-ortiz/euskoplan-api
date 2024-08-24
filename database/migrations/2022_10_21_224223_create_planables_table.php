<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('planables', function (Blueprint $table) {
            $table->id();
            $table->integer('indice');
            $table->integer('dia')->default(1); // Nuevo campo 'dia' para indicar el día del paso
            $table->longText('indicaciones')->nullable();
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->morphs('planables'); // Usa morphs para generar automáticamente los campos de polimorfismo
            $table->timestamps();
        });

        // Añade el índice compuesto para optimizar consultas
        Schema::table('planables', function (Blueprint $table) {
            $table->index(['plan_id', 'indice']);
            $table->index(['plan_id', 'dia']); // Índice para mejorar consultas basadas en 'dia'
        });
    }

    public function down()
    {
        Schema::dropIfExists('planables');
    }
};
