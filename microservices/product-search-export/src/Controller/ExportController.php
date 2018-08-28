<?php

namespace Shopsys\MicroserviceProductSearchExport\Controller;

use Shopsys\MicroserviceProductSearchExport\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ExportController
{
    /**
     * @var \Shopsys\MicroserviceProductSearchExport\Repository\ProductRepository
     */
    protected $productRepository;

    /**
     * @param \Shopsys\MicroserviceProductSearchExport\Repository\ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $domainId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(Request $request, int $domainId): JsonResponse
    {
        $data = json_decode($request->getContent(false), true);
        if (!is_array($data)) {
            return new JsonResponse(['message' => 'missing POST data'], 500);
        }
        $this->productRepository->bulkUpdate($domainId, $data);
        return new JsonResponse();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $domainId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction(Request $request, int $domainId): JsonResponse
    {
        $data = json_decode($request->getContent(false), true);
        if (!is_array($data)) {
            return new JsonResponse(['message' => 'missing POST data'], 500);
        }
        if (!isset($data['keep'])) {
            return new JsonResponse(['message' => 'missing keep filed'], 500);
        }
        $keepIds = $data['keep'];
        $this->productRepository->deleteNotPresent($domainId, $keepIds);
        return new JsonResponse();
    }
}
