<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators\IpGeoLocationIo;

use NuthouseCIS\IPLocation\Exception\IPLocationException;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Location\Region;
use NuthouseCIS\IPLocation\Locator;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class IpGeoLocationIoAdapter implements Locator
{
    protected ClientInterface $client;
    protected RequestFactoryInterface $requestFactory;
    protected string $apiKey;
    protected string $lang;
    protected string $baseUrl;
    protected ?array $fields;
    protected ?array $excludes;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        string $apiKey,
        string $lang = 'en',
        ?array $fields = ['geo'],
        ?array $excludes = null,
        string $baseUrl = 'https://api.ipgeolocation.io/ipgeo'
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->apiKey = $apiKey;
        $this->lang = $lang;
        $this->baseUrl = $baseUrl;
        $this->fields = $fields;
        $this->excludes = $excludes;
    }

    public function locate(Ip $ip): ?Location
    {
        $body = $this->handleRequest($ip);

        /** @psalm-suppress MixedArgumentTypeCoercion */
        return $this->handleBody($body);
    }

    /**
     * @param Ip $ip
     *
     * @return array
     * @throws IPLocationException
     */
    protected function handleRequest(Ip $ip): array
    {
        $query = [
            'apiKey' => $this->apiKey,
            'ip'     => (string)$ip,
            'lang'   => $this->lang,
        ];
        if ($this->fields) {
            $query['fields'] = implode(',', $this->fields);
        }
        if ($this->excludes) {
            $query['excludes'] = implode(',', $this->excludes);
        }
        $uri = $this->baseUrl . '?' . http_build_query($query);

        $request = $this->requestFactory->createRequest('GET', $uri);

        $response = $this->client->sendRequest($request);
        /**
         * @var array $body
         * @psalm-suppress MixedAssignment
         */
        $body = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() === 200 && isset($body['ip'])) {
            return $body;
        } elseif (isset($body['message'])) {
            throw new IPLocationException((string)$body['message']);
        } else {
            throw new IPLocationException(
                'The IpGeoLocationIo service responded with an unknown result'
            );
        }
    }

    /**
     * @param array $body
     *
     * @psalm-param array{
     * ip: string,
     * country_code2?: string,
     * country_code3?: string,
     * country_name?: string,
     * city?: string,
     * state_prov?: string,
     * latitude?: string,
     * longitude?: string,
     * } $body
     *
     * @return Location|null
     */
    protected function handleBody(array $body): ?Location
    {
        if (
            isset($body['country_code2'])
            || isset($body['country_code3'])
            || isset($body['country_name'])
        ) {
            $city = $region = $coords = null;
            if (isset($body['latitude']) && (float)$body['longitude']) {
                $coords = new Coordinates(
                    (float)$body['latitude'],
                    (float)$body['longitude']
                );
            }
            if (isset($body['city'])) {
                $city = new City($body['city'], $coords);
                $coords = null;
            }
            if (isset($body['state_prov'])) {
                $region = new Region(
                    $body['state_prov'],
                    null,
                    $coords
                );
                $coords = null;
            }
            $country = new Country(
                $body['country_code2'] ?? null,
                $body['country_code3'] ?? null,
                $body['country_name'] ?? null,
                $coords
            );

            foreach (
                [
                    'country_code2',
                    'country_code3',
                    'country_name',
                    'latitude',
                    'longitude',
                    'city',
                    'state_prov',
                ] as $key
            ) {
                if (isset($body[$key])) {
                    unset($body[$key]);
                }
            }

            return new Location(
                $country,
                $region,
                $city,
                $body
            );
        }

        return null;
    }
}
