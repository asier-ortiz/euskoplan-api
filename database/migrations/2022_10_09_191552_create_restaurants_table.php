<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
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
            $table->string('numeroTelefono2')->nullable();
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

            // Datos restauración
            $table->string('subtipoRecurso')->nullable();
            $table->string('nombreSubtipoRecurso')->nullable();
            $table->string('capacidad')->nullable();

            // Indexes
            $table->index('idioma');
            $table->index('nombre');
            $table->index('nombreProvincia');
            $table->index('nombreMunicipio');
            $table->index('nombreSubtipoRecurso');
            $table->fullText('descripcion');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
