<?php

namespace Seagull4auka\IPLocation\Tests;

use Seagull4auka\IPLocation\Location;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    public function testLocation(): void
    {
        $location = new Location(
            $country = 'Russia',
            $region = 'Saint-Petersburg',
            $city = 'Saint-Petersburg'
        );

        $this->assertEquals($country, $location->getCountry());
        $this->assertEquals($region, $location->getRegion());
        $this->assertEquals($city, $location->getCity());
    }
}
