<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation;

use NuthouseCIS\IPLocation\Location\Location;

interface Locator
{
    public function locate(Ip $ip): ?Location;
}
