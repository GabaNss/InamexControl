<?php

namespace App\Policies;

use App\Models\ProntuarioHistorico;
use App\Models\User;

class ProntuarioHistoricoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->temCargoAtribuido();
    }

    public function view(User $user, ProntuarioHistorico $prontuario): bool
    {
        return $user->temCargoAtribuido();
    }

    public function create(User $user): bool
    {
        return $user->temCargo('chefe_enfermagem', 'admin', 'diretor');
    }

    public function update(User $user, ProntuarioHistorico $prontuario): bool
    {
        return $user->temCargo('chefe_enfermagem', 'admin', 'diretor');
    }

    public function delete(User $user, ProntuarioHistorico $prontuario): bool
    {
        return $user->temCargo('admin', 'diretor');
    }
}
