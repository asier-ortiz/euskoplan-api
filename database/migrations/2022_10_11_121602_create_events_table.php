<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('fechaActualizacion');
            $table->string('idioma');

            // Datos generales
            $table->string('codigo')->nullable();
            $table->string('tipoRecurso')->nullable();
            $table->string('nombre')->nullable();
            $table->longText('descripcion')->nullable();
            $table->string('urlFichaPortal')->nullable();

            // Datos generales / localización
            $table->string('codigoProvincia')->nullable();
            $table->string('codigoMunicipio')->nullable();
            $table->string('codigoLocalidad')->nullable();
            $table->string('nombreProvincia')->nullable();
            $table->string('nombreMunicipio')->nullable();
            $table->string('nombreLocalidad')->nullable();

            // Datos generales / georeferenciación
            $table->string('gmLongitud')->nullable();
            $table->string('gmLatitud')->nullable();

            // Datos agenda
            $table->string('subtipoRecurso')->nullable();
            $table->string('nombreSubtipoRecurso')->nullable();
            $table->date('fechaInicio')->nullable();
            $table->date('fechaFin')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
