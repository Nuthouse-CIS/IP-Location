<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators;

use Exception;
use NuthouseCIS\IPLocation\Handlers\ErrorHandler;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Location;
use NuthouseCIS\IPLocation\Locator;

class MuteLocator implements Locator
{
    private Locator $next;
    private ?ErrorHandler $errorHandler;

    public function __construct(Locator $next, ?ErrorHandler $errorHandler = null)
    {
        $this->next = $next;
        $this->errorHandler = $errorHandler;
    }

    public function locate(Ip $ip): ?Location
    {
        try {
            return $this->next->locate($ip);
        } catch (Exception $e) {
            if ($this->errorHandler) {
                $this->errorHandler->handle($e);
            }

            return null;
        }
    }
}
