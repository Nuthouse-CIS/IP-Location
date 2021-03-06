<?php

declare(strict_types=1);

namespace Seagull4auka\IPLocation\Location;

class City
{
    private string $name;
    private ?Coordinates $coordinates;

    public function __construct(string $name, ?Coordinates $coordinates = null)
    {
        $this->name = $name;
        $this->coordinates = $coordinates;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCoordinates(): ?Coordinates
    {
        return $this->coordinates;
    }
}
