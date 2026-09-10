<?php

namespace App\Livewire\Medicamentos;

use App\Models\Medicamento;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
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

    public function excluir(Medicamento $medicamento): void
    {
        $this->authorize('delete', $medicamento);

        try {
            $medicamento->delete();
            session()->flash('status', 'Medicamento removido.');
        } catch (QueryException) {
            session()->flash('erro', 'Este medicamento esta em uso em prescricoes e nao pode ser removido. Desative-o em vez disso.');
        }
    }

    public function render(): View
    {
        $this->authorize('viewAny', Medicamento::class);

        $medicamentos = Medicamento::query()
            ->when($this->busca, fn ($query) => $query->where('nome', 'like', "%{$this->busca}%"))
            ->orderBy('nome')
            ->paginate(15);

        return view('livewire.medicamentos.index', ['medicamentos' => $medicamentos]);
    }
}
