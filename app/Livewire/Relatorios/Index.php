<?php

namespace App\Livewire\Relatorios;

use App\Models\Paciente;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public ?int $pacienteId = null;

    public string $inicio;

    public string $fim;

    public function mount(): void
    {
        $this->authorize('gerar-relatorios');

        $this->inicio = Carbon::today()->startOfMonth()->toDateString();
        $this->fim = Carbon::today()->toDateString();
    }

    public function render(): View
    {
        $internas = Paciente::where('ativa', true)->orderBy('nome')->get(['id', 'nome', 'prontuario']);

        $pacienteSelecionado = $this->pacienteId
            ? Paciente::find($this->pacienteId)
            : null;

        return view('livewire.relatorios.index', [
            'internas' => $internas,
            'pacienteSelecionado' => $pacienteSelecionado,
        ]);
    }
}
