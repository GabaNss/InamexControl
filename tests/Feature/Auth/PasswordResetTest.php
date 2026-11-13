<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create(['recovery_email' => 'recupera@pessoal.com']);

        $this->post('/forgot-password', ['recovery_email' => $user->recovery_email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_link_is_delivered_to_recovery_email_not_login_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'login@inamex.com',
            'recovery_email' => 'pessoal@gmail.com',
        ]);

        $this->post('/forgot-password', ['recovery_email' => 'pessoal@gmail.com']);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification, $channels, $notifiable) use ($user) {
            // A notificacao e roteada para o recovery_email, nunca para o login.
            return $user->routeNotificationForMail($notification) === 'pessoal@gmail.com';
        });
    }

    public function test_request_with_unknown_recovery_email_sends_nothing_but_shows_generic_message(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', ['recovery_email' => 'naoexiste@lugar.com']);

        // Nao revela que o e-mail nao existe (anti-enumeracao).
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');
        Notification::assertNothingSent();
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create(['recovery_email' => 'recupera@pessoal.com']);

        $this->post('/forgot-password', ['recovery_email' => $user->recovery_email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create(['recovery_email' => 'recupera@pessoal.com']);

        $this->post('/forgot-password', ['recovery_email' => $user->recovery_email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            // O reset continua ancorado no e-mail de LOGIN (chave do token).
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
