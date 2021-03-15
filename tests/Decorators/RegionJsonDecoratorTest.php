<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Decorators;

use NuthouseCIS\IPLocation\Decorators\CoordinatesJsonDecorator;
use NuthouseCIS\IPLocation\Decorators\RegionJsonDecorator;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Region;
use PHPUnit\Framework\TestCase;

class RegionJsonDecoratorTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $coordinates = new Coordinates($lat = 10.0, $lon = 5.0);
        $regionFull = new Region($name = 'Saint-Petersburg', $iso = 'RU-SPE', $coordinates);
        $region = new Region($name);

        $decoratorFull = new RegionJsonDecorator($regionFull);
        $decorator = new RegionJsonDecorator($region);

        $expectedFull = [
            'name' => $name,
            'isoCode' => $iso,
            'coordinates' => new CoordinatesJsonDecorator($coordinates)
        ];
        $expected = ['name' => $name, 'isoCode' => null];

        $this->assertEquals($expectedFull, $decoratorFull->jsonSerialize());
        $this->assertEquals($expected, $decorator->jsonSerialize());
    }
}
