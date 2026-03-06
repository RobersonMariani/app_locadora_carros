<?php

declare(strict_types=1);

use App\Jobs\VerificarLocacoesAtrasadasJob;
use App\Jobs\VerificarManutencoesProximasJob;
use Illuminate\Support\Facades\Schedule;

Schedule::command('inspire')->hourly();

Schedule::job((new VerificarManutencoesProximasJob)->onQueue('monitoring'))->dailyAt('07:00');
Schedule::job((new VerificarLocacoesAtrasadasJob)->onQueue('monitoring'))->dailyAt('08:00');
