<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AggregatedStat extends Model
{
    protected $fillable = [
        'linea',
        'fecha',
        'hora',
        'buses_activos',
        'promedio_velocidad',
        'porcentaje_aire',
    ];

    public static function calcularVelocidad($lat1, $lon1, $lat2, $lon2, $t1, $t2)
    {
        $distancia = self::haversineGreatCircleDistance(
            $lat1,
            $lon1,
            $lat2,
            $lon2,
        );

        $segundos = abs(Carbon::parse($t2)->diffInSeconds(Carbon::parse($t1)));

        if ($segundos == 0) return 0;

        return ($distancia / $segundos) * 3.6;
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public static function haversineGreatCircleDistance(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371000
    ) {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
