<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prescricao medica: liga um paciente a um medicamento, com dose e horario fixo
     * (manha/tarde). Cada prescricao ativa gera, diariamente, os RegistroMedicacao
     * que a equipe de enfermagem/tecnicos devem preencher (ver RegistroMedicacaoService).
     */
    public function up(): void
    {
        Schema::create('prescricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos')->restrictOnDelete();
            $table->string('dose'); // ex: "1 comprimido", "5ml"
            $table->enum('horario', ['manha', 'tarde']);
            // Apenas prescricoes ativas geram novos registros diarios; historico antigo nao e apagado.
            $table->boolean('ativa')->default(true);
            // Usuario (medico) responsavel pela prescricao, para fins de auditoria/responsabilidade clinica.
            $table->foreignId('prescrito_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescricoes');
    }
};
