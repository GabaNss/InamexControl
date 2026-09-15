<?php

namespace App\Console\Commands;

use App\Services\DiarioService;
use App\Services\RegistroMedicacaoService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Abre o diario do dia atual e gera os RegistroMedicacao pendentes para as
 * prescricoes ativas. Executado automaticamente às 00:01 pelo scheduler.
 */
class AbrirDiarioCommand extends Command
{
    protected $signature = 'diario:abrir';

    protected $description = 'Abre o diario do dia atual e gera os registros de medicacao pendentes (executado automaticamente às 00:01).';

    public function __construct(
        private readonly DiarioService $diarioService,
        private readonly RegistroMedicacaoService $registroMedicacaoService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $hoje = Carbon::today();

        DB::transaction(function () use ($hoje): void {
            $this->diarioService->abrirDia($hoje);
            $this->registroMedicacaoService->gerarRegistrosDoDia($hoje);
        });

        $this->info("Diario de {$hoje->toDateString()} aberto e registros pendentes gerados.");

        return self::SUCCESS;
    }
}
