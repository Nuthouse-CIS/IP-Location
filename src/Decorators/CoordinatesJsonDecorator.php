<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Decorators;

use JsonSerializable;
use NuthouseCIS\IPLocation\Location\Coordinates;

class CoordinatesJsonDecorator implements JsonSerializable
{
    private Coordinates $coordinates;

    public function __construct(Coordinates $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    public function jsonSerialize(): array
    {
        return [
            'latitude' => $this->coordinates->getLatitude(),
            'longitude' => $this->coordinates->getLongitude()
        ];
    }
}
