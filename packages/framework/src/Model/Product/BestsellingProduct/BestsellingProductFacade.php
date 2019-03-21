<?php

namespace Shopsys\FrameworkBundle\Model\Product\BestsellingProduct;

use DateTime;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;

class BestsellingProductFacade
{
    /** @access protected */
    const MAX_RESULTS = 10;
    /** @access protected */
    const ORDERS_CREATED_AT_LIMIT = '-1 month';
    public const MAX_SHOW_RESULTS = 3;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\AutomaticBestsellingProductRepository
     */
    protected $automaticBestsellingProductRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductRepository
     */
    protected $manualBestsellingProductRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\BestsellingProductCombinator
     */
    protected $bestsellingProductCombinator;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\AutomaticBestsellingProductRepository $automaticBestsellingProductRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductRepository $manualBestsellingProductRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\BestsellingProductCombinator $bestsellingProductCombinator
     */
    public function __construct(
        AutomaticBestsellingProductRepository $automaticBestsellingProductRepository,
        ManualBestsellingProductRepository $manualBestsellingProductRepository,
        BestsellingProductCombinator $bestsellingProductCombinator
    ) {
        $this->automaticBestsellingProductRepository = $automaticBestsellingProductRepository;
        $this->manualBestsellingProductRepository = $manualBestsellingProductRepository;
        $this->bestsellingProductCombinator = $bestsellingProductCombinator;
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

        return $this->bestsellingProductCombinator->combineManualAndAutomaticProducts(
            $manualProductsIndexedByPosition,
            $automaticProducts,
            self::MAX_RESULTS
        );
    }
}
