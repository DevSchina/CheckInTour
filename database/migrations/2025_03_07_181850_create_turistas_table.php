<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('turistas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_documento', ['RG', 'CPF', 'DNI']);
            $table->string('numero_documento')->nullable();
            $table->string('nombre');
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->date('fecha');
            $table->foreignId('guia_id')->constrained('users')->onDelete('cascade');
            $table->boolean('asistencia')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turistas');
    }
};
