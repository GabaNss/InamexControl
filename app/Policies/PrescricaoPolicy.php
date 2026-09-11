<?php

namespace App\Policies;

use App\Models\Prescricao;
use App\Models\User;

/**
 * Regras de acesso a prescricoes medicas.
 *
 * Apenas medico e admin podem criar/editar/inativar prescricoes (ato medico).
 * Diretor, enfermeiro e tecnico tem apenas leitura.
 */
class PrescricaoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->temCargoAtribuido();
    }

    public function view(User $user, Prescricao $prescricao): bool
    {
        return $user->temCargoAtribuido();
    }

    public function create(User $user): bool
    {
        return $user->temCargo('admin', 'medico');
    }

    public function update(User $user, Prescricao $prescricao): bool
    {
        return $user->temCargo('admin', 'medico');
    }

    public function delete(User $user, Prescricao $prescricao): bool
    {
        return $user->temCargo('admin', 'medico');
    }
}
