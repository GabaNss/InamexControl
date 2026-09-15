<?php

namespace App\Policies;

use App\Models\FichaMedica;
use App\Models\User;

class FichaMedicaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->temCargoAtribuido();
    }

    public function view(User $user, FichaMedica $fichaMedica): bool
    {
        return $user->temCargoAtribuido();
    }

    public function create(User $user): bool
    {
        return $user->temCargo('medico', 'chefe_enfermagem', 'admin') && $user->estaNoTurno();
    }

    public function update(User $user, FichaMedica $fichaMedica): bool
    {
        return $user->temCargo('medico', 'chefe_enfermagem', 'admin') && $user->estaNoTurno();
    }

    public function delete(User $user, FichaMedica $fichaMedica): bool
    {
        return $user->temCargo('admin', 'chefe_enfermagem') && $user->estaNoTurno();
    }
}
