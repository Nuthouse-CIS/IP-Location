<?php

namespace NuthouseCIS\IPLocation\Tests\Location;

use NuthouseCIS\IPLocation\Location\Coordinates;
use PHPUnit\Framework\TestCase;

class CoordinatesTest extends TestCase
{

    public function testSuccess(): void
    {
        $coordinates = new Coordinates($latitude = 10, $longitude = 5);
        $this->assertEquals($latitude, $coordinates->getLatitude());
        $this->assertEquals($longitude, $coordinates->getLongitude());
    }
}
