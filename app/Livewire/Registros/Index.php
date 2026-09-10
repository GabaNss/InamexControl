<?php

namespace App\Livewire\Registros;

use App\Exceptions\DiaEncerradoException;
use App\Models\RegistroMedicacao;
use App\Services\DiarioService;
use App\Services\RegistroMedicacaoService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Tela operacional do dia: lista os RegistroMedicacao da data selecionada
 * (manha/tarde) para a equipe marcar administrado/nao administrado.
 * Toda escrita passa por RegistroMedicacaoService para respeitar a regra de
 * imutabilidade apos o encerramento do dia.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    public string $data;

    public function mount(): void
    {
        $this->authorize('viewAny', RegistroMedicacao::class);

        $this->data = Carbon::today()->toDateString();

        $this->gerarPendentesSeHoje();
    }

    public function updatedData(): void
    {
        $this->gerarPendentesSeHoje();
    }

    /**
     * So gera registros pendentes automaticamente para o dia de hoje — nao
     * fabrica registros retroativos ao navegar para datas passadas.
     */
    private function gerarPendentesSeHoje(): void
    {
        $data = Carbon::parse($this->data);

        if ($data->isToday()) {
            app(RegistroMedicacaoService::class)->gerarRegistrosDoDia($data);
        }
    }

    public function marcar(int $registroId, bool $administrado): void
    {
        $registro = RegistroMedicacao::findOrFail($registroId);

        $this->authorize('update', $registro);

        try {
            app(RegistroMedicacaoService::class)->registrarAdministracao(
                registro: $registro,
                administrado: $administrado,
                observacao: $registro->observacao,
                usuario: auth()->user(),
            );

            session()->flash('status', 'Registro atualizado.');
        } catch (DiaEncerradoException $e) {
            session()->flash('erro', $e->getMessage());
        }
    }

    public function atualizarObservacao(int $registroId, string $observacao): void
    {
        $registro = RegistroMedicacao::findOrFail($registroId);

        $this->authorize('update', $registro);

        try {
            app(RegistroMedicacaoService::class)->registrarAdministracao(
                registro: $registro,
                administrado: $registro->administrado,
                observacao: $observacao,
                usuario: auth()->user(),
            );
        } catch (DiaEncerradoException $e) {
            session()->flash('erro', $e->getMessage());
        }
    }

    public function render(): View
    {
        $registros = RegistroMedicacao::query()
            ->where('data', $this->data)
            ->with(['prescricao.paciente', 'prescricao.medicamento', 'usuario'])
            ->get()
            ->groupBy('turno');

        $diaAberto = app(DiarioService::class)->estaEncerrado(Carbon::parse($this->data)) === false;

        return view('livewire.registros.index', [
            'registrosPorTurno' => $registros,
            'diaAberto' => $diaAberto,
        ]);
    }
}
