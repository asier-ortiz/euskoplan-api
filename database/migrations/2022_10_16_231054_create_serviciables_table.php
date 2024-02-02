<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('serviceables', function (Blueprint $table) {
            $table->id();

            // Servicio
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('cascade');

            // Recurso
            $table->unsignedBigInteger('serviceables_id');
            $table->string('serviceables_type');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('serviceables');
    }
};
