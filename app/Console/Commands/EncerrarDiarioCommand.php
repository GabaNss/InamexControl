<?php

namespace App\Console\Commands;

use App\Services\DiarioService;
use App\Services\RegistroMedicacaoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * Comando agendado (ver agendamento em routes/console.php, todo dia a
 * meia-noite) responsavel por:
 * 1. Encerrar o DiarioStatus do dia que terminou (via DiarioService::encerrarDia),
 *    tornando os RegistroMedicacao daquele dia imutaveis.
 * 2. Abrir o DiarioStatus do novo dia e gerar os RegistroMedicacao pendentes
 *    para as prescricoes ativas (via DiarioService::abrirDia e
 *    RegistroMedicacaoService::gerarRegistrosDoDia).
 *
 * $usuario = null em DiarioService::encerrarDia() porque esta acao e
 * disparada pelo sistema (cron), nao por um usuario autenticado.
 */
class EncerrarDiarioCommand extends Command
{
    protected $signature = 'diario:encerrar';

    protected $description = 'Encerra o diario do dia anterior e abre o diario do novo dia (executado automaticamente a meia-noite).';

    public function __construct(
        private readonly DiarioService $diarioService,
        private readonly RegistroMedicacaoService $registroMedicacaoService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $hoje = Carbon::today();
        $ontem = $hoje->copy()->subDay();

        DB::transaction(function () use ($hoje, $ontem): void {
            $this->diarioService->encerrarDia($ontem);
            $this->diarioService->abrirDia($hoje);
            $this->registroMedicacaoService->gerarRegistrosDoDia($hoje);
        });

        $this->info("Diario de {$ontem->toDateString()} encerrado e diario de {$hoje->toDateString()} aberto.");

        return self::SUCCESS;
    }
}
