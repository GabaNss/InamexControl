<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * As Policies por model (PacientePolicy, MedicamentoPolicy, PrescricaoPolicy,
     * RegistroMedicacaoPolicy, DiarioStatusPolicy, UserPolicy) sao descobertas
     * automaticamente pelo Laravel (convencao app/Models/{Model} <->
     * app/Policies/{Model}Policy). As Gates abaixo cobrem habilidades que nao
     * mapeiam para CRUD de um unico model.
     */
    public function boot(): void
    {
        // REGRA CRITICA DE SEGURANCA: o sistema lida com dados de pacientes de
        // uma instituicao psiquiatrica (dados sensiveis de saude, LGPD), por
        // isso TODO trafego deve ser HTTPS. Fora do ambiente local, forcamos
        // o esquema https na geracao de toda URL (route(), url(), asset(),
        // redirects), mesmo que a app esteja atras de um proxy/load balancer
        // que termina o TLS antes de repassar a requisicao por HTTP interno
        // (ver bootstrap/app.php -> trustProxies, necessario para o Laravel
        // detectar corretamente que a requisicao original era HTTPS).
        if (! $this->app->isLocal()) {
            URL::forceScheme('https');
        }

        // REGRA CRITICA: usuario desativado (User.ativo = false) nao pode fazer
        // nada no sistema, independente do cargo — checado antes de qualquer
        // outra Gate/Policy.
        Gate::before(function (User $user, string $ability) {
            return $user->ativo ? null : false;
        });

        // Quem pode gerar/exportar relatorios (PDF) com dados das internas.
        Gate::define('gerar-relatorios', fn (User $user) => $user->temCargoAtribuido());

        // Quem pode encerrar/reabrir o diario manualmente (fora do agendamento
        // automatico de meia-noite).
        Gate::define('encerrar-diario-manual', fn (User $user) => $user->temCargo('admin', 'diretor'));

        // Quem pode gerenciar usuarios e cargos do sistema.
        Gate::define('gerenciar-usuarios', fn (User $user) => $user->temCargo('admin'));

        // Quem pode gerar um dump manual do banco inteiro (todos os dados de
        // todas as internas, nao um recorte por paciente como 'gerar-relatorios').
        Gate::define('gerenciar-backup', fn (User $user) => $user->temCargo('admin'));
    }
}
