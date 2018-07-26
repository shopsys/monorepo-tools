<?php

namespace Shopsys\MicroserviceProductSearch\Controller;

use Shopsys\MicroserviceProductSearch\Repository\ProductSearchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * @var \Shopsys\MicroserviceProductSearch\Repository\ProductSearchRepository
     */
    private $productSearchRepository;

    public function __construct(ProductSearchRepository $productSearchRepository)
    {
        $this->productSearchRepository = $productSearchRepository;
    }

    public function productIdsAction(Request $request, int $domainId): JsonResponse
    {
        $searchText = $request->query->get('searchText', '');

        return new JsonResponse([
            'productIds' => $this->productSearchRepository->getProductIdsBySearchText($domainId, $searchText),
        ]);
    }
}
