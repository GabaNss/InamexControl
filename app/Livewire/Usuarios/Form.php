<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use App\Services\AuditoriaService;
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
            'turno_inicio' => ['nullable', 'date_format:H:i'],
            'turno_fim' => ['nullable', 'date_format:H:i'],
        ];

        // So exige senha na criacao, ou na edicao se o usuario digitou algo
        // (campo vazio na edicao = mantem a senha atual).
        if (! $this->user || $this->password !== '') {
            $rules['password'] = ['required', 'confirmed', 'min:8'];
        }

        $dados = $this->validate($rules);

        // Converte string vazia para null (campo de hora deixado em branco).
        $dados['turno_inicio'] = $dados['turno_inicio'] ?: null;
        $dados['turno_fim'] = $dados['turno_fim'] ?: null;

        unset($dados['password']);

        if ($this->password !== '') {
            $dados['password'] = Hash::make($this->password);
        }

        // Campos auditaveis — NUNCA inclui a senha (hash) na trilha de auditoria.
        $campos = ['name', 'email', 'cargo', 'ativo', 'turno_inicio', 'turno_fim'];

        if ($this->user) {
            $this->authorize('update', $this->user);
            $dadosAntes = $this->user->only($campos);
            $this->user->update($dados);

            app(AuditoriaService::class)->registrar(
                usuario: auth()->user(),
                acao: 'usuario.atualizado',
                alvo: $this->user,
                dadosAntes: $dadosAntes,
                dadosDepois: $this->user->only($campos),
            );
        } else {
            $this->authorize('create', User::class);
            $dados['must_change_password'] = true;
            $novoUsuario = User::create($dados);

            app(AuditoriaService::class)->registrar(
                usuario: auth()->user(),
                acao: 'usuario.criado',
                alvo: $novoUsuario,
                dadosDepois: $novoUsuario->only($campos),
            );
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
