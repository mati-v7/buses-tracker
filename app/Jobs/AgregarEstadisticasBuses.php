<?php

namespace App\Jobs;

use App\Models\AggregatedStat;
use App\Models\RawPosition;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class AgregarEstadisticasBuses implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fin = now();
        $inicio = $fin->copy()->subHour();

        $lineas = RawPosition::whereBetween('capturado_en', [$inicio, $fin])
            ->distinct()
            ->pluck('linea');

        foreach ($lineas as $linea) {
            $buses = RawPosition::where('linea', $linea)
                ->whereBetween('capturado_en', [$inicio, $fin])
                ->orderBy('unidad')
                ->orderBy('capturado_en')
                ->get();

            if ($buses->isEmpty()) {
                continue;
            }

            $unidades = $buses->groupBy('unidad');
            $velocidadesPromedioPorUnidad = [];

            foreach ($unidades as $unidad => $posiciones) {
                $vels = [];
                $posAnterior = null;

                foreach ($posiciones as $pos) {
                    if ($posAnterior) {
                        $vel = AggregatedStat::calcularVelocidad(
                            $posAnterior->posicion->getLatitude(),
                            $posAnterior->posicion->getLongitude(),
                            $pos->posicion->getLatitude(),
                            $pos->posicion->getLongitude(),
                            $posAnterior->capturado_en,
                            $pos->capturado_en
                        );
                        $vels[] = $vel;
                    }
                    $posAnterior = $pos;
                }

                if (!empty($vels)) {
                    $velocidadesPromedioPorUnidad[] = array_sum($vels) / count($vels);
                }
            }

            $velocidadPromedioLinea = !empty($velocidadesPromedioPorUnidad)
                ? array_sum($velocidadesPromedioPorUnidad) / count($velocidadesPromedioPorUnidad)
                : null;

            $busesActivos = $unidades->count();
            $porcentajeAire = ($buses->where('aire', true)->count() / $buses->count()) * 100;

            AggregatedStat::updateOrCreate(
                [
                    'linea' => $linea,
                    'fecha' => $inicio->toDateString(),
                    'hora'  => $inicio->hour,
                ],
                [
                    'buses_activos'      => $busesActivos,
                    'promedio_velocidad' => $velocidadPromedioLinea,
                    'porcentaje_aire'    => $porcentajeAire,
                ]
            );
        }
    }
}
