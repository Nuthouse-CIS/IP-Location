<?php

namespace Seagull4auka\IPLocation\Tests\Location;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Seagull4auka\IPLocation\Location\Coordinates;
use Seagull4auka\IPLocation\Location\Country;

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
        $this->assertEquals($isoAlpha2, $country->getIsoAlpha2());
        $this->assertEquals($isoAlpha3, $country->getIsoAlpha3());
        $this->assertEquals($name, $country->getName());
        $this->assertEquals($coords, $country->getCoordinates());
    }

    public function testCase(): void
    {
        $country = new Country(
            'ru',
            'rus'
        );
        $this->assertEquals('RU', $country->getIsoAlpha2());
        $this->assertEquals('RUS', $country->getIsoAlpha3());
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
        $this->assertEquals($isoAlpha2, $country->getIsoAlpha2());
    }

    public function testAtleastIsoAlpha3(): void
    {
        $country = new Country(
            null,
            $isoAlpha3 = 'RUS'
        );
        $this->assertEquals($isoAlpha3, $country->getIsoAlpha3());
    }

    public function testAtleastName(): void
    {
        $country = new Country(
            null,
            null,
            $name = 'Russia'
        );
        $this->assertEquals($name, $country->getName());
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
