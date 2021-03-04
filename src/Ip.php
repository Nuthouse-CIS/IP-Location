<?php

declare(strict_types=1);

namespace Seagull4auka\IPLocation;

use Webmozart\Assert\Assert;

final class Ip
{
    private string $value;

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
}
