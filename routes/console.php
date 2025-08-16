<?php

use App\Jobs\AgregarEstadisticasBuses;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::everyThreeMinutes()
    ->withoutOverlapping()
    ->group(function () {
        /**
         * Comando que se encarga de capturar datos de buses para una línea específica y los guarda en la base de datos
         */
        Schedule::command('buses:capturar-trafico 233')
            ->sendOutputTo(storage_path('logs/commands/capturar-trafico.log'), append: true);

        Schedule::command('buses:capturar-trafico 232')
            ->sendOutputTo(storage_path('logs/commands/capturar-trafico.log'), append: true);
    });

Schedule::everyFifteenMinutes()
    ->withoutOverlapping()
    ->group(function () {
        Schedule::job(new AgregarEstadisticasBuses)->everyFifteenMinutes();
    });
