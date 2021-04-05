<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Locators;

use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Country;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Locator;
use NuthouseCIS\IPLocation\Locators\CacheLocator;
use NuthouseCIS\IPLocation\Locators\StaticLocator;
use NuthouseCIS\IPLocation\Tests\Infrastructure\MemoryCache;
use PHPUnit\Framework\TestCase;

class CacheLocatorTest extends TestCase
{
    public function testCache(): void
    {
        $cache = new MemoryCache();
        $ip = new Ip('8.8.8.8');
        $locator = new class implements Locator {
            /** @var int[] */
            public array $ipCalls = [];
            public function locate(Ip $ip): ?Location
            {
                if (!isset($this->ipCalls[(string)$ip])) {
                    $this->ipCalls[(string)$ip] = 0;
                }
                $this->ipCalls[(string)$ip]++;
                return new Location(new Country('US'));
            }
        };
        $cacheLocator = new CacheLocator($locator, $cache);
        $location = $cacheLocator->locate($ip);
        $locationFromCache = $cacheLocator->locate($ip);
        $this->assertSame($location, $locationFromCache);
        $this->assertSame(1, $locator->ipCalls[(string)$ip]);
    }

    public function testPrefix(): void
    {
        $cache = new MemoryCache();
        $ip = new Ip('127.0.0.1');
        $staticLocator1 = new StaticLocator($location1 = new Location(new Country('US')));
        $staticLocator2 = new StaticLocator($location2 = new Location(new Country('RU')));

        $cacheLocator1 = new CacheLocator($staticLocator1, $cache, null, 'loc1-');
        $cacheLocator2 = new CacheLocator($staticLocator2, $cache, null, 'loc2-');

        $locationResult1 = $cacheLocator1->locate($ip);
        $locationResult2 = $cacheLocator2->locate($ip);

        $this->assertSame($location1, $locationResult1);
        $this->assertSame($location2, $locationResult2);
    }
}
