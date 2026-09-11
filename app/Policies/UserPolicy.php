<?php

namespace App\Policies;

use App\Models\User;

/**
 * Regras de acesso a gestao de usuarios do sistema (criar conta, definir
 * cargo, ativar/desativar acesso).
 *
 * Admin e diretor podem gerenciar usuarios. Diretor nao pode
 * desativar a propria conta nem a conta de um admin.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->temCargo('admin', 'diretor');
    }

    public function view(User $user, User $alvo): bool
    {
        return $user->temCargo('admin', 'diretor');
    }

    public function create(User $user): bool
    {
        return $user->temCargo('admin', 'diretor');
    }

    public function update(User $user, User $alvo): bool
    {
        // Diretor nao pode editar contas de admin
        if ($user->temCargo('diretor') && $alvo->temCargo('admin')) {
            return false;
        }

        return $user->temCargo('admin', 'diretor');
    }

    /**
     * Nenhum usuario pode desativar a propria conta.
     * Diretor nao pode desativar contas de admin.
     */
    public function delete(User $user, User $alvo): bool
    {
        if ($user->is($alvo)) {
            return false;
        }

        if ($user->temCargo('diretor') && $alvo->temCargo('admin')) {
            return false;
        }

        return $user->temCargo('admin', 'diretor');
    }
}
