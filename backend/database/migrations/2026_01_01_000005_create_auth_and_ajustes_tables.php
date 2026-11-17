<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios_gimnasio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('email', 120)->unique();
            $table->string('rol', 60)->default('Administrador');
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('tokens_acceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios_gimnasio')->cascadeOnDelete();
            $table->string('hash_token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('ajustes_gimnasio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_gimnasio', 120)->default('Gym Control');
            $table->unsignedTinyInteger('descuento_trimestral')->default(10);
            $table->unsignedTinyInteger('descuento_anual')->default(20);
            $table->dateTime('hora_simulada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajustes_gimnasio');
        Schema::dropIfExists('tokens_acceso');
        Schema::dropIfExists('usuarios_gimnasio');
    }
};
