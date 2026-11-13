<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recovery_email' => ['required', 'email'],
        ]);

        // Busca o usuario pelo e-mail de recuperacao (separado do login). O token
        // e armazenado/validado internamente pelo e-mail de login do usuario
        // (getEmailForPasswordReset) e a notificacao e entregue no recovery_email
        // (ver App\Models\User::routeNotificationForMail). sendResetLink mantem o
        // throttle nativo (1 pedido por 60s) contra abuso.
        Password::sendResetLink(['recovery_email' => $request->recovery_email]);

        // Resposta sempre generica: nao revela quais e-mails estao cadastrados
        // (evita enumeracao de usuarios).
        return back()->with('status', __('passwords.sent'));
    }
}
