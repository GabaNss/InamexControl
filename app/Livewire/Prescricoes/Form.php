<?php

namespace App\Livewire\Prescricoes;

use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\Prescricao;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Prescricao $prescricao = null;

    public ?int $paciente_id = null;

    public ?int $medicamento_id = null;

    public string $dose = '';

    public string $horario = 'manha';

    public bool $ativa = true;

    public function mount(?Prescricao $prescricao = null): void
    {
        if ($prescricao?->exists) {
            $this->authorize('update', $prescricao);

            $this->prescricao = $prescricao;
            $this->paciente_id = $prescricao->paciente_id;
            $this->medicamento_id = $prescricao->medicamento_id;
            $this->dose = $prescricao->dose;
            $this->horario = $prescricao->horario;
            $this->ativa = $prescricao->ativa;
        } else {
            $this->authorize('create', Prescricao::class);

            // Vindo do botao "Nova prescricao" na tela do paciente
            // (ver resources/views/livewire/pacientes/show.blade.php) —
            // pre-seleciona o paciente em vez de deixar o dropdown vazio.
            $pacienteId = request()->integer('paciente_id');
            if ($pacienteId && Paciente::where('id', $pacienteId)->where('ativa', true)->exists()) {
                $this->paciente_id = $pacienteId;
            }
        }
    }

    public function salvar(): void
    {
        $dados = $this->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medicamento_id' => ['required', 'exists:medicamentos,id'],
            'dose' => ['required', 'string', 'max:255'],
            'horario' => ['required', 'in:manha,tarde'],
            'ativa' => ['boolean'],
        ]);

        if ($this->prescricao) {
            $this->authorize('update', $this->prescricao);
            $this->prescricao->update($dados);
        } else {
            $this->authorize('create', Prescricao::class);
            $dados['prescrito_por'] = auth()->id();
            Prescricao::create($dados);
        }

        session()->flash('status', 'Prescricao salva com sucesso.');

        $this->redirectRoute('prescricoes.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.prescricoes.form', [
            'pacientes' => Paciente::where('ativa', true)->orderBy('nome')->get(),
            'medicamentos' => Medicamento::where('ativo', true)->orderBy('nome')->get(),
        ]);
    }
}
