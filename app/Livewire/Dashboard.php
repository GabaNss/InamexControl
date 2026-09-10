<?php

namespace App\Livewire;

use App\Models\DiarioStatus;
use App\Models\Paciente;
use App\Models\Prescricao;
use App\Models\ProntuarioDiario;
use App\Models\RegistroMedicacao;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $hoje = Carbon::today()->toDateString();

        // --- Status do dia ---
        $diarioStatus = DiarioStatus::where('data', $hoje)->first();
        $diaAberto    = $diarioStatus ? !$diarioStatus->encerrado : false;

        // --- Internas ativas com prescrições ---
        $internasAtivas = Paciente::where('ativa', true)
            ->withCount(['prescricoes as prescricoes_ativas_count' => fn ($q) => $q->where('ativa', true)])
            ->orderBy('nome')
            ->get();

        $totalInternas      = $internasAtivas->count();
        $semPrescricao      = $internasAtivas->where('prescricoes_ativas_count', 0);
        $prescricoesAtivas  = Prescricao::where('ativa', true)->count();

        // --- Prontuários diários de hoje ---
        $prontuariosHoje = ProntuarioDiario::where('data', $hoje)
            ->pluck('paciente_id')
            ->flip()
            ->all();

        $semProntuarioHoje = $internasAtivas->filter(
            fn ($i) => !isset($prontuariosHoje[$i->id])
        );

        // --- Registros de medicação de hoje ---
        $registrosHoje = RegistroMedicacao::where('data', $hoje)
            ->with('prescricao:id,paciente_id')
            ->get();

        $totalHoje        = $registrosHoje->count();
        $administradoHoje = $registrosHoje->where('administrado', true)->count();

        // Por turno
        $manha      = $registrosHoje->where('turno', 'manha');
        $tarde      = $registrosHoje->where('turno', 'tarde');
        $totalManha = $manha->count();
        $admManha   = $manha->where('administrado', true)->count();
        $totalTarde = $tarde->count();
        $admTarde   = $tarde->where('administrado', true)->count();

        // Registros agrupados por paciente (para badges na lista)
        $registrosPorPaciente = $registrosHoje->groupBy('prescricao.paciente_id');

        // Internas com doses pendentes hoje
        $comDosesPendentes = $internasAtivas->filter(function ($interna) use ($registrosPorPaciente) {
            $regs = $registrosPorPaciente->get($interna->id, collect());
            return $regs->count() > 0 && $regs->where('administrado', false)->count() > 0;
        });

        // --- Feed de doses recentes ---
        $dosesRecentes = RegistroMedicacao::with([
            'prescricao.medicamento:id,nome',
            'prescricao.paciente:id,nome',
            'usuario:id,name',
        ])
            ->where('data', $hoje)
            ->where('administrado', true)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $usuario = auth()->user();

        return view('livewire.dashboard', compact(
            'usuario',
            'diaAberto',
            'totalInternas',
            'internasAtivas',
            'prescricoesAtivas',
            'totalHoje',
            'administradoHoje',
            'totalManha',
            'admManha',
            'totalTarde',
            'admTarde',
            'prontuariosHoje',
            'registrosPorPaciente',
            'semPrescricao',
            'semProntuarioHoje',
            'comDosesPendentes',
            'dosesRecentes',
        ));
    }
}
