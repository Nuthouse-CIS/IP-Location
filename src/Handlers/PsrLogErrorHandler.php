<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Handlers;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class PsrLogErrorHandler implements ErrorHandler
{
    private LoggerInterface $logger;

    /** @var mixed */
    private $level;

    /**
     * @param LoggerInterface $logger
     * @param mixed           $level
     */
    public function __construct(LoggerInterface $logger, $level = LogLevel::ERROR)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    public function handle(Exception $exception): void
    {
        $this->logger->log($this->level, $exception->getMessage(), ['exception' => $exception]);
    }
}
