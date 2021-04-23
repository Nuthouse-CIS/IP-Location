<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation;

use Webmozart\Assert\Assert;

final class Ip
{
    private string $value;
    private int $version;

    public function __construct(string $value)
    {
        Assert::ip($value);
        $this->value = $value;
        $this->version = filter_var(
            $this->value,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4
        ) !== false ? 4 : 6;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function isVersion4(): bool
    {
        return $this->version === 4;
    }

    public function isVersion6(): bool
    {
        return $this->version === 6;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }
}
