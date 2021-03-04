<?php

declare(strict_types=1);

namespace Seagull4auka\IPLocation;

interface Locator
{
    public function locate(Ip $ip): ?Location;
}
