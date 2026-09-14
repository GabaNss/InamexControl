<?php

namespace Tests\Unit\Policies;

use App\Models\DiarioStatus;
use App\Models\User;
use App\Policies\DiarioStatusPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiarioStatusPolicyTest extends TestCase
{
    use RefreshDatabase;

    private DiarioStatusPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new DiarioStatusPolicy();
    }

    public function test_pendente_nao_pode_ver_o_diario(): void
    {
        $pendente = User::factory()->create(['cargo' => 'pendente']);

        $this->assertFalse($this->policy->viewAny($pendente));
    }

    public function test_qualquer_cargo_clinico_pode_ver_o_diario(): void
    {
        foreach (['admin', 'diretor', 'medico', 'enfermeiro', 'tecnico'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);
            $this->assertTrue($this->policy->viewAny($usuario));
        }
    }

    public function test_apenas_admin_pode_encerrar_ou_reabrir_manualmente(): void
    {
        $status = DiarioStatus::factory()->create();

        foreach (['medico', 'enfermeiro', 'tecnico', 'diretor'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);
            $this->assertFalse($this->policy->update($usuario, $status));
        }

        $admin = User::factory()->create(['cargo' => 'admin']);
        $this->assertTrue($this->policy->update($admin, $status));
    }
}
