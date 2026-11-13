<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class PrimeiroAcesso extends Component
{
    public string $recovery_email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function salvar(): void
    {
        $this->validate([
            'recovery_email' => [
                'required',
                'email',
                'max:255',
                // Garante unicidade mas ignora o próprio usuário (caso reenvie o form).
                \Illuminate\Validation\Rule::unique('users', 'recovery_email')->ignore(auth()->id()),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'recovery_email.unique' => 'Este e-mail de recuperação já está em uso por outro usuário.',
        ]);

        auth()->user()->update([
            'recovery_email'      => $this->recovery_email,
            'password'            => Hash::make($this->password),
            'must_change_password' => false,
        ]);

        session()->flash('status', 'Senha definida com sucesso. Bem-vindo!');

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.primeiro-acesso');
    }
}
