<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new UserPolicy();
    }

    public function test_apenas_admin_e_diretor_podem_ver_a_lista_de_usuarios(): void
    {
        foreach (['medico', 'enfermeiro', 'tecnico', 'pendente'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);
            $this->assertFalse($this->policy->viewAny($usuario));
        }

        foreach (['admin', 'diretor'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);
            $this->assertTrue($this->policy->viewAny($usuario));
        }
    }

    public function test_diretor_nao_pode_criar_ou_editar_usuarios(): void
    {
        $diretor = User::factory()->create(['cargo' => 'diretor']);
        $alvo = User::factory()->create();

        $this->assertFalse($this->policy->create($diretor));
        $this->assertFalse($this->policy->update($diretor, $alvo));
        $this->assertFalse($this->policy->delete($diretor, $alvo));
    }

    public function test_admin_pode_criar_e_editar_usuarios(): void
    {
        $admin = User::factory()->create(['cargo' => 'admin']);
        $alvo = User::factory()->create();

        $this->assertTrue($this->policy->create($admin));
        $this->assertTrue($this->policy->update($admin, $alvo));
        $this->assertTrue($this->policy->delete($admin, $alvo));
    }

    public function test_admin_nao_pode_desativar_a_propria_conta(): void
    {
        $admin = User::factory()->create(['cargo' => 'admin']);

        $this->assertFalse($this->policy->delete($admin, $admin));
    }
}
