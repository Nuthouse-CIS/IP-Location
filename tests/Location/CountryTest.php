<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Location;

use InvalidArgumentException;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    public function testSuccess(): void
    {
        $country = new Country(
            $isoAlpha2 = 'RU',
            $isoAlpha3 = 'RUS',
            $name = 'Russia',
            $coords = new Coordinates(5, 10)
        );
        $this->assertSame($isoAlpha2, $country->getIsoAlpha2());
        $this->assertSame($isoAlpha3, $country->getIsoAlpha3());
        $this->assertSame($name, $country->getName());
        $this->assertSame($coords, $country->getCoordinates());
    }

    public function testCase(): void
    {
        $country = new Country(
            'ru',
            'rus'
        );
        $this->assertSame('RU', $country->getIsoAlpha2());
        $this->assertSame('RUS', $country->getIsoAlpha3());
    }

    public function testAtleastException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Country(null);
    }

    public function testAtleastIsoAlpha2(): void
    {
        $country = new Country(
            $isoAlpha2 = 'RU'
        );
        $this->assertSame($isoAlpha2, $country->getIsoAlpha2());
    }

    public function testAtleastIsoAlpha3(): void
    {
        $country = new Country(
            null,
            $isoAlpha3 = 'RUS'
        );
        $this->assertSame($isoAlpha3, $country->getIsoAlpha3());
    }

    public function testAtleastName(): void
    {
        $country = new Country(
            null,
            null,
            $name = 'Russia'
        );
        $this->assertSame($name, $country->getName());
    }

    public function testLengthAlpha2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Country(
            ''
        );
    }

    public function testLengthAlpha3(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Country(
            null,
            ''
        );
    }

    public function testLengthName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Country(
            null,
            null,
            ''
        );
    }
}
