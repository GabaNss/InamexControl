<?php

namespace App\Services;

use App\Exceptions\DiaEncerradoException;
use App\Models\Prescricao;
use App\Models\RegistroMedicacao;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Camada de regra de negocio para os registros de administracao de medicacao.
 * Components Livewire e Policies devem delegar aqui em vez de checar/alterar
 * RegistroMedicacao diretamente, para garantir uma unica fonte de verdade.
 */
class RegistroMedicacaoService
{
    public function __construct(
        private readonly DiarioService $diarioService,
        private readonly AuditoriaService $auditoriaService,
    ) {}

    /**
     * Garante 1 RegistroMedicacao "pendente" (administrado = false, sem
     * usuario_id ainda) por prescricao ativa, para a data informada. Chamado
     * pelo EncerrarDiarioCommand na virada do dia, ou sob demanda ao abrir a
     * tela de registros do dia (idempotente: nao duplica os que ja existem).
     */
    public function gerarRegistrosDoDia(Carbon $data): void
    {
        Prescricao::where('ativa', true)->each(function (Prescricao $prescricao) use ($data): void {
            RegistroMedicacao::firstOrCreate([
                'prescricao_id' => $prescricao->id,
                'data' => $data->toDateString(),
                'turno' => $prescricao->horario,
            ], [
                'administrado' => false,
            ]);
        });
    }

    /**
     * Marca um registro como administrado/nao administrado, validando que o
     * dia ainda esta aberto antes de gravar. Registra a acao via AuditoriaService.
     *
     * @throws DiaEncerradoException se o dia da 'data' do registro ja estiver encerrado.
     */
    public function registrarAdministracao(RegistroMedicacao $registro, bool $administrado, ?string $observacao, User $usuario): RegistroMedicacao
    {
        if (! $this->diaEstaAberto($registro->data)) {
            throw new DiaEncerradoException($registro->data->toDateString());
        }

        $dadosAntes = $registro->only(['administrado', 'observacao', 'usuario_id']);

        $registro->update([
            'administrado' => $administrado,
            'observacao' => $observacao,
            'usuario_id' => $usuario->id,
        ]);

        $this->auditoriaService->registrar(
            usuario: $usuario,
            acao: 'registro_medicacao.atualizado',
            alvo: $registro,
            dadosAntes: $dadosAntes,
            dadosDepois: $registro->only(['administrado', 'observacao', 'usuario_id']),
        );

        return $registro;
    }

    /**
     * REGRA CRITICA: fonte de verdade para saber se um registro daquela data
     * ainda pode ser editado/excluido. Usado pelas Policies e pelos
     * componentes Livewire antes de qualquer escrita.
     */
    public function diaEstaAberto(Carbon $data): bool
    {
        return ! $this->diarioService->estaEncerrado($data);
    }
}
