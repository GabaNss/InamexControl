<?php

namespace App\Policies;

use App\Models\User;

/**
 * Regras de acesso a gestao de usuarios do sistema (criar conta, definir
 * cargo, ativar/desativar acesso).
 *
 * Apenas admin pode gerenciar usuarios. Diretor pode visualizar a lista, mas
 * nao alterar/criar/excluir.
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
        return $user->temCargo('admin');
    }

    public function update(User $user, User $alvo): bool
    {
        return $user->temCargo('admin');
    }

    /**
     * Admin nao pode desativar a propria conta (evita ficar sem acesso ao sistema).
     */
    public function delete(User $user, User $alvo): bool
    {
        return $user->temCargo('admin') && $user->isNot($alvo);
    }
}
