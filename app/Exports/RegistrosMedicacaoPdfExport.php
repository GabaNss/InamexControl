<?php

namespace App\Exports;

use App\Models\RegistroMedicacao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

/**
 * Exportacao em PDF do relatorio de registros de medicacao de um periodo
 * (para impressao/arquivamento fisico no prontuario, conforme exigido pela
 * vigilancia sanitaria). Usado por App\Livewire\Relatorios\Index.
 */
class RegistrosMedicacaoPdfExport
{
    public function __construct(
        private readonly Carbon $inicio,
        private readonly Carbon $fim,
    ) {}

    public function gerar(): \Barryvdh\DomPDF\PDF
    {
        $registros = RegistroMedicacao::query()
            ->with(['prescricao.paciente', 'prescricao.medicamento', 'usuario'])
            ->whereBetween('data', [$this->inicio->toDateString(), $this->fim->toDateString()])
            ->orderBy('data')
            ->orderBy('turno')
            ->get();

        return Pdf::loadView('relatorios.registros-pdf', [
            'registros' => $registros,
            'inicio' => $this->inicio,
            'fim' => $this->fim,
        ]);
    }
}
