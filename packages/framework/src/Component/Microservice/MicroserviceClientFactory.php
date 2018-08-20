<?php

namespace Shopsys\FrameworkBundle\Component\Microservice;

use GuzzleHttp\Client;

class MicroserviceClientFactory
{
    /**
     * @param string $microserviceUrl
     * @return \Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient
     */
    public function create(string $microserviceUrl): MicroserviceClient
    {
        $guzzleClient = new Client(['base_uri' => $microserviceUrl]);

        return new MicroserviceClient($guzzleClient);
    }
}
