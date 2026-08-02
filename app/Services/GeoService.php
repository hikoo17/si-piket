<?php

namespace App\Services;

class GeoService
{
    private const EARTH_RADIUS_METERS = 6371000;

    public static function getDistanceMeters(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2,
    ): float {
        $latitudeDelta = deg2rad($lat2 - $lat1);
        $longitudeDelta = deg2rad($lon2 - $lon1);

        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($longitudeDelta / 2) ** 2;

        $angularDistance = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $angularDistance;
    }
}
