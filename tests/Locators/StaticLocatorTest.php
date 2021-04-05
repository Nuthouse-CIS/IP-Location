<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Locators;

use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Locators\StaticLocator;
use PHPUnit\Framework\TestCase;

class StaticLocatorTest extends TestCase
{
    public function testLocate(): void
    {
        $location = new Location(
            new Country('US', 'USA')
        );
        $locator = new StaticLocator($location);

        $this->assertSame($location, $locator->locate(new Ip('8.8.8.8')));
    }
}
