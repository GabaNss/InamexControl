<?php

namespace App\Services;

use App\Models\LogAuditoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Camada responsavel por gravar a trilha de auditoria (logs_auditoria) de
 * toda acao sensivel do sistema: quem fez, quando, de onde (IP/user-agent) e
 * o que mudou. Deve ser chamada pelos demais Services (RegistroMedicacaoService,
 * DiarioService) e pelos componentes Livewire que alterem dados clinicos/cadastrais.
 */
class AuditoriaService
{
    /**
     * Registra uma acao de auditoria.
     *
     * $usuario e nulo para acoes disparadas pelo sistema (ex: o cron de
     * encerramento automatico do diario), nao por uma requisicao HTTP de um
     * usuario autenticado — nesse caso nao ha IP/user-agent de requisicao.
     */
    public function registrar(
        ?User $usuario,
        string $acao,
        ?Model $alvo = null,
        ?array $dadosAntes = null,
        ?array $dadosDepois = null,
    ): LogAuditoria {
        return LogAuditoria::create([
            'usuario_id' => $usuario?->id,
            'acao' => $acao,
            'alvo_type' => $alvo?->getMorphClass(),
            'alvo_id' => $alvo?->getKey(),
            'dados_antes' => $dadosAntes,
            'dados_depois' => $dadosDepois,
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
            'user_agent' => app()->runningInConsole() ? null : request()->userAgent(),
        ]);
    }
}
