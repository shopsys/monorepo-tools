<?php

namespace Shopsys\MicroserviceProductSearchExport\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class LiveCheckController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function checkAction(): JsonResponse
    {
        return new JsonResponse(['info' => 'running']);
    }
}
