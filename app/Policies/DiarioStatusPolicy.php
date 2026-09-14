<?php

namespace App\Policies;

use App\Models\DiarioStatus;
use App\Models\User;

/**
 * Regras de acesso ao status de encerramento do diario.
 *
 * Qualquer cargo clinico pode visualizar se o dia esta aberto/encerrado.
 * Apenas admin pode forcar um encerramento ou reabertura manual
 * (excepcional — o fluxo normal e automatico, ver
 * App\Console\Commands\EncerrarDiarioCommand).
 */
class DiarioStatusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->temCargoAtribuido();
    }

    public function view(User $user, DiarioStatus $diarioStatus): bool
    {
        return $user->temCargoAtribuido();
    }

    public function update(User $user, DiarioStatus $diarioStatus): bool
    {
        return $user->temCargo('admin');
    }
}
