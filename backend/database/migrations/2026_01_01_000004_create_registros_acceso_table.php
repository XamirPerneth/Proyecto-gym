<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros_acceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $table->foreignId('membresia_id')->nullable()->constrained('membresias')->nullOnDelete();
            $table->dateTime('fecha_hora');
            $table->boolean('permitido');
            $table->string('motivo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_acceso');
    }
};
