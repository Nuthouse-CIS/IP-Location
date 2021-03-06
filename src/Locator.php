<?php

declare(strict_types=1);

namespace Seagull4auka\IPLocation;

use Seagull4auka\IPLocation\Location\Location;

interface Locator
{
    public function locate(Ip $ip): ?Location;
}
