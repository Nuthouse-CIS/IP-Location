<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Location;

class Location
{
    private Country $country;
    private ?Region $region;
    private ?City $city;
    private ?array $extra;

    public function __construct(
        Country $country,
        ?Region $region = null,
        ?City $city = null,
        ?array $extra = null
    ) {
        $this->country = $country;
        $this->region = $region;
        $this->city = $city;
        $this->extra = $extra;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function getExtra(): ?array
    {
        return $this->extra;
    }

    public function getCoordinates(): ?Coordinates
    {
        if (($city = $this->getCity()) !== null && $city->getCoordinates()) {
            return $city->getCoordinates();
        } elseif (
            ($region = $this->getRegion()) !== null
            && $region->getCoordinates()
        ) {
            return $region->getCoordinates();
        }

        return $this->getCountry()->getCoordinates();
    }
}
