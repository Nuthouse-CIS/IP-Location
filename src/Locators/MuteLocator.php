<?php

declare(strict_types=1);

namespace Seagull4auka\IPLocation\Locators;

use Exception;
use Seagull4auka\IPLocation\Handlers\ErrorHandler;
use Seagull4auka\IPLocation\Ip;
use Seagull4auka\IPLocation\Location;
use Seagull4auka\IPLocation\Locator;

class MuteLocator implements Locator
{
    private Locator $next;
    private ErrorHandler $errorHandler;

    public function __construct(Locator $next, ErrorHandler $errorHandler)
    {
        $this->next = $next;
        $this->errorHandler = $errorHandler;
    }

    public function locate(Ip $ip): ?Location
    {
        try {
            return $this->next->locate($ip);
        } catch (Exception $e) {
            $this->errorHandler->handle($e);

            return null;
        }
    }
}
