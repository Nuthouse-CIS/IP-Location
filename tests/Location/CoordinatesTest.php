<?php

namespace NuthouseCIS\IPLocation\Tests\Location;

use NuthouseCIS\IPLocation\Location\Coordinates;
use PHPUnit\Framework\TestCase;

class CoordinatesTest extends TestCase
{

    public function testSuccess(): void
    {
        $coordinates = new Coordinates($latitude = 10.0, $longitude = 5.0);
        $this->assertSame($latitude, $coordinates->getLatitude());
        $this->assertSame($longitude, $coordinates->getLongitude());
    }
}
