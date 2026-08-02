<?php

namespace Tests\Unit;

use App\Services\GeoService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GeoServiceTest extends TestCase
{
    #[Test]
    public function it_calculates_haversine_distance_in_meters(): void
    {
        $distance = GeoService::getDistanceMeters(-6.200000, 106.816666, -6.201000, 106.816666);

        $this->assertEqualsWithDelta(111.2, $distance, 0.5);
    }
}
