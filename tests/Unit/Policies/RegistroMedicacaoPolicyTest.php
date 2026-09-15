<?php

namespace Tests\Unit\Policies;

use App\Models\RegistroMedicacao;
use App\Models\User;
use App\Policies\RegistroMedicacaoPolicy;
use App\Services\AuditoriaService;
use App\Services\DiarioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RegistroMedicacaoPolicyTest extends TestCase
{
    use RefreshDatabase;

    private RegistroMedicacaoPolicy $policy;

    private DiarioService $diarioService;

    protected function setUp(): void
    {
        parent::setUp();

        // Resolvido pelo container para que o RegistroMedicacaoService
        // injetado na Policy seja o mesmo grafo de dependencias usado em produção.
        $this->policy = app(RegistroMedicacaoPolicy::class);
        $this->diarioService = app(DiarioService::class);
    }

    public function test_pendente_nao_pode_ver_registros(): void
    {
        $pendente = User::factory()->create(['cargo' => 'pendente']);

        $this->assertFalse($this->policy->viewAny($pendente));
    }

    public function test_equipe_clinica_pode_criar_registros_com_dia_aberto(): void
    {
        foreach (['admin', 'medico', 'enfermeiro', 'tecnico'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);
            $this->assertTrue($this->policy->create($usuario));
        }

        $diretor = User::factory()->create(['cargo' => 'diretor']);
        $this->assertFalse($this->policy->create($diretor));
    }

    public function test_ninguem_pode_criar_registro_com_dia_encerrado(): void
    {
        $this->diarioService->encerrarDia(Carbon::today());

        foreach (['admin', 'medico', 'enfermeiro', 'tecnico'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);
            $this->assertFalse($this->policy->create($usuario));
        }
    }

    public function test_update_e_permitido_com_dia_aberto(): void
    {
        $enfermeira = User::factory()->create(['cargo' => 'enfermeiro']);
        $registro = RegistroMedicacao::factory()->create(['data' => Carbon::today()]);

        $this->assertTrue($this->policy->update($enfermeira, $registro));
    }

    public function test_update_e_bloqueado_quando_dia_encerrado_mesmo_para_admin(): void
    {
        $hoje = Carbon::today();
        $admin = User::factory()->create(['cargo' => 'admin']);
        $registro = RegistroMedicacao::factory()->create(['data' => $hoje]);

        $this->diarioService->encerrarDia($hoje);

        $this->assertFalse($this->policy->update($admin, $registro));
    }

    public function test_delete_exige_admin_ou_diretor_e_dia_aberto(): void
    {
        $registro = RegistroMedicacao::factory()->create(['data' => Carbon::today()]);

        $enfermeira = User::factory()->create(['cargo' => 'enfermeiro']);
        $this->assertFalse($this->policy->delete($enfermeira, $registro));

        $admin = User::factory()->create(['cargo' => 'admin']);
        $this->assertTrue($this->policy->delete($admin, $registro));
    }

    public function test_delete_e_bloqueado_apos_encerramento_mesmo_para_admin(): void
    {
        $hoje = Carbon::today();
        $registro = RegistroMedicacao::factory()->create(['data' => $hoje]);
        $admin = User::factory()->create(['cargo' => 'admin']);

        $this->diarioService->encerrarDia($hoje);

        $this->assertFalse($this->policy->delete($admin, $registro));
    }

    public function test_nao_pode_criar_registro_fora_do_turno(): void
    {
        $agora = now();

        $foraDoTurno = User::factory()->create([
            'cargo' => 'enfermeiro',
            'turno_inicio' => $agora->copy()->addHour()->format('H:i:s'),
            'turno_fim' => $agora->copy()->addHours(2)->format('H:i:s'),
        ]);

        $this->assertFalse($this->policy->create($foraDoTurno));
    }

    public function test_pode_criar_registro_dentro_do_turno(): void
    {
        $agora = now();

        $noTurno = User::factory()->create([
            'cargo' => 'enfermeiro',
            'turno_inicio' => $agora->copy()->subHour()->format('H:i:s'),
            'turno_fim' => $agora->copy()->addHour()->format('H:i:s'),
        ]);

        $this->assertTrue($this->policy->create($noTurno));
    }

    public function test_admin_pode_criar_registro_independente_do_turno(): void
    {
        $admin = User::factory()->create([
            'cargo' => 'admin',
            'turno_inicio' => '00:00:00',
            'turno_fim' => '00:01:00',
        ]);

        $this->assertTrue($this->policy->create($admin));
    }
}
