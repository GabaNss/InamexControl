<?php

namespace App\Livewire\Internas;

use App\Models\FichaMedica as ModelFichaMedica;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FichaMedica extends Component
{
    public int $pacienteId;

    public string $laudos = '';

    public string $diagnosticos = '';

    public string $medicamentosCronicos = '';

    public bool $editando = false;

    public function mount(int $pacienteId): void
    {
        $this->pacienteId = $pacienteId;
        $this->carregarFicha();
    }

    public function carregarFicha(): void
    {
        $ficha = ModelFichaMedica::where('paciente_id', $this->pacienteId)->first();
        $this->laudos = $ficha?->laudos ?? '';
        $this->diagnosticos = $ficha?->diagnosticos ?? '';
        $this->medicamentosCronicos = $ficha?->medicamentos_cronicos ?? '';
        $this->editando = false;
    }

    public function iniciarEdicao(): void
    {
        $this->editando = true;
    }

    public function cancelar(): void
    {
        $this->carregarFicha();
    }

    public function salvar(): void
    {
        $this->validate([
            'laudos' => ['nullable', 'string', 'max:65535'],
            'diagnosticos' => ['nullable', 'string', 'max:65535'],
            'medicamentosCronicos' => ['nullable', 'string', 'max:65535'],
        ]);

        $existente = ModelFichaMedica::where('paciente_id', $this->pacienteId)->first();

        if ($existente) {
            $this->authorize('update', $existente);
        } else {
            $this->authorize('create', ModelFichaMedica::class);
        }

        ModelFichaMedica::updateOrCreate(
            ['paciente_id' => $this->pacienteId],
            [
                'laudos' => $this->laudos ?: null,
                'diagnosticos' => $this->diagnosticos ?: null,
                'medicamentos_cronicos' => $this->medicamentosCronicos ?: null,
                'atualizado_por' => auth()->id(),
            ],
        );

        $this->editando = false;
        session()->flash('status-ficha', 'Ficha médica salva.');
    }

    public function deletar(): void
    {
        $ficha = ModelFichaMedica::where('paciente_id', $this->pacienteId)->first();
        if ($ficha) {
            $this->authorize('delete', $ficha);
            $ficha->delete();
        }
        $this->carregarFicha();
        session()->flash('status-ficha', 'Ficha médica removida.');
    }

    public function render(): View
    {
        $ficha = ModelFichaMedica::where('paciente_id', $this->pacienteId)->first();

        return view('livewire.internas.ficha-medica', compact('ficha'));
    }
}
