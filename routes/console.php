<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Encerramento automatico do diario: roda todo dia a meia-noite, tornando
// imutaveis os RegistroMedicacao do dia que terminou e abrindo o novo dia.
// Requer um scheduler ativo no servidor (cron chamando "php artisan schedule:run"
// a cada minuto, ou um worker/cron equivalente no provedor de hospedagem).
Schedule::command('diario:encerrar')
    ->dailyAt('00:00')
    ->withoutOverlapping();
