<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Decorators;

use JsonSerializable;
use NuthouseCIS\IPLocation\Location\City;

class CityJsonDecorator implements JsonSerializable
{
    private City $city;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    public function jsonSerialize(): array
    {
        $data = [
            'name' => $this->city->getName()
        ];
        if ($coordinates = $this->city->getCoordinates()) {
            $data['coordinates'] = new CoordinatesJsonDecorator($coordinates);
        }
        return $data;
    }
}
