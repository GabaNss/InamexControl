<?php

namespace App\Livewire\Internas;

use App\Exceptions\DiaEncerradoException;
use App\Models\RegistroMedicacao;
use App\Services\DiarioService;
use App\Services\RegistroMedicacaoService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class RegistrosPanel extends Component
{
    public int $pacienteId;

    public string $data;

    public function mount(int $pacienteId): void
    {
        $this->pacienteId = $pacienteId;
        $this->data = Carbon::today()->toDateString();
        $this->gerarPendentesSeHoje();
    }

    public function updatedData(): void
    {
        $this->gerarPendentesSeHoje();
    }

    private function gerarPendentesSeHoje(): void
    {
        if (Carbon::parse($this->data)->isToday()) {
            app(RegistroMedicacaoService::class)->gerarRegistrosDoDia(Carbon::today());
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
        } catch (DiaEncerradoException $e) {
            session()->flash('erro-registros', $e->getMessage());
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
            session()->flash('erro-registros', $e->getMessage());
        }
    }

    public function render(): View
    {
        $registros = RegistroMedicacao::where('data', $this->data)
            ->whereHas('prescricao', fn ($q) => $q->where('paciente_id', $this->pacienteId))
            ->with(['prescricao.medicamento', 'usuario'])
            ->get()
            ->groupBy('turno');

        $diaAberto = ! app(DiarioService::class)->estaEncerrado(Carbon::parse($this->data));

        return view('livewire.internas.registros-panel', [
            'registrosPorTurno' => $registros,
            'diaAberto' => $diaAberto,
        ]);
    }
}
