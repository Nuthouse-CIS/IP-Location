<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Locators\SypexGeo;

use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Location\Region;
use NuthouseCIS\IPLocation\Locators\SypexGeo\SypexGeoAdapter;
use NuthouseCIS\SxGeo\SxGeo;
use PHPUnit\Framework\TestCase;

class SypexGeoAdapterTest extends TestCase
{
    public function testSuccess(): void
    {
        $sxGeoResult = [
            'city' => [
                'id' => 524901,
                'lat' => 55.75222,
                'lon' => 37.61556,
                'name_ru' => 'Москва',
                'name_en' => 'Moscow',
            ],
            'region' => [
                'id' => 524894,
                'name_ru' => 'Москва',
                'name_en' => 'Moskva',
                'iso' => 'RU-MOW',
            ],
            'country' => [
                'id' => 185,
                'iso' => 'RU',
                'lat' => 60,
                'lon' => 100,
                'name_ru' => 'Россия',
                'name_en' => 'Russia',
            ],
        ];
        $sxGeo = $this->createMock(SxGeo::class);
        $sxGeo->method('getCityFull')->willReturn($sxGeoResult);

        $locationExpected = new Location(
            new Country(
                $sxGeoResult['country']['iso'],
                null,
                $sxGeoResult['country']['name_en'],
                new Coordinates(
                    $sxGeoResult['country']['lat'],
                    $sxGeoResult['country']['lon']
                )
            ),
            new Region(
                $sxGeoResult['region']['name_en'],
                $sxGeoResult['region']['iso']
            ),
            new City(
                $sxGeoResult['city']['name_en'],
                new Coordinates(
                    $sxGeoResult['city']['lat'],
                    $sxGeoResult['city']['lon']
                )
            ),
            [
                'city' => [
                    'id' => $sxGeoResult['city']['id'],
                    'name_ru' => $sxGeoResult['city']['name_ru'],
                ],
                'region' => [
                    'id' => $sxGeoResult['region']['id'],
                    'name_ru' => $sxGeoResult['region']['name_ru'],
                ],
                'country' => [
                    'id' => $sxGeoResult['country']['id'],
                    'name_ru' => $sxGeoResult['country']['name_ru'],
                ],
            ]
        );

        $locator = new SypexGeoAdapter($sxGeo);
        $location = $locator->locate(new Ip('77.88.8.8'));

        $this->assertEquals($locationExpected, $location);
    }
}
