<?php

namespace Shopsys\FrameworkBundle\Component\Microservice;

use GuzzleHttp\Client;

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

        $response = $this->guzzleClient->get($resource, ['query' => $parameters]);

        return json_decode($response->getBody()->getContents());
    }
}
