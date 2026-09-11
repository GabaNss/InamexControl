<?php

namespace App\Policies;

use App\Models\Paciente;
use App\Models\User;

/**
 * Regras de acesso ao cadastro de pacientes.
 *
 * Todos os cargos clinicos podem visualizar. Admin e chefe de enfermagem
 * podem criar/editar/excluir — diretor tem somente leitura.
 */
class PacientePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->temCargoAtribuido();
    }

    public function view(User $user, Paciente $paciente): bool
    {
        return $user->temCargoAtribuido();
    }

    public function create(User $user): bool
    {
        return $user->temCargo('admin', 'chefe_enfermagem');
    }

    public function update(User $user, Paciente $paciente): bool
    {
        return $user->temCargo('admin', 'chefe_enfermagem');
    }

    public function delete(User $user, Paciente $paciente): bool
    {
        return $user->temCargo('admin', 'chefe_enfermagem');
    }
}
