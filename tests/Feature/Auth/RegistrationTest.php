<?php

namespace Tests\Feature\Auth;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_mas_nascem_sem_cargo(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $usuario = User::where('email', 'test@example.com')->first();
        $this->assertSame('pendente', $usuario->cargo);
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
