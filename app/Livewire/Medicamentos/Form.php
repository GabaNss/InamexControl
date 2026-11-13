<?php

namespace App\Livewire\Medicamentos;

use App\Models\Medicamento;
use App\Services\AuditoriaService;
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

        $campos = ['nome', 'concentracao', 'via_administracao', 'ativo'];

        if ($this->medicamento) {
            $this->authorize('update', $this->medicamento);
            $dadosAntes = $this->medicamento->only($campos);
            $this->medicamento->update($dados);

            app(AuditoriaService::class)->registrar(
                usuario: auth()->user(),
                acao: 'medicamento.atualizado',
                alvo: $this->medicamento,
                dadosAntes: $dadosAntes,
                dadosDepois: $this->medicamento->only($campos),
            );
        } else {
            $this->authorize('create', Medicamento::class);
            $medicamento = Medicamento::create($dados);

            app(AuditoriaService::class)->registrar(
                usuario: auth()->user(),
                acao: 'medicamento.criado',
                alvo: $medicamento,
                dadosDepois: $medicamento->only($campos),
            );
        }

        session()->flash('status', 'Medicamento salvo com sucesso.');

        $this->redirectRoute('medicamentos.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.medicamentos.form');
    }
}
