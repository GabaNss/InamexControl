<?php

namespace App\Livewire\Auditoria;

use App\Models\LogAuditoria;
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

    public string $acao = '';

    public string $inicio = '';

    public string $fim = '';

    public function mount(): void
    {
        $this->authorize('ver-auditoria');
    }

    public function updatingBusca(): void { $this->resetPage(); }
    public function updatingAcao(): void { $this->resetPage(); }
    public function updatingInicio(): void { $this->resetPage(); }
    public function updatingFim(): void { $this->resetPage(); }

    public function render(): View
    {
        $logs = LogAuditoria::with('usuario')
            ->when($this->busca, fn ($q) => $q->whereHas(
                'usuario', fn ($u) => $u->where('name', 'like', "%{$this->busca}%")
            ))
            ->when($this->acao, fn ($q) => $q->where('acao', 'like', "%{$this->acao}%"))
            ->when($this->inicio, fn ($q) => $q->whereDate('created_at', '>=', $this->inicio))
            ->when($this->fim, fn ($q) => $q->whereDate('created_at', '<=', $this->fim))
            ->orderByDesc('created_at')
            ->paginate(25);

        $acoes = LogAuditoria::select('acao')->distinct()->orderBy('acao')->pluck('acao');

        $usuarios = User::orderBy('name')->get(['id', 'name']);

        return view('livewire.auditoria.index', compact('logs', 'acoes', 'usuarios'));
    }
}
