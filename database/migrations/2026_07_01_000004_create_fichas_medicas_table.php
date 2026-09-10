<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichas_medicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->text('laudos')->nullable();
            $table->text('diagnosticos')->nullable();
            $table->text('medicamentos_cronicos')->nullable();
            $table->foreignId('atualizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('paciente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichas_medicas');
    }
};
