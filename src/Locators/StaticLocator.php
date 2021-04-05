<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators;

use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Locator;

class StaticLocator implements Locator
{
    private Location $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    public function locate(Ip $ip): Location
    {
        return $this->location;
    }
}
