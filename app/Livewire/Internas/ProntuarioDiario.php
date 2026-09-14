<?php

namespace App\Livewire\Internas;

use App\Models\DiarioStatus;
use App\Models\Paciente;
use App\Models\ProntuarioDiario as ModelProntuarioDiario;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ProntuarioDiario extends Component
{
    #[Locked]
    public int $pacienteId;

    public string $dataSelecionada;

    public string $conteudo = '';

    public bool $editando = false;

    public bool $diaEncerrado = false;

    public function mount(int $pacienteId): void
    {
        $this->authorize('view', Paciente::findOrFail($pacienteId));
        $this->pacienteId = $pacienteId;
        $this->dataSelecionada = Carbon::today()->toDateString();
        $this->carregarDia();
    }

    public function carregarDia(): void
    {
        $prontuario = ModelProntuarioDiario::where('paciente_id', $this->pacienteId)
            ->where('data', $this->dataSelecionada)
            ->first();

        $this->conteudo = $prontuario?->conteudo ?? '';
        $this->editando = false;
        $this->diaEncerrado = (bool) DiarioStatus::where('data', $this->dataSelecionada)
            ->value('encerrado');
    }

    public function selecionarData(string $data): void
    {
        $this->dataSelecionada = $data;
        $this->carregarDia();
    }

    public function iniciarEdicao(): void
    {
        $this->editando = true;
    }

    public function cancelar(): void
    {
        $this->carregarDia();
    }

    public function deletar(): void
    {
        $prontuario = ModelProntuarioDiario::where('paciente_id', $this->pacienteId)
            ->where('data', $this->dataSelecionada)
            ->first();

        if ($prontuario) {
            $this->authorize('delete', $prontuario);
            $prontuario->delete();
        }

        $this->carregarDia();
        session()->flash('status-diario', 'Registro do dia removido.');
    }

    public function salvar(): void
    {
        $this->validate(['conteudo' => ['required', 'string', 'max:65535']]);

        $existente = ModelProntuarioDiario::where('paciente_id', $this->pacienteId)
            ->where('data', $this->dataSelecionada)
            ->first();

        if ($existente) {
            $this->authorize('update', $existente);
        } else {
            $this->authorize('create', ModelProntuarioDiario::class);
        }

        ModelProntuarioDiario::updateOrCreate(
            ['paciente_id' => $this->pacienteId, 'data' => $this->dataSelecionada],
            ['conteudo' => $this->conteudo, 'atualizado_por' => auth()->id()],
        );

        $this->editando = false;
        session()->flash('status-diario', 'Prontuário diário salvo.');
    }

    public function render(): View
    {
        // Datas dos últimos 14 dias que têm conteúdo (query única)
        $datasComConteudo = ModelProntuarioDiario::where('paciente_id', $this->pacienteId)
            ->whereBetween('data', [
                Carbon::today()->subDays(13)->toDateString(),
                Carbon::today()->toDateString(),
            ])
            ->pluck('data')
            ->map(fn ($d) => $d->toDateString())
            ->flip()
            ->all();

        $diasRecentes = collect(range(13, 0))->map(fn ($ago) => [
            'data'        => Carbon::today()->subDays($ago)->toDateString(),
            'label'       => Carbon::today()->subDays($ago)->format('d/m'),
            'diaSemana'   => Carbon::today()->subDays($ago)->locale('pt_BR')->isoFormat('ddd'),
            'temConteudo' => isset($datasComConteudo[Carbon::today()->subDays($ago)->toDateString()]),
            'isHoje'      => $ago === 0,
        ]);

        $ultimaEdicao = ModelProntuarioDiario::where('paciente_id', $this->pacienteId)
            ->where('data', $this->dataSelecionada)
            ->with('atualizadoPor')
            ->first();

        return view('livewire.internas.prontuario-diario', [
            'diasRecentes' => $diasRecentes,
            'ultimaEdicao' => $ultimaEdicao,
        ]);
    }
}
