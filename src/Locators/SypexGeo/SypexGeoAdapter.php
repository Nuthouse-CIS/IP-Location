<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators\SypexGeo;

use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\SxGeo\SxGeo;

class SypexGeoAdapter extends AbstractSypexGeoAdapter
{
    protected SxGeo $database;

    public function __construct(SxGeo $database)
    {
        $this->database = $database;
    }

    /**
     * @inheritDoc
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public function locate(Ip $ip): ?Location
    {
        /**
         * @var false|array $sxGeoData
         */
        $sxGeoData = $this->database->getCityFull((string)$ip);
        if ($sxGeoData) {
            return $this->parseBody($sxGeoData);
        }

        return null;
    }
}
