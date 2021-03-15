<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Locators\IpGeoLocationIo;

use NuthouseCIS\IPLocation\Exception\IPLocationException;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Locators\IpGeoLocationIo\IpGeoLocationIoAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class IpGeoLocationIoAdapterTest extends TestCase
{
    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function testSuccess(): void
    {
        $responseBody = '{"ip":"8.8.8.8","continent_code":"NA","continent_name":"North America","country_code2":"US","country_code3":"USA","country_name":"United States","country_capital":"Washington, D.C.","state_prov":"Florida","district":"Orange County","city":"Mountain View","zipcode":"32830","latitude":"28.35753","longitude":"-81.55827","is_eu":false,"calling_code":"+1","country_tld":".us","languages":"en-US,es-US,haw,fr","country_flag":"https://ipgeolocation.io/static/flags/us_64.png","geoname_id":"6949555","isp":"Google LLC","connection_type":"","organization":"Google LLC","currency":{"code":"USD","name":"US Dollar","symbol":"$"},"time_zone":{"name":"America/New_York","offset":-5,"current_time":"2021-03-09 12:09:30.479-0500","current_time_unix":1615309770.479,"is_dst":false,"dst_savings":1}}';
        $locationExtra = [
            "ip" => "8.8.8.8",
            "continent_code" => "NA",
            "continent_name" => "North America",
            "country_capital" => "Washington, D.C.",
            "district" => "Orange County",
            "zipcode" => "32830",
            "is_eu" => false,
            "calling_code" => "+1",
            "country_tld" => ".us",
            "languages" => "en-US,es-US,haw,fr",
            "country_flag" => "https://ipgeolocation.io/static/flags/us_64.png",
            "geoname_id" => "6949555",
            "isp" => "Google LLC",
            "connection_type" => "",
            "organization" => "Google LLC",
            "currency" => [
                "code" => "USD",
                "name" => "US Dollar",
                "symbol" => "$"
            ],
            "time_zone" => [
                "name" => "America/New_York",
                "offset" => -5,
                "current_time" => "2021-03-09 12:09:30.479-0500",
                "current_time_unix" => 1615309770.479,
                "is_dst" => false,
                "dst_savings" => 1
            ]
        ];

        $requestMock = $this->createMock(RequestInterface::class);
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($responseBody);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);
        $responseMock->method('getStatusCode')->willReturn(200);

        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $requestFactoryMock->method('createRequest')->willReturn($requestMock);

        $httpClientMock = $this->createMock(ClientInterface::class);
        $httpClientMock->method('sendRequest')->willReturn($responseMock);

        $adapter = new IpGeoLocationIoAdapter($httpClientMock, $requestFactoryMock, 'apiKey');
        $location = $adapter->locate(new Ip('8.8.8.8'));

        $this->assertInstanceOf(Location::class, $location);
        $this->assertSame('US', $location->getCountry()->getIsoAlpha2());
        $this->assertSame('USA', $location->getCountry()->getIsoAlpha3());
        $this->assertSame('United States', $location->getCountry()->getName());
        $this->assertNull($location->getCountry()->getCoordinates());
        $this->assertSame(28.35753, $location->getCoordinates()->getLatitude());
        $this->assertSame(-81.55827, $location->getCoordinates()->getLongitude());
        $this->assertSame('Florida', $location->getRegion()->getName());
        $this->assertNull($location->getRegion()->getCoordinates());
        $this->assertSame('Mountain View', $location->getCity()->getName());
        $this->assertSame($location->getCoordinates(), $location->getCity()->getCoordinates());
        $this->assertSame($locationExtra, $location->getExtra());
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
                'Provided API key is not valid. Contact technical support for assistance at support@ipgeolocation.io'
            ],
            'key missing' => [
                '',
                '8.8.8.8',
                "Please provide an API key (as 'apiKey=YOUR_API_KEY' URL parameter) to use IPGeolocation API. To get your free API Key, sign up at https://ipgeolocation.io/signup.html"
            ],
            'ip incorrect' => [
                'apiKey',
                $ip = '0.0.0.0',
                "'{$ip}' is a bogon (This host is on this network (RFC1122, Section 3.2.1.3)) IP address."
            ]
        ];
    }

    /**
     * @dataProvider errorResponsesProvider
     *
     * @param string $apiKey
     * @param string $ip
     * @param string $message
     */
    public function testError(string $apiKey, string $ip, string $message): void
    {
        $responseBody = "{\"message\":\"{$message}\"}";

        $requestMock = $this->createMock(RequestInterface::class);
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($responseBody);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);
        $responseMock->method('getStatusCode')->willReturn(200);

        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $requestFactoryMock->method('createRequest')->willReturn($requestMock);

        $httpClientMock = $this->createMock(ClientInterface::class);
        $httpClientMock->method('sendRequest')->willReturn($responseMock);
        $adapter = new IpGeoLocationIoAdapter($httpClientMock, $requestFactoryMock, $apiKey);

        $this->expectException(IPLocationException::class);
        $this->expectExceptionMessage($message);
        $adapter->locate(new Ip($ip));
    }

    public function testUnknownResult(): void
    {
        $responseBody = "unhandled response";

        $requestMock = $this->createMock(RequestInterface::class);
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($responseBody);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);
        $responseMock->method('getStatusCode')->willReturn(200);

        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $requestFactoryMock->method('createRequest')->willReturn($requestMock);

        $httpClientMock = $this->createMock(ClientInterface::class);
        $httpClientMock->method('sendRequest')->willReturn($responseMock);
        $adapter = new IpGeoLocationIoAdapter($httpClientMock, $requestFactoryMock, 'apiKey');

        $this->expectException(IPLocationException::class);
        $this->expectExceptionMessage('The IpGeoLocationIo service responded with an unknown result');
        $adapter->locate(new Ip('8.8.8.8'));
    }
}
