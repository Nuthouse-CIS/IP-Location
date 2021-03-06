<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Handlers;

use Exception;

interface ErrorHandler
{
    public function handle(Exception $exception): void;
}
