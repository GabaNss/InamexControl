<?php

namespace App\Livewire\Internas;

use App\Models\Paciente;
use App\Models\ProntuarioHistorico as ModelProntuarioHistorico;
use App\Services\AuditoriaService;
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

        $dadosAntes = $existente?->only(['conteudo']);

        $historico = ModelProntuarioHistorico::updateOrCreate(
            ['paciente_id' => $this->pacienteId],
            ['conteudo' => $this->conteudo, 'atualizado_por' => auth()->id()],
        );

        app(AuditoriaService::class)->registrar(
            usuario: auth()->user(),
            acao: $existente ? 'prontuario_historico.atualizado' : 'prontuario_historico.criado',
            alvo: $historico,
            dadosAntes: $dadosAntes,
            dadosDepois: $historico->only(['conteudo']),
        );

        $this->editando = false;
        session()->flash('status-historico', 'Prontuário histórico salvo.');
    }

    public function deletar(): void
    {
        $historico = ModelProntuarioHistorico::where('paciente_id', $this->pacienteId)->first();
        if ($historico) {
            $this->authorize('delete', $historico);
            $dadosAntes = $historico->only(['conteudo']);
            $historico->delete();

            app(AuditoriaService::class)->registrar(
                usuario: auth()->user(),
                acao: 'prontuario_historico.excluido',
                alvo: $historico,
                dadosAntes: $dadosAntes,
            );
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
