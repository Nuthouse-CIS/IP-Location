<?php

namespace Seagull4auka\IPLocation\Tests\Location;

use PHPUnit\Framework\TestCase;
use Seagull4auka\IPLocation\Location\City;
use Seagull4auka\IPLocation\Location\Coordinates;
use Seagull4auka\IPLocation\Location\Country;
use Seagull4auka\IPLocation\Location\Location;
use Seagull4auka\IPLocation\Location\Region;

class LocationTest extends TestCase
{
    public function testSuccess(): void
    {
        $location = new Location(
            $country = new Country('RU', null, null, null),
            $region = new Region('Saint-Petersburg', null, null),
            $city = new City('Saint-Petersburg', null)
        );

        $this->assertEquals($country, $location->getCountry());
        $this->assertEquals($region, $location->getRegion());
        $this->assertEquals($city, $location->getCity());
    }

    public function testCoordinates(): void
    {
        $location = new Location(
            $country = new Country(null, null, 'Russia', new Coordinates(1, 1)),
            $region = new Region(
                'Saint-Petersburg', null, new Coordinates(2, 2)
            ),
            $city = new City(
                'Saint-Petersburg',
                $coords = new Coordinates(10, 10)
            )
        );
        $this->assertEquals($coords, $location->getCoordinates());

        $location2 = new Location(
            $country = new Country(null, null, 'Russia', new Coordinates(1, 1)),
            $region = new Region(
                'Saint-Petersburg',
                null,
                $coords2 = new Coordinates(10, 10)
            ),
            $city = new City('Saint-Petersburg')
        );
        $this->assertEquals($coords2, $location2->getCoordinates());

        $location3 = new Location(
            $country = new Country(
                null,
                null,
                'Russia',
                $coords3 = new Coordinates(10, 10)
            ),
            $region = new Region('Saint-Petersburg'),
            $city = new City('Saint-Petersburg')
        );
        $this->assertEquals($coords3, $location3->getCoordinates());
    }
}
