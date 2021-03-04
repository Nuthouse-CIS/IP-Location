<?php

declare(strict_types=1);

namespace Seagull4auka\IPLocation\Handlers;

use Exception;
use Psr\Log\LoggerInterface;

class PsrLogErrorHandler implements ErrorHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Exception $exception): void
    {
        $this->logger->error($exception->getMessage(), ['exception' => $exception]);
    }
}
