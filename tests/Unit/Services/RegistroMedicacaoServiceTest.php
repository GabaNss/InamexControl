<?php

namespace Tests\Unit\Services;

use App\Exceptions\DiaEncerradoException;
use App\Models\Prescricao;
use App\Models\RegistroMedicacao;
use App\Models\User;
use App\Services\AuditoriaService;
use App\Services\DiarioService;
use App\Services\RegistroMedicacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RegistroMedicacaoServiceTest extends TestCase
{
    use RefreshDatabase;

    private RegistroMedicacaoService $registroMedicacaoService;

    private DiarioService $diarioService;

    protected function setUp(): void
    {
        parent::setUp();

        $auditoriaService = new AuditoriaService();
        $this->diarioService = new DiarioService($auditoriaService);
        $this->registroMedicacaoService = new RegistroMedicacaoService($this->diarioService, $auditoriaService);
    }

    public function test_gerar_registros_do_dia_cria_um_registro_por_prescricao_ativa(): void
    {
        $hoje = Carbon::today();
        Prescricao::factory()->create(['ativa' => true, 'horario' => 'manha']);
        Prescricao::factory()->create(['ativa' => true, 'horario' => 'tarde']);
        Prescricao::factory()->create(['ativa' => false, 'horario' => 'manha']);

        $this->registroMedicacaoService->gerarRegistrosDoDia($hoje);

        $this->assertSame(2, RegistroMedicacao::where('data', $hoje->toDateString())->count());
    }

    public function test_gerar_registros_do_dia_e_idempotente(): void
    {
        $hoje = Carbon::today();
        Prescricao::factory()->create(['ativa' => true]);

        $this->registroMedicacaoService->gerarRegistrosDoDia($hoje);
        $this->registroMedicacaoService->gerarRegistrosDoDia($hoje);

        $this->assertSame(1, RegistroMedicacao::where('data', $hoje->toDateString())->count());
    }

    public function test_registros_gerados_nascem_pendentes_sem_usuario(): void
    {
        $hoje = Carbon::today();
        Prescricao::factory()->create(['ativa' => true]);

        $this->registroMedicacaoService->gerarRegistrosDoDia($hoje);

        $registro = RegistroMedicacao::where('data', $hoje->toDateString())->first();
        $this->assertFalse($registro->administrado);
        $this->assertNull($registro->usuario_id);
    }

    public function test_dia_esta_aberto_por_padrao_quando_nao_ha_diario_status(): void
    {
        $this->assertTrue($this->registroMedicacaoService->diaEstaAberto(Carbon::today()));
    }

    public function test_registrar_administracao_atualiza_o_registro_e_audita(): void
    {
        $enfermeira = User::factory()->create(['cargo' => 'enfermeiro']);
        $registro = RegistroMedicacao::factory()->create(['data' => Carbon::today(), 'administrado' => false]);

        $atualizado = $this->registroMedicacaoService->registrarAdministracao(
            $registro,
            true,
            'Paciente recusou inicialmente, administrado em seguida.',
            $enfermeira,
        );

        $this->assertTrue($atualizado->administrado);
        $this->assertSame($enfermeira->id, $atualizado->usuario_id);
        $this->assertDatabaseHas('logs_auditoria', [
            'acao' => 'registro_medicacao.atualizado',
            'usuario_id' => $enfermeira->id,
            'alvo_type' => RegistroMedicacao::class,
            'alvo_id' => $registro->id,
        ]);
    }

    public function test_registrar_administracao_lanca_excecao_quando_dia_encerrado(): void
    {
        $hoje = Carbon::today();
        $this->diarioService->encerrarDia($hoje);

        $registro = RegistroMedicacao::factory()->create(['data' => $hoje, 'administrado' => false]);
        $tecnico = User::factory()->create(['cargo' => 'tecnico']);

        $this->expectException(DiaEncerradoException::class);

        $this->registroMedicacaoService->registrarAdministracao($registro, true, null, $tecnico);
    }

    public function test_registro_nao_e_alterado_quando_dia_encerrado(): void
    {
        $hoje = Carbon::today();
        $registro = RegistroMedicacao::factory()->create(['data' => $hoje, 'administrado' => false]);
        $this->diarioService->encerrarDia($hoje);
        $tecnico = User::factory()->create(['cargo' => 'tecnico']);

        try {
            $this->registroMedicacaoService->registrarAdministracao($registro, true, 'tentativa bloqueada', $tecnico);
        } catch (DiaEncerradoException) {
            // esperado
        }

        $registro->refresh();
        $this->assertFalse($registro->administrado);
        $this->assertNull($registro->observacao);
        $this->assertNull($registro->usuario_id);
    }
}
