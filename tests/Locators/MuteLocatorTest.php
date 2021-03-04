<?php

namespace Seagull4auka\IPLocation\Tests\Locators;

use Exception;
use PHPUnit\Framework\TestCase;
use Seagull4auka\IPLocation\Handlers\ErrorHandler;
use Seagull4auka\IPLocation\Ip;
use Seagull4auka\IPLocation\Locator;
use Seagull4auka\IPLocation\Locators\MuteLocator;

class MuteLocatorTest extends TestCase
{
    public function testMute(): void
    {
        $exception = new Exception('');
        $nextLocator = $this->createMock(Locator::class);
        $nextLocator->method('locate')
            ->willThrowException($exception);

        $errorHandler = new class implements ErrorHandler {
            public function handle(Exception $exception): void
            {
            }
        };

        $locator = new MuteLocator($nextLocator, $errorHandler);
        try {
            $locator->locate(new Ip('8.8.8.8'));
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail('Threw an exception');
        }
    }
}
