<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_name_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
    }

    public function test_login_email_cannot_be_changed_via_profile(): void
    {
        $user = User::factory()->create(['email' => 'original@inamex.com']);

        $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'hacker@evil.com',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        // O e-mail de login permanece inalterado, mesmo tendo sido enviado no POST.
        $this->assertSame('original@inamex.com', $user->refresh()->email);
    }

    public function test_account_self_deletion_route_does_not_exist(): void
    {
        // Sistema clinico: o usuario nao pode excluir a propria conta. Contas sao
        // apenas desativadas pelo admin, preservando a trilha de auditoria (LGPD).
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('profile.destroy'));

        $user = User::factory()->create(['ativo' => true]);

        $this
            ->actingAs($user)
            ->delete('/profile', ['password' => 'password'])
            ->assertStatus(405); // Method Not Allowed: so GET e PATCH existem em /profile

        $this->assertNotNull($user->fresh());
    }
}
