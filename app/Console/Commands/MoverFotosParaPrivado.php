<?php

namespace App\Console\Commands;

use App\Models\Paciente;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Migra as fotos de pacientes que ainda estao no disco 'public' (acessivel sem
 * login via /storage) para o disco privado 'local' (storage/app/private), fora
 * do webroot. Atualiza o caminho em cada paciente. Idempotente: pacientes cujo
 * arquivo ja nao esteja no disco public sao ignorados.
 *
 * Execucao unica apos o deploy desta mudanca (LGPD): depois disso toda foto nova
 * ja nasce no disco privado (ver App\Livewire\Pacientes\Form).
 */
class MoverFotosParaPrivado extends Command
{
    protected $signature = 'pacientes:mover-fotos-privado {--dry-run : Apenas lista o que seria movido, sem alterar nada}';

    protected $description = 'Move fotos de pacientes do disco public para o disco privado (local) e atualiza o caminho.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $movidas = 0;
        $ignoradas = 0;

        $pacientes = Paciente::whereNotNull('foto')->get();

        foreach ($pacientes as $paciente) {
            $caminho = $paciente->foto;

            // Ja esta no privado (ou o arquivo original sumiu do public): nada a fazer.
            if (! Storage::disk('public')->exists($caminho)) {
                $ignoradas++;

                continue;
            }

            if ($dryRun) {
                $this->line("[dry-run] moveria: {$caminho} (paciente #{$paciente->id})");
                $movidas++;

                continue;
            }

            $conteudo = Storage::disk('public')->get($caminho);
            Storage::disk('local')->put($caminho, $conteudo);
            Storage::disk('public')->delete($caminho);

            // O caminho relativo e o mesmo nos dois discos, entao nao muda o valor;
            // ainda assim salvamos para registrar explicitamente a migracao.
            $paciente->forceFill(['foto' => $caminho])->save();

            $this->info("movida: {$caminho} (paciente #{$paciente->id})");
            $movidas++;
        }

        $this->info("Concluido. {$movidas} foto(s) " . ($dryRun ? 'seriam movidas' : 'movidas') . ", {$ignoradas} ignorada(s).");

        return self::SUCCESS;
    }
}
