<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Decorators;

use JsonSerializable;
use NuthouseCIS\IPLocation\Location\Region;

class RegionJsonDecorator implements JsonSerializable
{
    private Region $region;

    public function __construct(Region $region)
    {
        $this->region = $region;
    }

    public function jsonSerialize(): array
    {
        $data = [
            'name' => $this->region->getName(),
            'isoCode' => $this->region->getIsoCode(),
        ];
        if ($coordinates = $this->region->getCoordinates()) {
            $data['coordinates'] = new CoordinatesJsonDecorator($coordinates);
        }
        return $data;
    }
}
