<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registro de administracao (ou nao) de uma dose prescrita, em uma data/turno
     * especifico. REGRA CRITICA: depois que App\Models\DiarioStatus.encerrado = true
     * para a 'data' deste registro, a linha se torna IMUTAVEL — nenhuma edicao ou
     * exclusao e permitida (reforcado em App\Policies\RegistroMedicacaoPolicy e
     * App\Services\RegistroMedicacaoService::class, nunca apenas no frontend).
     * Por isso so existe 'created_at' (sem updated_at): o registro nasce e nao
     * deveria mais ser tocado apos o fechamento do dia.
     */
    public function up(): void
    {
        Schema::create('registros_medicacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescricao_id')->constrained('prescricoes')->restrictOnDelete();
            $table->date('data');
            $table->enum('turno', ['manha', 'tarde']);
            $table->boolean('administrado')->default(false);
            $table->text('observacao')->nullable();
            // Usuario (enfermeiro/tecnico) que realizou o registro — base da trilha de
            // auditoria. Nulo ate alguem de fato marcar a dose (o registro "nasce"
            // pendente, gerado pelo sistema a meia-noite, antes de qualquer acao humana).
            $table->foreignId('usuario_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();

            // Evita duplicidade: uma prescricao so pode ter um registro por data+turno.
            $table->unique(['prescricao_id', 'data', 'turno']);
            $table->index(['data', 'turno']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_medicacao');
    }
};
