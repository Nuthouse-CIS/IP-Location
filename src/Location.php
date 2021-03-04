<?php

declare(strict_types=1);

namespace Seagull4auka\IPLocation;

class Location
{
    private string $country;
    private ?string $region;
    private ?string $city;

    public function __construct(string $country, ?string $region, ?string $city)
    {
        $this->country = $country;
        $this->region = $region;
        $this->city = $city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }
}
