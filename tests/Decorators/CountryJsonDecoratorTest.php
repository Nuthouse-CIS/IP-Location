<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Decorators;

use NuthouseCIS\IPLocation\Decorators\CoordinatesJsonDecorator;
use NuthouseCIS\IPLocation\Decorators\CountryJsonDecorator;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use PHPUnit\Framework\TestCase;

class CountryJsonDecoratorTest extends TestCase
{

    public function testJsonSerialize(): void
    {
        $coordinates = new Coordinates($lat = 10.0, $lon = 5.0);
        $countryFull = new Country(
            $iso2 = 'RU',
            $iso3 = 'RUS',
            $name = 'Russia',
            $coordinates
        );
        $country = new Country($iso2);

        $decoratorFull = new CountryJsonDecorator($countryFull);
        $decorator = new CountryJsonDecorator($country);

        $expectedFull = [
            'isoAlpha2'     => $iso2,
            'isoAlpha3'     => $iso3,
            'name'        => $name,
            'coordinates' => new CoordinatesJsonDecorator($coordinates),
        ];
        $expected = ['isoAlpha2' => $iso2, 'isoAlpha3' => null, 'name' => null];

        $this->assertEquals($expectedFull, $decoratorFull->jsonSerialize());
        $this->assertEquals($expected, $decorator->jsonSerialize());
    }
}
