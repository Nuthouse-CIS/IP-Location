<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators;

use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Locator;

class ChainLocator implements Locator
{
    /**
     * @var Locator[]
     */
    private array $locators;

    public function __construct(Locator ...$locators)
    {
        $this->locators = $locators;
    }

    public function locate(Ip $ip): ?Location
    {
        $result = null;
        foreach ($this->locators as $locator) {
            $location = $locator->locate($ip);
            if ($location === null) {
                continue;
            }
            if ($location->getCity() !== null) {
                return $location;
            }
            if ($result === null && $location->getRegion() !== null) {
                $result = $location;
                continue;
            }
            if ($result === null || $result->getRegion() === null) {
                $result = $location;
            }
        }
        return $result;
    }
}
