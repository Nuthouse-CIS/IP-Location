<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\Infrastructure;

use DateInterval;
use DateTimeImmutable;
use Psr\SimpleCache\CacheInterface;

class MemoryCache implements CacheInterface
{
    public array $storage = [];

    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            /** @psalm-var array{expires: null|\DateTimeImmutable, value: mixed} $storageItem */
            $storageItem = $this->storage[$key];
            if ($storageItem['expires'] === null || $storageItem['expires'] > (new DateTimeImmutable())) {
                return $storageItem['value'];
            }
        }
        return $default;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        $expires = null;
        if ($ttl !== null) {
            if ($ttl instanceof DateInterval) {
                $expires = (new DateTimeImmutable())->add($ttl);
            } else {
                $expires = (new DateTimeImmutable())->add(new DateInterval("PT{$ttl}S"));
            }
        }
        $this->storage[$key] = [
            'value' => $value,
            'expires' => $expires,
        ];
        return true;
    }

    /**
     * @inheritdoc
     */
    public function delete($key): bool
    {
        if ($this->has($key)) {
            unset($key);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function clear(): bool
    {
        $this->storage = [];
        return true;
    }

    /**
     * @inheritdoc
     * @psalm-suppress MixedAssignment, MixedArgument
     */
    public function getMultiple($keys, $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $this->get($key, $default);
        }
    }

    /**
     * @inheritdoc
     * @psalm-suppress MixedAssignment, MixedArgument
     */
    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    /**
     * @inheritdoc
     * @psalm-suppress MixedAssignment, MixedArgument
     */
    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function has($key): bool
    {
        return isset($this->storage[$key]);
    }
}
