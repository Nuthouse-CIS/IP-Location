<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Decorators;

use NuthouseCIS\IPLocation\Decorators\CityJsonDecorator;
use NuthouseCIS\IPLocation\Decorators\CoordinatesJsonDecorator;
use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use PHPUnit\Framework\TestCase;

class CityJsonDecoratorTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $coordinates = new Coordinates($lat = 10.0, $lon = 5.0);
        $cityFull = new City($name = 'Saint-Petersburg', $coordinates);
        $city = new City($name);

        $decoratorFull = new CityJsonDecorator($cityFull);
        $decorator = new CityJsonDecorator($city);

        $expectedFull = [
            'name' => $name,
            'coordinates' => new CoordinatesJsonDecorator($coordinates)
        ];
        $expected = ['name' => $name];

        $this->assertEquals($expectedFull, $decoratorFull->jsonSerialize());
        $this->assertEquals($expected, $decorator->jsonSerialize());
    }
}
