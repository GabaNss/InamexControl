<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $cargo = 'tecnico';

    public bool $ativo = true;

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(?User $user = null): void
    {
        if ($user?->exists) {
            $this->authorize('update', $user);

            $this->user = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->cargo = $user->cargo;
            $this->ativo = $user->ativo;
        } else {
            $this->authorize('create', User::class);
        }
    }

    public function salvar(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'cargo' => ['required', 'in:' . implode(',', User::CARGOS)],
            'ativo' => ['boolean'],
        ];

        // So exige senha na criacao, ou na edicao se o usuario digitou algo
        // (campo vazio na edicao = mantem a senha atual).
        if (! $this->user || $this->password !== '') {
            $rules['password'] = ['required', 'confirmed', 'min:8'];
        }

        $dados = $this->validate($rules);

        unset($dados['password']);

        if ($this->password !== '') {
            $dados['password'] = Hash::make($this->password);
        }

        if ($this->user) {
            $this->authorize('update', $this->user);
            $this->user->update($dados);
        } else {
            $this->authorize('create', User::class);
            User::create($dados);
        }

        session()->flash('status', 'Usuario salvo com sucesso.');

        $this->redirectRoute('usuarios.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.usuarios.form', ['cargos' => User::CARGOS]);
    }
}
