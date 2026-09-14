<?php

namespace Tests\Feature\Auth;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registro_publico_esta_desabilitado(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Qualquer',
            'email' => 'qualquer@exemplo.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertGuest();
    }

    public function test_usuario_pendente_nao_ve_nada_e_admin_atribuir_cargo_libera_acesso(): void
    {
        $pendente = User::factory()->create(['cargo' => 'pendente', 'ativo' => true]);

        $this->actingAs($pendente);
        $this->assertFalse($pendente->can('viewAny', Paciente::class));
        $this->get(route('pacientes.index'))->assertForbidden();

        $pendente->update(['cargo' => 'tecnico']);
        $pendente->refresh();

        $this->assertTrue($pendente->can('viewAny', Paciente::class));
        $this->get(route('pacientes.index'))->assertOk();
    }
}
