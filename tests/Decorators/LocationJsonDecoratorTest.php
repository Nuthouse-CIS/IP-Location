<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Decorators;

use NuthouseCIS\IPLocation\Decorators\CityJsonDecorator;
use NuthouseCIS\IPLocation\Decorators\CoordinatesJsonDecorator;
use NuthouseCIS\IPLocation\Decorators\CountryJsonDecorator;
use NuthouseCIS\IPLocation\Decorators\LocationJsonDecorator;
use NuthouseCIS\IPLocation\Decorators\RegionJsonDecorator;
use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Location\Region;
use PHPUnit\Framework\TestCase;

class LocationJsonDecoratorTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $coordinates = new Coordinates($lat = 10.0, $lon = 5.0);
        $country = new Country($countryIso2 = 'RU');
        $region = new Region('Saint-Petersburg');
        $city = new City('Saint-Petersburg', $coordinates);

        $locationFull = new Location($country, $region, $city, $extra = ['timezone' => 'UTC']);
        $location = new Location($country);

        $decoratorFull = new LocationJsonDecorator($locationFull);
        $decorator = new LocationJsonDecorator($location);

        $expectedFull = [
            'country' => new CountryJsonDecorator($country),
            'region' => new RegionJsonDecorator($region),
            'city' => new CityJsonDecorator($city),
            'coordinates' => new CoordinatesJsonDecorator($coordinates),
            'extra' => $extra,
        ];
        $expected = ['country' => new CountryJsonDecorator($country)];

        $this->assertEquals($expectedFull, $decoratorFull->jsonSerialize());
        $this->assertEquals($expected, $decorator->jsonSerialize());
    }
}
