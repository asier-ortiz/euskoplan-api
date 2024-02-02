<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('localities', function (Blueprint $table) {
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
            $table->longText('codigoProvincia')->nullable();
            $table->longText('codigoMunicipio')->nullable();
            $table->longText('codigoLocalidad')->nullable();
            $table->longText('nombreProvincia')->nullable();
            $table->longText('nombreMunicipio')->nullable();
            $table->longText('nombreLocalidad')->nullable();

            // Datos generales / georeferenciación
            $table->string('gmLongitud')->nullable();
            $table->string('gmLatitud')->nullable();

            // Datos localidad
            $table->string('numHabitantes')->nullable();
            $table->string('superficie')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('localities');
    }
};
