<?php

namespace App\Livewire\Pacientes;

use App\Models\Paciente;
use App\Models\Prescricao;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Paciente $paciente;

    public function mount(Paciente $paciente): void
    {
        $this->authorize('view', $paciente);

        $this->paciente = $paciente;
    }

    public function desativarPrescricao(int $id): void
    {
        $prescricao = Prescricao::findOrFail($id);
        $this->authorize('update', $prescricao);
        $prescricao->update(['ativa' => false]);
    }

    public function excluirPrescricao(int $id): void
    {
        $prescricao = Prescricao::findOrFail($id);
        $this->authorize('delete', $prescricao);
        $prescricao->delete();
    }

    public function render(): View
    {
        $prescricoes = $this->paciente->prescricoes()
            ->with('medicamento', 'prescritor')
            ->orderByDesc('ativa')
            ->orderBy('horario')
            ->get();

        return view('livewire.pacientes.show', ['prescricoes' => $prescricoes]);
    }
}
