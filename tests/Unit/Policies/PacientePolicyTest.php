<?php

namespace Tests\Unit\Policies;

use App\Models\Paciente;
use App\Models\User;
use App\Policies\PacientePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacientePolicyTest extends TestCase
{
    use RefreshDatabase;

    private PacientePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new PacientePolicy();
    }

    public function test_pendente_nao_pode_ver_pacientes(): void
    {
        $pendente = User::factory()->create(['cargo' => 'pendente']);

        $this->assertFalse($this->policy->viewAny($pendente));
        $this->assertFalse($this->policy->view($pendente, Paciente::factory()->create()));
    }

    /**
     * @dataProvider cargosComAcesso
     */
    public function test_qualquer_cargo_real_pode_ver_pacientes(string $cargo): void
    {
        $usuario = User::factory()->create(['cargo' => $cargo]);

        $this->assertTrue($this->policy->viewAny($usuario));
        $this->assertTrue($this->policy->view($usuario, Paciente::factory()->create()));
    }

    /**
     * @dataProvider cargosSemGestaoCadastral
     */
    public function test_apenas_admin_e_diretor_podem_criar_editar_excluir(string $cargo): void
    {
        $usuario = User::factory()->create(['cargo' => $cargo]);
        $paciente = Paciente::factory()->create();

        $this->assertFalse($this->policy->create($usuario));
        $this->assertFalse($this->policy->update($usuario, $paciente));
        $this->assertFalse($this->policy->delete($usuario, $paciente));
    }

    public function test_admin_e_diretor_podem_criar_editar_excluir(): void
    {
        $paciente = Paciente::factory()->create();

        foreach (['admin', 'diretor'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);

            $this->assertTrue($this->policy->create($usuario));
            $this->assertTrue($this->policy->update($usuario, $paciente));
            $this->assertTrue($this->policy->delete($usuario, $paciente));
        }
    }

    public static function cargosComAcesso(): array
    {
        return [['admin'], ['diretor'], ['medico'], ['enfermeiro'], ['tecnico']];
    }

    public static function cargosSemGestaoCadastral(): array
    {
        return [['medico'], ['enfermeiro'], ['tecnico']];
    }
}
