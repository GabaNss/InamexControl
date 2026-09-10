<?php

namespace App\Policies;

use App\Models\Paciente;
use App\Models\User;

/**
 * Regras de acesso ao cadastro de pacientes.
 *
 * admin, medico, diretor, enfermeiro e tecnico podem visualizar (todos
 * precisam ver o cadastro para localizar o paciente); apenas admin e diretor
 * podem criar/editar/excluir o cadastro (dados cadastrais, nao clinicos).
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
        return $user->temCargo('admin', 'diretor');
    }

    public function update(User $user, Paciente $paciente): bool
    {
        return $user->temCargo('admin', 'diretor');
    }

    public function delete(User $user, Paciente $paciente): bool
    {
        return $user->temCargo('admin', 'diretor');
    }
}
