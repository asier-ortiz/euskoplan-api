<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('museums', function (Blueprint $table) {
            $table->id();

            $table->string('fechaActualizacion');
            $table->string('idioma');

            // Datos generales
            $table->string('codigo')->nullable();
            $table->string('tipoRecurso')->nullable();
            $table->string('nombre')->nullable();
            $table->longText('descripcion')->nullable();
            $table->string('urlFichaPortal')->nullable();

            // Datos generales / datos contacto
            $table->string('direccion')->nullable();
            $table->string('codigoPostal')->nullable();
            $table->string('numeroTelefono')->nullable();
            $table->string('email')->nullable();
            $table->string('paginaWeb')->nullable();

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

            // Datos arte y cultura
            $table->string('subTipoRecurso')->nullable();
            $table->string('nombreSubTipoRecurso')->nullable();
            $table->string('tematica')->nullable();
            $table->string('nombreTematica')->nullable();
            $table->string('capacidad')->nullable();
            $table->longText('horario')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('museums');
    }
};
