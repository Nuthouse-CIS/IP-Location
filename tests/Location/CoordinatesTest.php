<?php

namespace Seagull4auka\IPLocation\Tests\Location;

use PHPUnit\Framework\TestCase;
use Seagull4auka\IPLocation\Location\Coordinates;

class CoordinatesTest extends TestCase
{

    public function testSuccess(): void
    {
        $coordinates = new Coordinates($latitude = 10, $longitude = 5);
        $this->assertEquals($latitude, $coordinates->getLatitude());
        $this->assertEquals($longitude, $coordinates->getLongitude());
    }
}
