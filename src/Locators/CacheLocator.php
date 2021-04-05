<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators;

use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Locator;
use Psr\SimpleCache\CacheInterface;

class CacheLocator implements Locator
{
    private Locator $next;
    private CacheInterface $cache;
    private ?int $ttl;
    private string $prefix;

    public function __construct(
        Locator $next,
        CacheInterface $cache,
        ?int $ttl = null,
        string $prefix = 'location-'
    ) {
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
