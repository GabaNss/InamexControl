<?php

namespace App\Exports;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Exportacao em Excel (.xlsx) do cadastro de pacientes (lista administrativa).
 * Usado por App\Livewire\Relatorios\Index.
 */
class PacientesExport implements FromQuery, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return Paciente::query()->orderBy('nome');
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Prontuario',
            'Quarto',
            'Status',
        ];
    }

    public function map($paciente): array
    {
        return [
            $paciente->nome,
            $paciente->prontuario,
            $paciente->quarto,
            $paciente->ativa ? 'Ativo' : 'Inativo',
        ];
    }
}
