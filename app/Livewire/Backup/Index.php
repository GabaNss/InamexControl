<?php

namespace App\Livewire\Backup;

use App\Services\AuditoriaService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Backup manual do banco inteiro (dump via pg_dump), restrito a admin (ver
 * Gate 'gerenciar-backup' em AppServiceProvider) — nao e um recorte por
 * paciente como as exportacoes em App\Livewire\Relatorios.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    public ?string $erro = null;

    public function mount(): void
    {
        $this->authorize('gerenciar-backup');
    }

    public function baixar()
    {
        $this->authorize('gerenciar-backup');
        $this->erro = null;

        $conexao = config('database.connections.pgsql');
        $arquivo = tempnam(sys_get_temp_dir(), 'backup_inamexcontrol_');

        $process = new Process([
            'pg_dump',
            '--host='.$conexao['host'],
            '--port='.$conexao['port'],
            '--username='.$conexao['username'],
            '--dbname='.$conexao['database'],
            '--format=custom',
            '--no-owner',
            '--no-privileges',
            '--file='.$arquivo,
        ]);

        $process->setEnv([
            'PGPASSWORD' => $conexao['password'],
            'PGSSLMODE' => $conexao['sslmode'] ?? 'require',
        ]);
        $process->setTimeout(300);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            @unlink($arquivo);
            Log::error('Falha ao gerar backup manual do banco.', ['erro' => $e->getMessage()]);
            $this->erro = 'Não foi possível gerar o backup. Verifique a conexão com o banco e tente novamente.';

            return null;
        }

        app(AuditoriaService::class)->registrar(
            usuario: auth()->user(),
            acao: 'backup.manual.gerado',
        );

        $nomeArquivo = 'backup-inamexcontrol-'.now()->format('Y-m-d_His').'.dump';

        return response()->streamDownload(function () use ($arquivo) {
            readfile($arquivo);
            unlink($arquivo);
        }, $nomeArquivo, ['Content-Type' => 'application/octet-stream']);
    }

    public function render(): View
    {
        return view('livewire.backup.index');
    }
}
