<?php

namespace Shopsys\MicroserviceProductSearchExport\Controller;

use Shopsys\MicroserviceProductSearchExport\Structure\Exception\StructureException;
use Shopsys\MicroserviceProductSearchExport\Structure\StructureManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StructureController
{
    /**
     * @var \Shopsys\MicroserviceProductSearchExport\Structure\StructureManager
     */
    private $structureManager;

    /**
     * @param \Shopsys\MicroserviceProductSearchExport\Structure\StructureManager $structureManager
     */
    public function __construct(StructureManager $structureManager)
    {
        $this->structureManager = $structureManager;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $domainId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createAction(Request $request, int $domainId): JsonResponse
    {
        try {
            $this->structureManager->createIndex($domainId);
            return new JsonResponse();
        } catch (StructureException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $domainId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction(Request $request, int $domainId): JsonResponse
    {
        $this->structureManager->deleteIndex($domainId);
        return new JsonResponse();
    }
}
