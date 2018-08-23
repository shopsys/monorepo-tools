<?php

namespace Shopsys\FrameworkBundle\Component\Microservice;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class MicroserviceClient
{
    const PARAMETER_DOMAIN_ID = 'domainId';

    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzleClient;

    /**
     * @param \GuzzleHttp\Client $guzzleClient
     */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param string $resource
     * @param array $parameters
     * @return mixed
     */
    public function get(string $resource, array $parameters = [])
    {
        if (array_key_exists(self::PARAMETER_DOMAIN_ID, $parameters)) {
            $domainId = (int)$parameters[self::PARAMETER_DOMAIN_ID];
            $resource = $domainId . '/' . $resource;

            unset($parameters[self::PARAMETER_DOMAIN_ID]);
        }

        $response = $this->guzzleClient->get($resource, [
            RequestOptions::QUERY => $parameters,
            RequestOptions::CONNECT_TIMEOUT => 0.1,
            RequestOptions::TIMEOUT => 1.0,
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public function post(string $resource, array $parameters = [])
    {
        $response = $this->guzzleClient->post($resource, [
            RequestOptions::JSON => $parameters,
            RequestOptions::CONNECT_TIMEOUT => 0.1,
            RequestOptions::TIMEOUT => 1.0,
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public function delete(string $resource, array $parameters = [])
    {
        $response = $this->guzzleClient->delete($resource, [
            RequestOptions::JSON => $parameters,
            RequestOptions::CONNECT_TIMEOUT => 0.1,
            RequestOptions::TIMEOUT => 1.0,
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public function patch(string $resource, array $parameters = [])
    {
        $response = $this->guzzleClient->patch($resource, [
            RequestOptions::JSON => $parameters,
            RequestOptions::CONNECT_TIMEOUT => 0.1,
            RequestOptions::TIMEOUT => 1.0,
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
