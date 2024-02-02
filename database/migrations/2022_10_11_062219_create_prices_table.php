<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();

            $table->string('codigo')->nullable();
            $table->string('nombre')->nullable();
            $table->integer('capacidad')->nullable();
            $table->decimal('precioMinimo', 10, 2)->nullable();
            $table->decimal('precioMaximo', 10, 2)->nullable();

            // Alojamiento
            $table->unsignedBigInteger('accommodation_id');
            $table->foreign('accommodation_id')
                ->references('id')
                ->on('accommodations')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prices');
    }
};
