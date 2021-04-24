<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation;

use NuthouseCIS\IPLocation\Location\Location;

interface Locator
{
    /**
     * @param Ip $ip
     *
     * @return Location|null
     */
    public function locate(Ip $ip): ?Location;
}
