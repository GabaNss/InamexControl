<?php

namespace App\Policies;

use App\Models\Medicamento;
use App\Models\User;

/**
 * Regras de acesso ao catalogo de medicamentos.
 *
 * Todos os cargos clinicos podem visualizar; apenas admin e medico podem
 * criar/editar/excluir — diretor tem somente leitura.
 */
class MedicamentoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->temCargoAtribuido();
    }

    public function view(User $user, Medicamento $medicamento): bool
    {
        return $user->temCargoAtribuido();
    }

    public function create(User $user): bool
    {
        return $user->temCargo('admin', 'medico', 'chefe_enfermagem');
    }

    public function update(User $user, Medicamento $medicamento): bool
    {
        return $user->temCargo('admin', 'medico', 'chefe_enfermagem');
    }

    public function delete(User $user, Medicamento $medicamento): bool
    {
        return $user->temCargo('admin', 'medico', 'chefe_enfermagem');
    }
}
