<?php

namespace App\Policies;

use App\Models\RegistroMedicacao;
use App\Models\User;
use App\Services\RegistroMedicacaoService;
use Illuminate\Support\Carbon;

/**
 * Regras de acesso aos registros de administracao de medicacao.
 *
 * Leitura/criacao: enfermeiro, tecnico, medico, diretor e admin podem
 * visualizar e preencher os registros do dia.
 *
 * REGRA CRITICA: update/delete so sao permitidos se, alem do cargo, o dia da
 * 'data' do registro ainda nao estiver encerrado (RegistroMedicacaoService e
 * a fonte de verdade dessa checagem — repetida aqui para a Policy refletir o
 * mesmo resultado que o Service vai aplicar, evitando que a UI permita uma
 * acao que o backend vai rejeitar).
 */
class RegistroMedicacaoPolicy
{
    public function __construct(
        private readonly RegistroMedicacaoService $registroMedicacaoService,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->temCargoAtribuido();
    }

    public function view(User $user, RegistroMedicacao $registroMedicacao): bool
    {
        return $user->temCargoAtribuido();
    }

    public function create(User $user): bool
    {
        return $user->temCargo('admin', 'medico', 'enfermeiro', 'chefe_enfermagem', 'tecnico')
            && $user->estaNoTurno()
            && $this->registroMedicacaoService->diaEstaAberto(Carbon::today());
    }

    public function update(User $user, RegistroMedicacao $registroMedicacao): bool
    {
        return $user->temCargo('admin', 'medico', 'enfermeiro', 'chefe_enfermagem', 'tecnico')
            && $user->estaNoTurno()
            && $this->registroMedicacaoService->diaEstaAberto($registroMedicacao->data);
    }

    public function delete(User $user, RegistroMedicacao $registroMedicacao): bool
    {
        return $user->temCargo('admin', 'chefe_enfermagem')
            && $user->estaNoTurno()
            && $this->registroMedicacaoService->diaEstaAberto($registroMedicacao->data);
    }
}
