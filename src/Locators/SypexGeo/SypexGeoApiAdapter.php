<?php

declare(strict_types=1);

namespace NuthouseCIS\IPLocation\Locators\SypexGeo;

use NuthouseCIS\IPLocation\Exception\IPLocationException;
use NuthouseCIS\IPLocation\Ip;
use NuthouseCIS\IPLocation\Location\Location;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class SypexGeoApiAdapter extends AbstractSypexGeoAdapter
{
    protected ClientInterface $client;
    protected RequestFactoryInterface $requestFactory;
    protected string $server;
    protected ?string $apiKey;

    /**
     * SypexGeoApiAdapter constructor.
     *
     * @param ClientInterface $client
     * @param RequestFactoryInterface $requestFactory
     * @param string $server Possible API servers listed in NuthouseCIS\IPLocation\Locators\SypexGeo\ApiServer
     * @param string|null $apiKey
     *
     * @see ApiServer
     */
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        string $server = ApiServer::SERVER_GEODNS,
        ?string $apiKey = null
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->server = $server;
        $this->apiKey = $apiKey;
    }

    /**
     * @inheritDoc
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public function locate(Ip $ip): ?Location
    {
        $body = $this->request($ip);

        return $this->parseBody($body);
    }

    private function request(Ip $ip): array
    {
        $uri = sprintf(
            'https://%s/%s/json/%s',
            $this->server,
            $this->apiKey ?: '',
            (string)$ip
        );
        $request = $this->requestFactory->createRequest('GET', $uri);

        $response = $this->client->sendRequest($request);
        /**
         * @psalm-var array $body
         * @psalm-suppress MixedAssignment
         */
        $body = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() === 200 && isset($body['ip'])) {
            return $body;
        } elseif (!empty($body['error'])) {
            throw new IPLocationException((string)$body['error']);
        } else {
            throw new IPLocationException(
                'The SypexGeo service responded with an unknown result'
            );
        }
    }
}
