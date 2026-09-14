<?php

namespace App\Livewire\Internas;

use App\Models\Paciente;
use App\Models\ProntuarioHistorico as ModelProntuarioHistorico;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ProntuarioHistorico extends Component
{
    #[Locked]
    public int $pacienteId;

    public string $conteudo = '';

    public bool $editando = false;

    public function mount(int $pacienteId): void
    {
        $this->authorize('view', Paciente::findOrFail($pacienteId));
        $this->pacienteId = $pacienteId;
        $this->carregarConteudo();
    }

    public function carregarConteudo(): void
    {
        $historico = ModelProntuarioHistorico::where('paciente_id', $this->pacienteId)->first();
        $this->conteudo = $historico?->conteudo ?? '';
        $this->editando = false;
    }

    public function iniciarEdicao(): void
    {
        $this->editando = true;
    }

    public function cancelar(): void
    {
        $this->carregarConteudo();
    }

    public function salvar(): void
    {
        $this->validate(['conteudo' => ['required', 'string', 'max:65535']]);

        $existente = ModelProntuarioHistorico::where('paciente_id', $this->pacienteId)->first();

        if ($existente) {
            $this->authorize('update', $existente);
        } else {
            $this->authorize('create', ModelProntuarioHistorico::class);
        }

        ModelProntuarioHistorico::updateOrCreate(
            ['paciente_id' => $this->pacienteId],
            ['conteudo' => $this->conteudo, 'atualizado_por' => auth()->id()],
        );

        $this->editando = false;
        session()->flash('status-historico', 'Prontuário histórico salvo.');
    }

    public function deletar(): void
    {
        $historico = ModelProntuarioHistorico::where('paciente_id', $this->pacienteId)->first();
        if ($historico) {
            $this->authorize('delete', $historico);
            $historico->delete();
        }
        $this->carregarConteudo();
        session()->flash('status-historico', 'Prontuário histórico removido.');
    }

    public function render(): View
    {
        $historico = ModelProntuarioHistorico::where('paciente_id', $this->pacienteId)->first();

        return view('livewire.internas.prontuario-historico', compact('historico'));
    }
}
