<?php

namespace App\Services;

use App\Models\DiarioStatus;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Camada de regra de negocio para o ciclo de vida do "diario" (o dia
 * operacional do INAMEX). Responsavel por abrir, encerrar e consultar o
 * status de uma data.
 */
class DiarioService
{
    public function __construct(
        private readonly AuditoriaService $auditoriaService,
    ) {}

    /**
     * Garante que existe um DiarioStatus (aberto) para a data informada.
     * Chamado ao virar o dia, antes de gerar os RegistroMedicacao do dia.
     */
    public function abrirDia(Carbon $data): DiarioStatus
    {
        return DiarioStatus::firstOrCreate(
            ['data' => $data->toDateString()],
            ['encerrado' => false],
        );
    }

    /**
     * REGRA CRITICA: encerra o dia, tornando os RegistroMedicacao daquela
     * data imutaveis. Chamado automaticamente pelo
     * App\Console\Commands\EncerrarDiarioCommand a meia-noite, mas tambem
     * pode ser exposto a admin/diretor para encerramento manual excepcional
     * (nesse caso $usuario e informado, para a auditoria).
     */
    public function encerrarDia(Carbon $data, ?User $usuario = null): DiarioStatus
    {
        $status = DiarioStatus::firstOrCreate(
            ['data' => $data->toDateString()],
            ['encerrado' => false],
        );

        $status->update([
            'encerrado' => true,
            'encerrado_em' => now(),
        ]);

        $this->auditoriaService->registrar(
            usuario: $usuario,
            acao: 'diario.encerrado',
            alvo: $status,
            dadosDepois: ['data' => $data->toDateString(), 'encerrado' => true],
        );

        return $status;
    }

    /**
     * Desfaz um encerramento manual, reabrindo o dia para edicao. Exclusivo
     * para admin (uso excepcional em caso de fechamento acidental).
     */
    public function reabrirDia(Carbon $data, ?User $usuario = null): DiarioStatus
    {
        $status = DiarioStatus::where('data', $data->toDateString())->firstOrFail();

        $status->update([
            'encerrado' => false,
            'encerrado_em' => null,
        ]);

        $this->auditoriaService->registrar(
            usuario: $usuario,
            acao: 'diario.reaberto',
            alvo: $status,
            dadosDepois: ['data' => $data->toDateString(), 'encerrado' => false],
        );

        return $status;
    }

    /**
     * Atalho usado pelas Policies/Services para checar se uma data esta
     * encerrada (true) ou ainda aberta para edicao (false). Ausencia de
     * registro de DiarioStatus para a data significa dia ainda aberto.
     */
    public function estaEncerrado(Carbon $data): bool
    {
        return DiarioStatus::where('data', $data->toDateString())
            ->where('encerrado', true)
            ->exists();
    }
}
