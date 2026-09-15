<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 00:01 — abre o diario do novo dia e gera os registros de medicacao
// pendentes para todas as prescricoes ativas.
Schedule::command('diario:abrir')
    ->dailyAt('00:01')
    ->withoutOverlapping();

// 23:59 — encerra o diario do dia atual, tornando os registros imutaveis.
Schedule::command('diario:encerrar')
    ->dailyAt('23:59')
    ->withoutOverlapping();
