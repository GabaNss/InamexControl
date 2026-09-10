<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cadastro dos pacientes internados na instituicao (INAMEX).
     * 'ativa' indica se o paciente esta atualmente internado/em tratamento
     * (soft "alta" sem perder o historico de prescricoes e registros).
     */
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('prontuario')->unique();
            $table->string('quarto')->nullable();
            // Caminho/disco do arquivo de foto (armazenamento via filesystem, nao em base64).
            $table->string('foto')->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
            $table->softDeletes(); // preserva historico mesmo em caso de exclusao administrativa
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
