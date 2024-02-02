<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('favouritables', function (Blueprint $table) {
            $table->id();

            // Usuario
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Recurso o plan
            $table->unsignedBigInteger('favouritables_id');
            $table->string('favouritables_type');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('favouritables');
    }
};
