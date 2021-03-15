<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Decorators;

use JsonSerializable;
use NuthouseCIS\IPLocation\Location\Country;

class CountryJsonDecorator implements JsonSerializable
{
    private Country $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function jsonSerialize(): array
    {
        $data = [
            'isoAlpha2' => $this->country->getIsoAlpha2(),
            'isoAlpha3' => $this->country->getIsoAlpha3(),
            'name' => $this->country->getName()
        ];
        if ($coordinates = $this->country->getCoordinates()) {
            $data['coordinates'] = new CoordinatesJsonDecorator($coordinates);
        }
        return $data;
    }
}
