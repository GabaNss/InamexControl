<?php

namespace Tests\Unit\Policies;

use App\Models\Prescricao;
use App\Models\User;
use App\Policies\PrescricaoPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescricaoPolicyTest extends TestCase
{
    use RefreshDatabase;

    private PrescricaoPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new PrescricaoPolicy();
    }

    public function test_pendente_nao_pode_ver_prescricoes(): void
    {
        $pendente = User::factory()->create(['cargo' => 'pendente']);

        $this->assertFalse($this->policy->viewAny($pendente));
    }

    public function test_enfermeiro_e_tecnico_tem_apenas_leitura(): void
    {
        $prescricao = Prescricao::factory()->create();

        foreach (['enfermeiro', 'tecnico'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);

            $this->assertTrue($this->policy->viewAny($usuario));
            $this->assertFalse($this->policy->create($usuario));
            $this->assertFalse($this->policy->update($usuario, $prescricao));
            $this->assertFalse($this->policy->delete($usuario, $prescricao));
        }
    }

    public function test_medico_diretor_e_admin_podem_prescrever(): void
    {
        $prescricao = Prescricao::factory()->create();

        foreach (['medico', 'diretor', 'admin'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);

            $this->assertTrue($this->policy->create($usuario));
            $this->assertTrue($this->policy->update($usuario, $prescricao));
            $this->assertTrue($this->policy->delete($usuario, $prescricao));
        }
    }
}
