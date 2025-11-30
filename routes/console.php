<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\Billings\Trial\ExpirarTrials;
use App\Jobs\Billings\Trial\LimparTrialsExpirados;
use App\Jobs\Billings\Trial\VerificarTrialsExpirando;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::job(new VerificarTrialsExpirando)
    ->dailyAt('09:00')           // Envia avisos pela manhã
    ->timezone('Africa/Luanda')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new ExpirarTrials)
    ->dailyAt('00:30')           // Expira trials vencidos após meia-noite
    ->timezone('Africa/Luanda')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new LimparTrialsExpirados)
    ->dailyAt('03:00')           // Limpa dados após 3 dias de expiração
    ->timezone('Africa/Luanda')
    ->withoutOverlapping()
    ->onOneServer();
/*
|--------------------------------------------------------------------------
| Schedules de Produção – OMNIGREJAS Trials
|--------------------------------------------------------------------------
| Estes jobs mantêm o ciclo completo dos períodos de teste:
| 1. VerificarTrialsExpirando → envia lembretes (4 e 1 dia antes)
| 2. ExpirarTrials → marca expirados automaticamente
| 3. LimparTrialsExpirados → remove trials expirados há +3 dias
|
| Todos executam no fuso horário de Angola.
| O servidor deve ter o cron do Laravel ativo a cada minuto:
| * * * * * php /caminho/para/o/projeto/artisan schedule:run >> /dev/null 2>&1
|--------------------------------------------------------------------------


Schedules de Testes Local – OMNIGREJAS Trials

Schedule::call(function () {
    dispatch(new ExpirarTrials());
    logger('✅ Job ExpirarTrials executado automaticamente (simulação a cada 5s)');
})->everySecond();

Schedule::call(function () {
    dispatch(new VerificarTrialsExpirando());
    logger('✅ Job Verificar executado automaticamente (simulação a cada 5s)');
})->everySecond();

Schedule::call(function () {
    dispatch(new LimparTrialsExpirados());
    logger('✅ Job Limpando executado automaticamente (simulação a cada 5s)');
})->everySecond();

Exemplos


*/

