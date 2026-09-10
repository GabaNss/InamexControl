<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trilha de auditoria generica (quem fez, o que, quando, de onde). Preenchida
     * pelo App\Services\AuditoriaService a cada acao sensivel (criar/editar/excluir
     * registros de medicacao, encerrar o dia, alterar prescricoes, etc.).
     * 'acao_em' (polymorphic) aponta para o registro afetado quando aplicavel.
     */
    public function up(): void
    {
        Schema::create('logs_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('acao'); // ex: "registro_medicacao.criado", "diario.encerrado"
            // Relacionamento polimorfico com a entidade afetada (paciente, prescricao, registro, etc.).
            $table->nullableMorphs('alvo');
            $table->json('dados_antes')->nullable();
            $table->json('dados_depois')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_auditoria');
    }
};
