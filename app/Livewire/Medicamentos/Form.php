<?php

namespace App\Livewire\Medicamentos;

use App\Models\Medicamento;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Medicamento $medicamento = null;

    public string $nome = '';

    public string $concentracao = '';

    public string $via_administracao = '';

    public bool $ativo = true;

    public function mount(?Medicamento $medicamento = null): void
    {
        if ($medicamento?->exists) {
            $this->authorize('update', $medicamento);

            $this->medicamento = $medicamento;
            $this->nome = $medicamento->nome;
            $this->concentracao = $medicamento->concentracao;
            $this->via_administracao = $medicamento->via_administracao;
            $this->ativo = $medicamento->ativo;
        } else {
            $this->authorize('create', Medicamento::class);
        }
    }

    public function salvar(): void
    {
        $dados = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'concentracao' => ['required', 'string', 'max:100'],
            'via_administracao' => ['required', 'string', 'max:100'],
            'ativo' => ['boolean'],
        ]);

        if ($this->medicamento) {
            $this->authorize('update', $this->medicamento);
            $this->medicamento->update($dados);
        } else {
            $this->authorize('create', Medicamento::class);
            Medicamento::create($dados);
        }

        session()->flash('status', 'Medicamento salvo com sucesso.');

        $this->redirectRoute('medicamentos.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.medicamentos.form');
    }
}
