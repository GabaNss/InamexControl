<?php

namespace Tests\Unit\Services;

use App\Models\Paciente;
use App\Models\User;
use App\Services\AuditoriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditoriaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_grava_quem_fez_o_que_e_o_alvo(): void
    {
        $usuario = User::factory()->create();
        $paciente = Paciente::factory()->create();

        $log = (new AuditoriaService())->registrar(
            usuario: $usuario,
            acao: 'paciente.atualizado',
            alvo: $paciente,
            dadosAntes: ['nome' => 'Antigo'],
            dadosDepois: ['nome' => 'Novo'],
        );

        $this->assertSame($usuario->id, $log->usuario_id);
        $this->assertSame('paciente.atualizado', $log->acao);
        $this->assertSame(Paciente::class, $log->alvo_type);
        $this->assertSame($paciente->id, $log->alvo_id);
        $this->assertSame(['nome' => 'Antigo'], $log->dados_antes);
        $this->assertSame(['nome' => 'Novo'], $log->dados_depois);
    }

    public function test_registrar_aceita_usuario_nulo_para_acoes_do_sistema(): void
    {
        $log = (new AuditoriaService())->registrar(
            usuario: null,
            acao: 'diario.encerrado',
        );

        $this->assertNull($log->usuario_id);
        $this->assertNull($log->ip_address);
        $this->assertNull($log->user_agent);
    }

    public function test_registrar_sem_alvo_nao_preenche_colunas_polimorficas(): void
    {
        $log = (new AuditoriaService())->registrar(
            usuario: User::factory()->create(),
            acao: 'login.efetuado',
        );

        $this->assertNull($log->alvo_type);
        $this->assertNull($log->alvo_id);
    }
}
