<?php

namespace Shopsys\FrameworkBundle\Component\Microservice;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class MicroserviceClient
{
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
        $options = array_merge(
            $this->createDefaultOptions(),
            [RequestOptions::QUERY => $parameters]
        );

        $response = $this->guzzleClient->get($resource, $options);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param string $resource
     * @param array $parameters
     * @return mixed
     */
    public function post(string $resource, array $parameters = [])
    {
        $options = $this->createJsonOptions($parameters);
        $response = $this->guzzleClient->post($resource, $options);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param string $resource
     * @param array $parameters
     * @return mixed
     */
    public function delete(string $resource, array $parameters = [])
    {
        $options = $this->createJsonOptions($parameters);
        $response = $this->guzzleClient->delete($resource, $options);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param string $resource
     * @param array $parameters
     * @return mixed
     */
    public function patch(string $resource, array $parameters = [])
    {
        $options = $this->createJsonOptions($parameters);
        $response = $this->guzzleClient->patch($resource, $options);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @return array
     */
    protected function createDefaultOptions(): array
    {
        return [
            RequestOptions::CONNECT_TIMEOUT => 0.1,
            RequestOptions::TIMEOUT => 1.0,
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
        ];
    }

    /**
     * @param array $jsonData
     * @return array
     */
    protected function createJsonOptions(array $jsonData): array
    {
        return array_merge(
            $this->createDefaultOptions(),
            [RequestOptions::JSON => $jsonData]
        );
    }
}
