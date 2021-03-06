<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Location;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

class Country
{
    private ?string $isoAlpha2;
    private ?string $isoAlpha3;
    private ?string $name;
    private ?Coordinates $coordinates;

    public function __construct(
        ?string $isoAlpha2,
        ?string $isoAlpha3 = null,
        ?string $name = null,
        ?Coordinates $coordinates = null
    ) {
        Assert::nullOrLength($isoAlpha2, 2);
        Assert::nullOrLength($isoAlpha3, 3);
        Assert::nullOrMinLength($name, 1);

        if ($isoAlpha2 === null && $isoAlpha3 === null && $name === null) {
            throw new InvalidArgumentException(
                'At least one of the alpha2 or alpha3 or name arguments must be set'
            );
        }

        if ($isoAlpha2 !== null) {
            $isoAlpha2 = mb_strtoupper($isoAlpha2);
        }
        if ($isoAlpha3 !== null) {
            $isoAlpha3 = mb_strtoupper($isoAlpha3);
        }

        $this->isoAlpha2 = $isoAlpha2;
        $this->isoAlpha3 = $isoAlpha3;
        $this->name = $name;
        $this->coordinates = $coordinates;
    }

    public function getIsoAlpha2(): ?string
    {
        return $this->isoAlpha2;
    }

    public function getIsoAlpha3(): ?string
    {
        return $this->isoAlpha3;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCoordinates(): ?Coordinates
    {
        return $this->coordinates;
    }
}
