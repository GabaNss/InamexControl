<?php

namespace App\Exports;

use App\Models\RegistroMedicacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Exportacao em Excel (.xlsx) dos registros de administracao de medicacao
 * de um periodo, para relatorios de auditoria/fiscalizacao (ex: vigilancia
 * sanitaria, conselho regional). Usado por App\Livewire\Relatorios\Index.
 */
class RegistrosMedicacaoExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Carbon $inicio,
        private readonly Carbon $fim,
    ) {}

    public function query(): Builder
    {
        return RegistroMedicacao::query()
            ->with(['prescricao.paciente', 'prescricao.medicamento', 'usuario'])
            ->whereBetween('data', [$this->inicio->toDateString(), $this->fim->toDateString()])
            ->orderBy('data')
            ->orderBy('turno');
    }

    public function headings(): array
    {
        return [
            'Paciente',
            'Prontuario',
            'Medicamento',
            'Dose',
            'Data',
            'Turno',
            'Administrado',
            'Observacao',
            'Responsavel',
        ];
    }

    public function map($registro): array
    {
        return [
            $registro->prescricao->paciente->nome,
            $registro->prescricao->paciente->prontuario,
            $registro->prescricao->medicamento->nome,
            $registro->prescricao->dose,
            $registro->data->format('d/m/Y'),
            ucfirst($registro->turno),
            $registro->administrado ? 'Sim' : 'Nao',
            $registro->observacao,
            $registro->usuario?->name,
        ];
    }
}
