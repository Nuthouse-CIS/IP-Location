<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Location;

use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use PHPUnit\Framework\TestCase;

class CityTest extends TestCase
{
    public function testSuccess(): void
    {
        $city = new City(
            $name = 'Saint-Petersburg',
            $coordinates = new Coordinates(10.0, 5.0)
        );
        $this->assertSame($name, $city->getName());
        $this->assertSame($coordinates, $city->getCoordinates());
    }
}
