<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->char('idioma', 2)->index();
            $table->string('titulo')->index();
            $table->longText('descripcion')->nullable();
            $table->integer('votos')->default(0);
            $table->boolean('publico')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
