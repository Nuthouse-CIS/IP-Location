<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Decorators;

use JsonSerializable;
use NuthouseCIS\IPLocation\Location\Location;

class LocationJsonDecorator implements JsonSerializable
{
    private Location $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    public function jsonSerialize(): array
    {
        $data = [
            'country' => new CountryJsonDecorator($this->location->getCountry())
        ];
        if ($region = $this->location->getRegion()) {
            $data['region'] = new RegionJsonDecorator($region);
        }
        if ($city = $this->location->getCity()) {
            $data['city'] = new CityJsonDecorator($city);
        }
        if ($coordinates = $this->location->getCoordinates()) {
            $data['coordinates'] = new CoordinatesJsonDecorator($coordinates);
        }
        if (($extra = $this->location->getExtra()) !== null) {
            $data['extra'] = $extra;
        }
        return $data;
    }
}
