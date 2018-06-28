<?php

namespace Shopsys\FrameworkBundle\Model\Product\BestsellingProduct;

use DateTime;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;

class BestsellingProductFacade
{
    const MAX_RESULTS = 10;
    const ORDERS_CREATED_AT_LIMIT = '-1 month';
    const MAX_SHOW_RESULTS = 3;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\AutomaticBestsellingProductRepository
     */
    protected $automaticBestsellingProductRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductRepository
     */
    protected $manualBestsellingProductRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\BestsellingProductService
     */
    protected $bestsellingProductService;

    public function __construct(
        AutomaticBestsellingProductRepository $automaticBestsellingProductRepository,
        ManualBestsellingProductRepository $manualBestsellingProductRepository,
        BestsellingProductService $bestsellingProductService
    ) {
        $this->automaticBestsellingProductRepository = $automaticBestsellingProductRepository;
        $this->manualBestsellingProductRepository = $manualBestsellingProductRepository;
        $this->bestsellingProductService = $bestsellingProductService;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    public function getAllOfferedBestsellingProducts($domainId, Category $category, PricingGroup $pricingGroup)
    {
        $manualBestsellingProducts = $this->manualBestsellingProductRepository->getOfferedByCategory(
            $domainId,
            $category,
            $pricingGroup
        );

        $manualProductsIndexedByPosition = [];
        foreach ($manualBestsellingProducts as $manualBestsellingProduct) {
            $manualProductsIndexedByPosition[$manualBestsellingProduct->getPosition()] = $manualBestsellingProduct->getProduct();
        }

        $automaticProducts = $this->automaticBestsellingProductRepository->getOfferedProductsByCategory(
            $domainId,
            $category,
            $pricingGroup,
            new DateTime(self::ORDERS_CREATED_AT_LIMIT),
            self::MAX_RESULTS
        );

        return $this->bestsellingProductService->combineManualAndAutomaticProducts(
            $manualProductsIndexedByPosition,
            $automaticProducts,
            self::MAX_RESULTS
        );
    }
}
