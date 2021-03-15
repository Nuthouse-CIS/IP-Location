<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Decorators;

use NuthouseCIS\IPLocation\Decorators\CoordinatesJsonDecorator;
use NuthouseCIS\IPLocation\Location\Coordinates;
use PHPUnit\Framework\TestCase;

class CoordinatesJsonDecoratorTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $coordinates = new Coordinates($lat = 10.0, $lon = 5.0);

        $decorator = new CoordinatesJsonDecorator($coordinates);

        $expected = [
            'latitude' => $lat,
            'longitude' => $lon,
        ];
        $this->assertSame($expected, $decorator->jsonSerialize());
    }
}
