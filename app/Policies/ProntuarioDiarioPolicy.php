<?php

namespace App\Policies;

use App\Models\ProntuarioDiario;
use App\Models\User;

class ProntuarioDiarioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->temCargoAtribuido();
    }

    public function view(User $user, ProntuarioDiario $prontuario): bool
    {
        return $user->temCargoAtribuido();
    }

    public function create(User $user): bool
    {
        return $user->temCargo('enfermeiro', 'chefe_enfermagem', 'admin', 'diretor');
    }

    public function update(User $user, ProntuarioDiario $prontuario): bool
    {
        if ($prontuario->estaEncerrado()) {
            return false;
        }

        return $user->temCargo('enfermeiro', 'chefe_enfermagem', 'admin', 'diretor');
    }

    public function delete(User $user, ProntuarioDiario $prontuario): bool
    {
        return $user->temCargo('admin', 'diretor');
    }
}
