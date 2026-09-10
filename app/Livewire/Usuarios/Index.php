<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $busca = '';

    public function updatingBusca(): void
    {
        $this->resetPage();
    }

    /**
     * Desativa o acesso (nao remove fisicamente, para preservar a trilha de auditoria).
     */
    public function desativar(User $user): void
    {
        $this->authorize('delete', $user);

        $user->update(['ativo' => false]);

        session()->flash('status', 'Usuario desativado.');
    }

    public function reativar(User $user): void
    {
        $this->authorize('update', $user);

        $user->update(['ativo' => true]);

        session()->flash('status', 'Usuario reativado.');
    }

    public function render(): View
    {
        $this->authorize('viewAny', User::class);

        $usuarios = User::query()
            ->when($this->busca, fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$this->busca}%")
                ->orWhere('email', 'like', "%{$this->busca}%")))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.usuarios.index', ['usuarios' => $usuarios]);
    }
}
