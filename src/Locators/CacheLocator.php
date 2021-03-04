<?php

declare(strict_types=1);

namespace Seagull4auka\IPLocation\Locators;

use Psr\SimpleCache\CacheInterface;
use Seagull4auka\IPLocation\Ip;
use Seagull4auka\IPLocation\Location;
use Seagull4auka\IPLocation\Locator;

class CacheLocator implements Locator
{
    private Locator $next;
    private CacheInterface $cache;
    private int $ttl;
    private string $prefix;

    public function __construct(Locator $next, CacheInterface $cache, int $ttl, string $prefix = 'location-')
    {
        $this->next = $next;
        $this->cache = $cache;
        $this->ttl = $ttl;
        $this->prefix = $prefix;
    }

    public function locate(Ip $ip): ?Location
    {
        $key = $this->prefix . $ip;

        /** @var Location|null $location */
        $location = $this->cache->get($key);

        if ($location === null) {
            $location = $this->next->locate($ip);
            $this->cache->set($key, $location, $this->ttl);
        }
        return $location;
    }
}
