<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membresias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('planes');
            $table->string('tipo_tarifa', 20);
            $table->string('estado', 20);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedInteger('precio_final');
            $table->date('fecha_congelamiento')->nullable();
            $table->unsignedSmallInteger('dias_congelamiento')->default(0);
            $table->unsignedSmallInteger('dias_congelados_acumulados')->default(0);
            $table->timestamps();

            $table->index(['socio_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membresias');
    }
};
