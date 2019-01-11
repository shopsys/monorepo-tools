<?php

declare(strict_types=1);

namespace Shopsys\Releaser\Guzzle;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Nette\Utils\Json;
use function GuzzleHttp\Promise\unwrap;

final class ApiCaller
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $url
     * @return mixed[]
     */
    public function sendGetToJsonArray(string $url): array
    {
        $request = new Request('GET', $url);
        $response = $this->client->send($request);

        $json = $response->getBody()->getContents();
        return Json::decode($json, Json::FORCE_ARRAY);
    }

    /**
     * @param string[] $urls
     * @return string[]
     */
    public function sendGetsAsyncToStrings(array $urls)
    {
        $promises = [];

        foreach ($urls as $url) {
            $request = new Request('GET', $url);
            $promises[] = $this->client->sendAsync($request);
        }

        // Wait on all of the requests to complete. Throws a ConnectException if any of the requests fail
        /** @var \Psr\Http\Message\ResponseInterface[] $responses */
        $responses = unwrap($promises);

        $stringResponses = [];
        foreach ($responses as $response) {
            $stringResponses[] = $response->getBody()->getContents();
        }

        return $stringResponses;
    }
}
