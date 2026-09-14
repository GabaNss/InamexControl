<?php

namespace Tests\Unit\Services;

use App\Models\DiarioStatus;
use App\Models\User;
use App\Services\AuditoriaService;
use App\Services\DiarioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DiarioServiceTest extends TestCase
{
    use RefreshDatabase;

    private DiarioService $diarioService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->diarioService = new DiarioService(new AuditoriaService());
    }

    public function test_esta_encerrado_e_falso_quando_nao_ha_status_para_a_data(): void
    {
        $this->assertFalse($this->diarioService->estaEncerrado(Carbon::today()));
    }

    public function test_abrir_dia_cria_status_aberto_sem_duplicar(): void
    {
        $hoje = Carbon::today();

        $this->diarioService->abrirDia($hoje);
        $this->diarioService->abrirDia($hoje);

        $this->assertSame(1, DiarioStatus::where('data', $hoje->toDateString())->count());
        $this->assertFalse(DiarioStatus::where('data', $hoje->toDateString())->first()->encerrado);
    }

    public function test_encerrar_dia_marca_como_encerrado_e_registra_auditoria(): void
    {
        $hoje = Carbon::today();
        $diretor = User::factory()->create(['cargo' => 'diretor']);

        $status = $this->diarioService->encerrarDia($hoje, $diretor);

        $this->assertTrue($status->encerrado);
        $this->assertNotNull($status->encerrado_em);
        $this->assertTrue($this->diarioService->estaEncerrado($hoje));

        $this->assertDatabaseHas('logs_auditoria', [
            'usuario_id' => $diretor->id,
            'acao' => 'diario.encerrado',
            'alvo_type' => DiarioStatus::class,
            'alvo_id' => $status->id,
        ]);
    }

    public function test_encerrar_dia_funciona_mesmo_sem_status_previamente_aberto(): void
    {
        $hoje = Carbon::today();

        $status = $this->diarioService->encerrarDia($hoje);

        $this->assertTrue($status->encerrado);
    }

    public function test_reabrir_dia_desfaz_encerramento_e_registra_auditoria(): void
    {
        $hoje = Carbon::today();
        $admin = User::factory()->create(['cargo' => 'admin']);

        $this->diarioService->encerrarDia($hoje);
        $this->assertTrue($this->diarioService->estaEncerrado($hoje));

        $status = $this->diarioService->reabrirDia($hoje, $admin);

        $this->assertFalse($status->encerrado);
        $this->assertNull($status->encerrado_em);
        $this->assertFalse($this->diarioService->estaEncerrado($hoje));

        $this->assertDatabaseHas('logs_auditoria', [
            'usuario_id' => $admin->id,
            'acao' => 'diario.reaberto',
            'alvo_type' => DiarioStatus::class,
            'alvo_id' => $status->id,
        ]);
    }

    public function test_reabrir_dia_inexistente_lanca_excecao(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->diarioService->reabrirDia(Carbon::today());
    }
}
