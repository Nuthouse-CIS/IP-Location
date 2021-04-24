<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Locators\Ip2Location;

use NuthouseCIS\IPLocation\Exception\IPLocationException;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Location\Region;
use NuthouseCIS\IPLocation\Locators\Ip2Location\Ip2LocationApiAdapter;
use NuthouseCIS\IPLocation\Tests\MockTraits\HttpClientTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class Ip2LocationApiAdapterTest
 *
 * @package NuthouseCIS\IPLocation\Tests\Locators\Ip2Location
 * @psalm-suppress PossiblyInvalidArgument
 */
class Ip2LocationApiAdapterTest extends TestCase
{
    use HttpClientTrait;

    public function testSuccess(): void
    {
        $responseBody =
            '{"country_code":"US","country_name":"United States of America","region_name":"California","city_name":"Mountain View","latitude":"37.405992","longitude":"-122.078515","credits_consumed":3}';
        $expectedLocation = new Location(
            new Country(
                'US',
                null,
                'United States of America'
            ),
            new Region('California'),
            new City(
                'Mountain View',
                new Coordinates(37.405992, -122.078515)
            ),
            ['credits_consumed' => 3]
        );

        list(
            'client' => $httpClientMock, 'requestFactory' => $requestFactoryMock
            ) = $this->createClientMockObjects($responseBody);

        $adapter = new Ip2LocationApiAdapter($httpClientMock, $requestFactoryMock, 'apiKey');

        $location = $adapter->locate(new Ip('8.8.8.8'));

        $this->assertEquals($expectedLocation, $location);
    }

    /**
     * @dataProvider errorResponsesProvider
     *
     * @param string $apiKey
     * @param string $ip
     * @param string $package
     * @param string $addons
     * @param string $message
     */
    public function testError(string $apiKey, string $ip, string $package, string $addons, string $message): void
    {
        $responseBody = "{\"response\":\"$message\"}";
        $addons = explode(',', $addons);
        list(
            'client' => $httpClientMock, 'requestFactory' => $requestFactoryMock
            ) = $this->createClientMockObjects($responseBody);
        $adapter = new Ip2LocationApiAdapter(
            $httpClientMock,
            $requestFactoryMock,
            $apiKey,
            $package,
            'en',
            $addons
        );

        $this->expectException(IPLocationException::class);
        $this->expectExceptionMessage($message);
        $adapter->locate(new Ip($ip));
    }

    public function testNotFoundResult(): void
    {
        $responseBody =
            '{"country_code":"-","country_name":"-","region_name":"-","city_name":"-","latitude":"0","longitude":"0","credits_consumed":3}';

        list(
            'client' => $httpClientMock, 'requestFactory' => $requestFactoryMock
            ) = $this->createClientMockObjects($responseBody);
        $adapter = new Ip2LocationApiAdapter(
            $httpClientMock,
            $requestFactoryMock,
            'apiKey'
        );

        $this->assertNull(
            $adapter->locate(new Ip('240.240.240.240'))
        ); //is a bogon (Reserved (RFC1112, Section 4)) IP address.
    }

    /**
     * @return string[][]
     */
    public function errorResponsesProvider(): array
    {
        return [
            'invalid key' => [
                'wrongKey',
                '8.8.8.8',
                'WS5',
                '',
                'Invalid account.',
            ],
            'key missing' => [
                '',
                '8.8.8.8',
                'WS5',
                '',
                "Permission denied. Please visit https://www.ip2location.com/web-service for details.",
            ],
            'addon incorrect' => [
                'apiKey',
                '8.8.8.8',
                'WS5',
                'incorrect_addon',
                "Invalid addon name.",
            ],
            'addon not available' => [
                'apiKey',
                '8.8.8.8',
                'WS3',
                'geotargeting',
                "Geotargeting addon is not available in WS3 package.",
            ],
        ];
    }
}
