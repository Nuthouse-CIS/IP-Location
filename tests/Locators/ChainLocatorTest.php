<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Locators;

use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\City;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Location\Region;
use NuthouseCIS\IPLocation\Locators\ChainLocator;
use NuthouseCIS\IPLocation\Locators\StaticLocator;
use PHPUnit\Framework\TestCase;

class ChainLocatorTest extends TestCase
{
    public function testLocate(): void
    {
        $ip = new Ip('8.8.8.8');
        $country = new Country('US');
        $region = new Region('Florida');
        $city = new City('Mountain View');
        $locatorCountry = new StaticLocator(
            $locationCountry = new Location($country)
        );
        $locatorCountry2 = new StaticLocator(
            $locationCountry2 = new Location($country)
        );
        $locatorRegion = new StaticLocator(
            $locationRegion = new Location($country, $region)
        );
        $locatorRegion2 = new StaticLocator(
            $locationRegion2 = new Location($country, $region)
        );
        $locatorCity = new StaticLocator(
            $locationCity = new Location($country, null, $city)
        );

        $chainLocatorLast = new ChainLocator(
            $locatorCountry,
            $locatorRegion,
            $locatorCity
        );
        $chainLocatorMiddle = new ChainLocator(
            $locatorCountry,
            $locatorCity,
            $locatorRegion
        );
        $chainLocatorFirst = new ChainLocator(
            $locatorCity,
            $locatorCountry,
            $locatorRegion
        );
        $chainLocatorRegion = new ChainLocator(
            $locatorCountry,
            $locatorRegion
        );
        $chainLocatorCountryFirst = new ChainLocator(
            $locatorCountry,
            $locatorCountry2
        );
        $chainLocatorRegionFirst = new ChainLocator(
            $locatorRegion,
            $locatorRegion2
        );

        $this->assertSame($locationCity, $chainLocatorLast->locate($ip));
        $this->assertSame($locationCity, $chainLocatorMiddle->locate($ip));
        $this->assertSame($locationCity, $chainLocatorFirst->locate($ip));

        $this->assertSame($locationRegion, $chainLocatorRegion->locate($ip));

        $this->assertSame(
            $locationCountry,
            $chainLocatorCountryFirst->locate($ip)
        );
        $this->assertSame(
            $locationRegion,
            $chainLocatorRegionFirst->locate($ip)
        );
    }
}
