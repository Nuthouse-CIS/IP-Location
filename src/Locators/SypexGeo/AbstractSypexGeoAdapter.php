<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators\SypexGeo;

use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Coordinates;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Location\Region;
use NuthouseCIS\IPLocation\Locator;

abstract class AbstractSypexGeoAdapter implements Locator
{
    /**
     * @param array $body
     *
     * @psalm-param array{
     *   city: null|array{
     *     id: int,
     *     lat: float,
     *     lon: float,
     *     name_en: string
     *   },
     *   region: null|array{
     *     id: int,
     *     name_en: string,
     *     iso: string,
     *     lat?: float,
     *     lon?: float,
     *   },
     *   country: null|array{
     *     id: int,
     *     iso: string,
     *     lat: float,
     *     lon: float,
     *     name_en: string
     *   }
     * } $body
     *
     * @return Location|null
     */
    protected function parseBody(array $body): ?Location
    {
        if (!empty($body['country']['name_en'])) {
            $city = $region = null;
            if (!empty($body['city']['name_en'])) {
                $city = new City(
                    $body['city']['name_en'],
                    new Coordinates($body['city']['lat'], $body['city']['lon'])
                );
                unset($body['city']['name_en'], $body['city']['lat'], $body['city']['lon']);
            }

            if (!empty($body['region']['name_en'])) {
                $region = new Region(
                    $body['region']['name_en'],
                    $body['region']['iso'],
                    isset($body['region']['lat'], $body['region']['lon']) ? new Coordinates(
                        $body['region']['lat'],
                        $body['region']['lon']
                    ) : null
                );
                unset(
                    $body['region']['name_en'],
                    $body['region']['iso'],
                    $body['region']['lat'],
                    $body['region']['lon']
                );
            }

            $country = new Country(
                $body['country']['iso'],
                null,
                $body['country']['name_en'],
                new Coordinates($body['country']['lat'], $body['country']['lon'])
            );
            unset(
                $body['country']['name_en'],
                $body['country']['iso'],
                $body['country']['lat'],
                $body['country']['lon']
            );

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
