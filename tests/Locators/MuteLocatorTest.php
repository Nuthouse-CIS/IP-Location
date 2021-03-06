<?php

namespace NuthouseCIS\IPLocation\Tests\Locators;

use Exception;
use NuthouseCIS\IPLocation\Handlers\ErrorHandler;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Locator;
use NuthouseCIS\IPLocation\Locators\MuteLocator;
use PHPUnit\Framework\TestCase;

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
