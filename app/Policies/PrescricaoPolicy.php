<?php

namespace App\Policies;

use App\Models\Prescricao;
use App\Models\User;

/**
 * Regras de acesso a prescricoes medicas.
 *
 * Apenas medico, diretor e admin podem criar/editar/inativar prescricoes
 * (ato medico). Enfermeiro e tecnico tem apenas leitura, pois precisam ver a
 * prescricao para preencher o RegistroMedicacao.
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
        return $user->temCargo('admin', 'diretor', 'medico');
    }

    public function update(User $user, Prescricao $prescricao): bool
    {
        return $user->temCargo('admin', 'diretor', 'medico');
    }

    public function delete(User $user, Prescricao $prescricao): bool
    {
        return $user->temCargo('admin', 'diretor', 'medico');
    }
}
