<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Location;

class Region
{
    private string $name;
    private ?string $isoCode;
    private ?Coordinates $coordinates;

    public function __construct(
        string $name,
        ?string $isoCode = null,
        ?Coordinates $coordinates = null
    ) {
        $this->name = $name;
        $this->isoCode = $isoCode;
        $this->coordinates = $coordinates;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function getCoordinates(): ?Coordinates
    {
        return $this->coordinates;
    }
}
