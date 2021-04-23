<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Tests\MockTraits;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Trait HttpClientTrait
 *
 * @package NuthouseCIS\IPLocation\Tests\MockTraits
 */
trait HttpClientTrait
{
    /**
     * Returns a mock object for the specified class.
     *
     * @psalm-template RealInstanceType of object
     * @psalm-param class-string<RealInstanceType> $originalClassName
     * @psalm-return MockObject&RealInstanceType
     */
    abstract protected function createMock(string $originalClassName): MockObject;

    /**
     * Create Mock objects of ClientInterface & RequestFactoryInterface
     *
     * @param string $responseBody
     * @param int $responseCode
     *
     * @return array
     * @psalm-return array{client: ClientInterface, requestFactory: RequestFactoryInterface}
     */
    public function createClientMockObjects(string $responseBody = '', int $responseCode = 200): array
    {
        $requestMock = $this->createMock(RequestInterface::class);
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($responseBody);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);
        $responseMock->method('getStatusCode')->willReturn($responseCode);

        $requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $requestFactoryMock->method('createRequest')->willReturn($requestMock);

        $httpClientMock = $this->createMock(ClientInterface::class);
        $httpClientMock->method('sendRequest')->willReturn($responseMock);

        return [
            'client' => $httpClientMock,
            'requestFactory' => $requestFactoryMock,
        ];
    }
}
