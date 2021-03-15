<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Location;

use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Region;
use PHPUnit\Framework\TestCase;

class RegionTest extends TestCase
{

    public function testSuccess(): void
    {
        $region = new Region(
            $name = 'Saint-Petersburg',
            $isoCode = 'RU-SPE',
            $coordinates = new Coordinates(10.5, 5.0)
        );
        $this->assertSame($name, $region->getName());
        $this->assertSame($isoCode, $region->getIsoCode());
        $this->assertSame($coordinates, $region->getCoordinates());

        $regionMinimal = new Region($name);
        $this->assertNull($regionMinimal->getIsoCode());
        $this->assertNull($regionMinimal->getCoordinates());
    }
}
