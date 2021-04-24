<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Locators\SypexGeo;

use NuthouseCIS\IPLocation\Exception\IPLocationException;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Locators\SypexGeo\ApiServer;
use NuthouseCIS\IPLocation\Locators\SypexGeo\SypexGeoApiAdapter;
use NuthouseCIS\IPLocation\Tests\MockTraits\HttpClientTrait;
use PHPUnit\Framework\TestCase;

class SypexGeoApiAdapterTest extends TestCase
{
    use HttpClientTrait;

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testSuccess(): void
    {
        $responseBody =
            '{"ip":"77.88.8.8","city":{"id":524901,"lat":55.752220000000001,"lon":37.615560000000002,"name_ru":"Москва","name_en":"Moscow","name_de":"Moskau","name_fr":"Moscou","name_it":"Mosca","name_es":"Moscú","name_pt":"Moscovo","okato":"45","vk":1,"population":12330126,"tel":"495,496,498,499","post":"101xxx:135xxx"},"region":{"id":524894,"lat":55.759999999999998,"lon":37.609999999999999,"name_ru":"Москва","name_en":"Moskva","name_de":"Moskau","name_fr":"Moscou","name_it":"Mosca","name_es":"Moscú","name_pt":"Moscovo","iso":"RU-MOW","timezone":"Europe/Moscow","okato":"45","auto":"77, 97, 99, 177, 197, 199, 777","vk":0,"utc":3},"country":{"id":185,"iso":"RU","continent":"EU","lat":60,"lon":100,"name_ru":"Россия","name_en":"Russia","name_de":"Russland","name_fr":"Russie","name_it":"Russia","name_es":"Rusia","name_pt":"Rússia","timezone":"Europe/Moscow","area":17100000,"population":140702000,"capital_id":524901,"capital_ru":"Москва","capital_en":"Moscow","cur_code":"RUB","phone":"7","neighbours":"GE,CN,BY,UA,KZ,LV,PL,EE,LT,FI,MN,NO,AZ,KP","vk":1,"utc":3},"error":"","request":-154,"created":"2021.04.09","timestamp":1617993452}';
        $locationExtra = [
            "city" => [
                "id" => 524901,
                "name_de" => "Moskau",
                "name_es" => "Moscú",
                "name_fr" => "Moscou",
                "name_it" => "Mosca",
                "name_pt" => "Moscovo",
                "name_ru" => "Москва",
                "okato" => "45",
                "population" => 12330126,
                "post" => "101xxx:135xxx",
                "tel" => "495,496,498,499",
                "vk" => 1,
            ],
            "country" => [
                "area" => 17100000,
                "capital_en" => "Moscow",
                "capital_id" => 524901,
                "capital_ru" => "Москва",
                "continent" => "EU",
                "cur_code" => "RUB",
                "id" => 185,
                "name_de" => "Russland",
                "name_es" => "Rusia",
                "name_fr" => "Russie",
                "name_it" => "Russia",
                "name_pt" => "Rússia",
                "name_ru" => "Россия",
                "neighbours" => "GE,CN,BY,UA,KZ,LV,PL,EE,LT,FI,MN,NO,AZ,KP",
                "phone" => "7",
                "population" => 140702000,
                "timezone" => "Europe/Moscow",
                "utc" => 3,
                "vk" => 1,
            ],
            "region" => [
                "auto" => "77, 97, 99, 177, 197, 199, 777",
                "id" => 524894,
                "name_de" => "Moskau",
                "name_es" => "Moscú",
                "name_fr" => "Moscou",
                "name_it" => "Mosca",
                "name_pt" => "Moscovo",
                "name_ru" => "Москва",
                "okato" => "45",
                "timezone" => "Europe/Moscow",
                "utc" => 3,
                "vk" => 0,
            ],
            "created" => "2021.04.09",
            "error" => "",
            "ip" => "77.88.8.8",
            "request" => -154,
            "timestamp" => 1617993452,
        ];

        list(
            'client' => $httpClientMock, 'requestFactory' => $requestFactoryMock
            ) = $this->createClientMockObjects($responseBody);

        $adapter = new SypexGeoApiAdapter($httpClientMock, $requestFactoryMock, ApiServer::SERVER_GEODNS, null);
        $location = $adapter->locate(new Ip('77.88.8.8'));

        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame('RU', $location->getCountry()->getIsoAlpha2());
        $this->assertSame(null, $location->getCountry()->getIsoAlpha3());
        $this->assertSame('Russia', $location->getCountry()->getName());
        $this->assertSame(60.0, $location->getCountry()->getCoordinates()->getLatitude());
        $this->assertSame(100.0, $location->getCountry()->getCoordinates()->getLongitude());
        $this->assertSame(55.76, $location->getRegion()->getCoordinates()->getLatitude());
        $this->assertSame(37.61, $location->getRegion()->getCoordinates()->getLongitude());
        $this->assertSame(55.75222, $location->getCoordinates()->getLatitude());
        $this->assertSame(37.61556, $location->getCoordinates()->getLongitude());
        $this->assertSame('Moskva', $location->getRegion()->getName());
        $this->assertSame('RU-MOW', $location->getRegion()->getIsoCode());
        $this->assertSame('Moscow', $location->getCity()->getName());
        $this->assertSame($location->getCoordinates(), $location->getCity()->getCoordinates());
        $this->assertEquals($locationExtra, $location->getExtra());
    }

    public function testError(): void
    {
        $errorMessage = 'Some error';
        $responseBody = "{\"error\": \"$errorMessage\"}";

        list(
            'client' => $httpClientMock,
            'requestFactory' => $requestFactoryMock
            ) = $this->createClientMockObjects($responseBody);

        $adapter = new SypexGeoApiAdapter($httpClientMock, $requestFactoryMock, ApiServer::SERVER_GEODNS, null);

        $this->expectExceptionMessage($errorMessage);
        $adapter->locate(new Ip('77.88.8.8'));
    }

    public function testUnknownResult(): void
    {
        $responseBody = "unhandled response";

        list(
            'client' => $httpClientMock, 'requestFactory' => $requestFactoryMock
            ) = $this->createClientMockObjects($responseBody);
        $adapter = new SypexGeoApiAdapter($httpClientMock, $requestFactoryMock, ApiServer::SERVER_GEODNS, null);

        $this->expectException(IPLocationException::class);
        $this->expectExceptionMessage('The SypexGeo service responded with an unknown result');
        $adapter->locate(new Ip('8.8.8.8'));
    }
}
