<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Locators\Ip2Location;

use IP2Location\Database;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Location\Region;
use NuthouseCIS\IPLocation\Locators\Ip2Location\Ip2LocationAdapter;
use PHPUnit\Framework\TestCase;

class Ip2LocationAdapterTest extends TestCase
{

    public function testSuccess(): void
    {
        $locationExpected = new Location(
            new Country('US', null, 'United States of America'),
            new Region('California'),
            new City(
                'Mountain View',
                new Coordinates(37.405991, -122.078514)
            ),
            [
                1004 => '134744072',
                1003 => 4,
                1002 => '8.8.8.8',
                16 => Database::FIELD_NOT_SUPPORTED,
                17 => Database::FIELD_NOT_SUPPORTED,
                18 => Database::FIELD_NOT_SUPPORTED,
                15 => Database::FIELD_NOT_SUPPORTED,
                14 => Database::FIELD_NOT_SUPPORTED,
                12 => Database::FIELD_NOT_SUPPORTED,
                13 => Database::FIELD_NOT_SUPPORTED,
                20 => Database::FIELD_NOT_SUPPORTED,
                19 => Database::FIELD_NOT_SUPPORTED,
                11 => Database::FIELD_NOT_SUPPORTED,
                10 => Database::FIELD_NOT_SUPPORTED,
                9 => Database::FIELD_NOT_SUPPORTED,
                8 => Database::FIELD_NOT_SUPPORTED,
                7 => Database::FIELD_NOT_SUPPORTED,
            ]
        );
        $db = new Database(__DIR__ . '/../../Data/IP2LOCATION-LITE-DB5.BIN', Database::FILE_IO);
        $adapter = new Ip2LocationAdapter(
            $db,
            [Database::ALL],
            [
                Database::COUNTRY,
                Database::REGION_NAME,
                Database::CITY_NAME,
                Database::COORDINATES,
            ]
        );

        $location = $adapter->locate(new Ip('8.8.8.8'));
        $this->assertEquals($locationExpected, $location);
    }

    public function testSuccessV6(): void
    {
        $locationExpected = new Location(
            new Country('US', null, 'United States of America'),
            null,
            null,
            []
        );

        $db = new Database(__DIR__ . '/../../Data/IP2LOCATION-LITE-DB1.IPV6.BIN', Database::FILE_IO);
        $adapter = new Ip2LocationAdapter(
            $db,
            [Database::COUNTRY]
        );

        $location = $adapter->locate(new Ip('2001:4860:4860::8888'));
        $this->assertEquals($locationExpected, $location);
    }

    public function testRequired(): void
    {
        $db = new Database(__DIR__ . '/../../Data/IP2LOCATION-LITE-DB5.BIN', Database::FILE_IO);
        $adapter = new Ip2LocationAdapter(
            $db,
            [
                Database::COUNTRY,
                Database::REGION_NAME,
                Database::CITY_NAME,
                Database::COORDINATES,
            ],
            [
                Database::COUNTRY,
                Database::REGION_NAME,
                Database::CITY_NAME,
                Database::COORDINATES,
                $requiredField = Database::IP_ADDRESS,
            ]
        );

        $this->expectExceptionMessage("Field '{$requiredField}' is required");
        $adapter->locate(new Ip('8.8.8.8'));
    }

    public function testUnsupported(): void
    {
        $db = new Database(__DIR__ . '/../../Data/IP2LOCATION-LITE-DB5.BIN', Database::FILE_IO);
        $adapter = new Ip2LocationAdapter(
            $db,
            $fields = [
                Database::COUNTRY,
                Database::REGION_NAME,
                Database::CITY_NAME,
                Database::COORDINATES,
                $unsupportedField = Database::TIME_ZONE,
            ],
            $fields
        );

        $this->expectExceptionMessage(
            sprintf('Required field \'%s\' has invalid value: %s', $unsupportedField, Database::FIELD_NOT_SUPPORTED)
        );
        $adapter->locate(new Ip('8.8.8.8'));
    }
}
