<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('planilla_turista', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planilla_id')->constrained('planillas')->onDelete('cascade');
            $table->foreignId('turista_id')->constrained('turistas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planilla_turista');
    }
};
