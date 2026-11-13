<?php

namespace Tests\Feature\Auth;

use App\Livewire\PrimeiroAcesso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PrimeiroAcessoTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_com_senha_provisoria_e_redirecionado_para_primeiro_acesso(): void
    {
        $user = User::factory()->create(['must_change_password' => true, 'cargo' => 'tecnico']);

        $this->actingAs($user);

        // Qualquer rota autenticada redireciona para a tela de primeiro acesso.
        $this->get(route('dashboard'))->assertRedirect(route('primeiro-acesso'));
        $this->get(route('pacientes.index'))->assertRedirect(route('primeiro-acesso'));
    }

    public function test_usuario_pode_acessar_a_propria_tela_de_primeiro_acesso(): void
    {
        $user = User::factory()->create(['must_change_password' => true, 'cargo' => 'tecnico']);

        $this->actingAs($user);

        $this->get(route('primeiro-acesso'))->assertOk();
    }

    public function test_pode_sair_mesmo_com_senha_provisoria(): void
    {
        $user = User::factory()->create(['must_change_password' => true, 'cargo' => 'tecnico']);

        $this->actingAs($user);

        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_definir_senha_e_recovery_email_libera_o_acesso(): void
    {
        $user = User::factory()->create([
            'must_change_password' => true,
            'cargo' => 'tecnico',
            'recovery_email' => null,
        ]);

        $this->actingAs($user);

        Livewire::test(PrimeiroAcesso::class)
            ->set('recovery_email', 'pessoal@gmail.com')
            ->set('password', 'nova-senha-forte')
            ->set('password_confirmation', 'nova-senha-forte')
            ->call('salvar')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $user->refresh();

        $this->assertFalse($user->must_change_password);
        $this->assertSame('pessoal@gmail.com', $user->recovery_email);
        $this->assertTrue(Hash::check('nova-senha-forte', $user->password));

        // Agora navega normalmente.
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_recovery_email_precisa_ser_unico(): void
    {
        User::factory()->create(['recovery_email' => 'ocupado@gmail.com']);

        $user = User::factory()->create(['must_change_password' => true, 'cargo' => 'tecnico']);
        $this->actingAs($user);

        Livewire::test(PrimeiroAcesso::class)
            ->set('recovery_email', 'ocupado@gmail.com')
            ->set('password', 'nova-senha-forte')
            ->set('password_confirmation', 'nova-senha-forte')
            ->call('salvar')
            ->assertHasErrors('recovery_email');

        $user->refresh();
        $this->assertTrue($user->must_change_password);
    }

    public function test_senha_precisa_ser_confirmada(): void
    {
        $user = User::factory()->create(['must_change_password' => true, 'cargo' => 'tecnico']);
        $this->actingAs($user);

        Livewire::test(PrimeiroAcesso::class)
            ->set('recovery_email', 'pessoal@gmail.com')
            ->set('password', 'nova-senha-forte')
            ->set('password_confirmation', 'diferente')
            ->call('salvar')
            ->assertHasErrors('password');
    }

    public function test_endpoint_do_livewire_nao_e_redirecionado_pelo_middleware(): void
    {
        // Regressao: a tela de primeiro acesso e um componente Livewire. Se o
        // middleware redirecionar o POST /livewire/update, o envio do formulario
        // quebra. O middleware deve deixar o endpoint do Livewire passar.
        $user = User::factory()->create(['must_change_password' => true, 'cargo' => 'tecnico']);
        $this->actingAs($user);

        $updateUrl = app(\Livewire\Mechanisms\HandleRequests\HandleRequests::class)->getUpdateUri();

        // Payload invalido (sem snapshot) -> Livewire responde com erro, mas o
        // que importa e NAO ter sido redirecionado para primeiro-acesso pelo middleware.
        $response = $this->post($updateUrl, []);

        $this->assertFalse(
            $response->isRedirect(route('primeiro-acesso')),
            'O middleware nao deve redirecionar o endpoint do Livewire.'
        );
    }

    public function test_admin_sem_flag_nao_e_afetado(): void
    {
        $admin = User::factory()->create(['must_change_password' => false, 'cargo' => 'admin']);

        $this->actingAs($admin);

        $this->get(route('dashboard'))->assertOk();
    }
}
