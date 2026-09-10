<?php

namespace App\Livewire\Prescricoes;

use App\Models\Prescricao;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $busca = '';

    public string $status = 'ativas';

    public function updatingBusca(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Inativa em vez de excluir: o historico de RegistroMedicacao gerado a
     * partir desta prescricao precisa ser preservado (e a FK e restrictOnDelete).
     */
    public function inativar(Prescricao $prescricao): void
    {
        $this->authorize('delete', $prescricao);

        $prescricao->update(['ativa' => false]);

        session()->flash('status', 'Prescricao inativada.');
    }

    public function render(): View
    {
        $this->authorize('viewAny', Prescricao::class);

        $prescricoes = Prescricao::query()
            ->with(['paciente', 'medicamento'])
            ->when($this->busca, fn ($query) => $query->whereHas('paciente', fn ($q) => $q
                ->where('nome', 'like', "%{$this->busca}%")))
            ->when($this->status === 'ativas', fn ($query) => $query->where('ativa', true))
            ->when($this->status === 'inativas', fn ($query) => $query->where('ativa', false))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.prescricoes.index', ['prescricoes' => $prescricoes]);
    }
}
