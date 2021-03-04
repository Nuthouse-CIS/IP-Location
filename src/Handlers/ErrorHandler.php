<?php

declare(strict_types=1);

namespace Seagull4auka\IPLocation\Handlers;

use Exception;

interface ErrorHandler
{
    public function handle(Exception $exception): void;
}
