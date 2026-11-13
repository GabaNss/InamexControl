<?php

namespace App\Livewire\Pacientes;

use App\Models\Paciente;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Form extends Component
{
    use WithFileUploads;

    public ?Paciente $paciente = null;

    public string $nome = '';

    public string $prontuario = '';

    public string $quarto = '';

    public bool $ativa = true;

    /** Novo upload de foto (substitui a foto atual, se enviado). */
    public $foto = null;

    public function mount(?Paciente $paciente = null): void
    {
        if ($paciente?->exists) {
            $this->authorize('update', $paciente);

            $this->paciente = $paciente;
            $this->nome = $paciente->nome;
            $this->prontuario = $paciente->prontuario;
            $this->quarto = (string) $paciente->quarto;
            $this->ativa = $paciente->ativa;
        } else {
            $this->authorize('create', Paciente::class);
        }
    }

    public function salvar(): void
    {
        $dados = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'prontuario' => ['required', 'string', 'max:50', Rule::unique('pacientes', 'prontuario')->ignore($this->paciente?->id)],
            'quarto' => ['nullable', 'string', 'max:50'],
            'ativa' => ['boolean'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ]);

        unset($dados['foto']);

        if ($this->foto) {
            // Disco 'local' = storage/app/private, fora do webroot. A foto (dado
            // pessoal sensivel) so e servida pela rota autenticada pacientes.foto.
            $dados['foto'] = $this->foto->store('pacientes', 'local');
        }

        if ($this->paciente) {
            $this->authorize('update', $this->paciente);
            $fotoAntiga = $this->paciente->foto;
            $this->paciente->update($dados);
            if ($this->foto && $fotoAntiga) {
                Storage::disk('local')->delete($fotoAntiga);
            }
        } else {
            $this->authorize('create', Paciente::class);
            Paciente::create($dados);
        }

        session()->flash('status', 'Paciente salvo com sucesso.');

        $this->redirectRoute('pacientes.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pacientes.form');
    }
}
