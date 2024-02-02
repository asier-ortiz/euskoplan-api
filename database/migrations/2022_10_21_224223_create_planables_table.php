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
            $table->longText('indicaciones')->nullable();

            // Plan
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')
                ->references('id')
                ->on('plans')
                ->onDelete('cascade');

            // Recurso
            $table->unsignedBigInteger('planables_id');
            $table->string('planables_type');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('planables');
    }
};
