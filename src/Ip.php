<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation;

use Webmozart\Assert\Assert;

final class Ip
{
    private string $value;
    private ?bool $isVersion4 = null;
    private ?bool $isVersion6 = null;

    public function __construct(string $value)
    {
        Assert::ip($value);
        $this->value = $value;
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
        if ($this->isVersion4 === null) {
            $this->isVersion4 = filter_var(
                $this->value,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_IPV4
            ) !== false;
        }

        return $this->isVersion4;
    }

    public function isVersion6(): bool
    {
        if ($this->isVersion6 === null) {
            $this->isVersion6 = filter_var(
                $this->value,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_IPV6
            ) !== false;
        }

        return $this->isVersion6;
    }
}
