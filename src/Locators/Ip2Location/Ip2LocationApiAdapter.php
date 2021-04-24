<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators\Ip2Location;

use NuthouseCIS\IPLocation\Exception\IPLocationException;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Location\Region;
use NuthouseCIS\IPLocation\Locator;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * Class Ip2LocationApiAdapter
 *
 * @psalm-type ApiReturn array{
 *   country_code: string,
 *   credits_consumed: int,
 *   country_name?: string,
 *   region_name?: string,
 *   city_name?: string,
 *   latitude?: string,
 *   longitude?: string,
 *   area_code?: string,
 *   domain?: string,
 *   elevation?: string,
 *   idd_code?: string,
 *   isp?: string,
 *   mcc?: string,
 *   mnc?: string,
 *   mobile_brand?: string,
 *   net_speed?: string,
 *   time_zone?: string,
 *   usage_type?: string,
 *   weather_station_code?: string,
 *   weather_station_name?: string,
 *   zip_code?: string,
 *   country?: array{
 *     alpha3_code: string,
 *     capital: string,
 *     demonym: string,
 *     flag: string,
 *     idd_code: string,
 *     is_eu: bool,
 *     name: string,
 *     numeric_code: string,
 *     population: string,
 *     tld: string,
 *     total_area: string,
 *     currency: array{
 *       code: string,
 *       name: string,
 *       symbol: string,
 *     },
 *     language: array{code: string, name: string},
 *     translations: array<string, string>,
 *   },
 *   continent?: array{
 *     code: string,
 *     name: string,
 *     hemisphere: array<string>,
 *     translations: array<string, string>,
 *   },
 *   region?: array{
 *     code: string,
 *     name: string,
 *     translations: array<string, string>,
 *   },
 *   time_zone_info?: array{
 *     current_time: string,
 *     gmt_offset: int,
 *     is_dst: string,
 *     olson: string,
 *     sunrise: string,
 *     sunset: string,
 *   },
 *   geotargeting?: array{metro: string},
 *   country_groupings?: list<array{acronym: string, name: string}>,
 *   city?: array{name: string, translations: array<string, string>},
 * }
 * @package NuthouseCIS\IPLocation\Locators\Ip2Location
 * @link https://www.ip2location.com/docs/ws1-user-manual.pdf
 */
class Ip2LocationApiAdapter implements Locator
{
    protected ClientInterface $client;
    protected RequestFactoryInterface $requestFactory;
    protected string $apiKey;
    protected string $package;
    protected string $lang;
    protected array $addons;
    protected string $baseUrl;

    /**
     * Ip2LocationApiAdapter constructor.
     *
     * @param ClientInterface $client
     * @param RequestFactoryInterface $requestFactory
     * @param string $apiKey
     * @param string $package
     * @param string $lang
     * @param array $addons
     * @param string $baseUrl
     */
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        string $apiKey,
        string $package = 'WS5',
        string $lang = 'en',
        array $addons = [],
        string $baseUrl = 'https://api.ip2location.com/v2/'
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->apiKey = $apiKey;
        $this->package = $package;
        $this->lang = $lang;
        $this->addons = $addons;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function locate(Ip $ip): ?Location
    {
        $body = $this->request($ip);

        return $this->parseBody($body);
    }

    /**
     * @param Ip $ip
     *
     * @return array
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-return ApiReturn
     * @throws ClientExceptionInterface
     */
    protected function request(Ip $ip): array
    {
        $query = [
            'key' => $this->apiKey,
            'ip' => (string)$ip,
            'lang' => $this->lang,
            'package' => $this->package,
        ];
        if ($this->addons) {
            $query['addon'] = implode(',', $this->addons);
        }
        $uri = $this->baseUrl . '?' . http_build_query($query);

        $request = $this->requestFactory->createRequest('GET', $uri);

        $response = $this->client->sendRequest($request);


        if (
            $response->getStatusCode() !== 200
            || !is_array(
                $data = json_decode(
                    $response->getBody()->getContents(),
                    true
                )
            )
        ) {
            throw new IPLocationException("Service return unhandled response");
        }
        /**
         * @psalm-var ApiReturn|array{response: string} $data
         */
        if (isset($data['response'])) {
            throw new IPLocationException($data['response']);
        }

        /** @psalm-suppress LessSpecificReturnStatement */
        return $data;
    }

    /**
     * @param array $body
     *
     * @return Location|null
     *
     * @psalm-param ApiReturn $body
     * @psalm-suppress PossiblyUndefinedArrayOffset
     */
    protected function parseBody(array $body): ?Location
    {
        /** @psalm-var ApiReturn $body */
        $body = $this->filterBody($body);

        if (isset($body['country_code'])) {
            $region = $city = $coordinates = null;
            if (isset($body['latitude']) && isset($body['longitude'])) {
                $coordinates = new Coordinates(
                    (float)$body['latitude'],
                    (float)$body['longitude']
                );
                unset($body['latitude'], $body['longitude']);
            }
            if (isset($body['city_name'])) {
                $city = new City(
                    $body['city_name'],
                    $coordinates
                );
                $coordinates = null;
                unset($body['city_name']);
            }
            if (isset($body['region_name'])) {
                //Addon `region` have field code, but on tests it returns not ISO3166-2
                $region = new Region(
                    $body['region_name']
                );
                unset($body['region_name']);
            }
            $country = new Country(
                $body['country_code'],
                $body['country']['alpha3_code'] ?? null,
                $body['country_name'] ?? null,
                $coordinates
            );
            unset($body['country_code'], $body['country']['alpha3_code'], $body['country_name']);

            return new Location(
                $country,
                $region,
                $city,
                $body
            );
        }

        return null;
    }

    /**
     * @param array $body
     *
     * @return array
     * @template T
     * @psalm-param array<array-key, T> $body
     */
    protected function filterBody(array $body): array
    {
        foreach ($body as &$item) {
            if (is_array($item)) {
                $item = $this->filterBody($item);
            }
        }

        return array_filter(
            $body,
            static fn($value) => is_array($value)
                ? (bool)count($value)
                : ($value !== '' && $value !== '-' && $value !== '0')
        );
    }
}
