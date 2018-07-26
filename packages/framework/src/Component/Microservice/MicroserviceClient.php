<?php

namespace Shopsys\FrameworkBundle\Component\Microservice;

use GuzzleHttp\Client;

class MicroserviceClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    /**
     * @param int $domainId
     * @param string $searchText
     * @return object
     */
    public function search(int $domainId, string $searchText) {
        $uri = sprintf('http://microservice-product-search:8000/%s/product-ids', $domainId);
        $response = $this->guzzleClient->get($uri, ['query' => ['searchText' => $searchText]]);

        $responseContent = $response->getBody()->getContents();

        return json_decode($responseContent);
    }
}
