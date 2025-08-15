<?php

namespace App\Console\Commands;

use App\Models\RawPosition;
use Carbon\Carbon;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CapturarTrafico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buses:capturar-trafico {linea}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Captura datos de buses para una línea específica y los guarda en la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $linea = $this->argument('linea');

        $this->info("Consultando API para la línea {$linea}...");

        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post('https://www.jaha.com.py/api/posicionColectivos', [
                    'linea' => $linea
                ]);

            if ($response->failed()) {
                $this->error("Error al consultar la API");
                return Command::FAILURE;
            }

            $data = $response->json();
            $capturadoEn = Carbon::now();

            foreach ($data as $bus) {
                RawPosition::create([
                    'linea'        => $linea,
                    'unidad'       => $bus['unidad'],
                    'posicion'     => Point::makeGeodetic($bus['lon'], $bus['lat']),
                    'estado'       => trim($bus['estado']) ?? null,
                    'sen'          => $bus['sen'] ?? null,
                    'hora_api'     => $bus['hora'] ?? null,
                    'online'       => isset($bus['online']) ? (bool)$bus['online'] : false,
                    'aire'         => isset($bus['aire']) ? (bool)$bus['aire'] : false,
                    'capturado_en' => $capturadoEn,
                ]);
            }

            $this->info("Captura completada: " . count($data) . " registros guardados.");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
