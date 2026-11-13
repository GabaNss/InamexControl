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

        app(\App\Services\AuditoriaService::class)->registrar(
            usuario: auth()->user(),
            acao: 'prescricao.inativada',
            alvo: $prescricao,
            dadosAntes: ['ativa' => true],
            dadosDepois: ['ativa' => false],
        );
    }

    public function excluirPrescricao(int $id): void
    {
        $prescricao = Prescricao::findOrFail($id);
        $this->authorize('delete', $prescricao);
        $dadosAntes = $prescricao->only(['paciente_id', 'medicamento_id', 'dose', 'horario', 'ativa']);
        $prescricao->delete();

        app(\App\Services\AuditoriaService::class)->registrar(
            usuario: auth()->user(),
            acao: 'prescricao.excluida',
            alvo: $prescricao,
            dadosAntes: $dadosAntes,
        );
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
