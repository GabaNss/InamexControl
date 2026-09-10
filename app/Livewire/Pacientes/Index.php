<?php

namespace App\Livewire\Pacientes;

use App\Models\Paciente;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $busca = '';

    public string $status = 'ativos';

    public function updatingBusca(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function excluir(Paciente $paciente): void
    {
        $this->authorize('delete', $paciente);

        $paciente->delete();

        session()->flash('status', 'Paciente removido.');
    }

    public function render(): View
    {
        $this->authorize('viewAny', Paciente::class);

        $pacientes = Paciente::query()
            ->when($this->busca, fn ($query) => $query->where(fn ($q) => $q
                ->where('nome', 'like', "%{$this->busca}%")
                ->orWhere('prontuario', 'like', "%{$this->busca}%")))
            ->when($this->status === 'ativos', fn ($query) => $query->where('ativa', true))
            ->when($this->status === 'inativos', fn ($query) => $query->where('ativa', false))
            ->orderBy('nome')
            ->paginate(15);

        return view('livewire.pacientes.index', ['pacientes' => $pacientes]);
    }
}
