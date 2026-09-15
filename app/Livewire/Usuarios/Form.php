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

    public string $turno_inicio = '';

    public string $turno_fim = '';

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
            $this->turno_inicio = $user->turno_inicio ?? '';
            $this->turno_fim = $user->turno_fim ?? '';
        } else {
            $this->authorize('create', User::class);
        }
    }

    public function salvar(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'cargo' => ['required', 'in:' . implode(',', $this->cargosPermitidos())],
            'ativo' => ['boolean'],
            'turno_inicio' => ['required', 'date_format:H:i'],
            'turno_fim' => ['required', 'date_format:H:i'],
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

    private function cargosPermitidos(): array
    {
        if (auth()->user()?->temCargo('admin')) {
            return User::CARGOS;
        }

        return array_values(array_diff(User::CARGOS, ['admin']));
    }

    public function render(): View
    {
        return view('livewire.usuarios.form', ['cargos' => $this->cargosPermitidos()]);
    }
}
