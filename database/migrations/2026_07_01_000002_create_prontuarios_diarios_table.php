<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prontuarios_diarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->date('data');
            $table->text('conteudo')->nullable();
            $table->foreignId('atualizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['paciente_id', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prontuarios_diarios');
    }
};
