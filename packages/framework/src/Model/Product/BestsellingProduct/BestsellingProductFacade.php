<?php

namespace Shopsys\FrameworkBundle\Model\Product\BestsellingProduct;

use DateTime;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetailFactory;

class BestsellingProductFacade
{
    const MAX_RESULTS = 10;
    const ORDERS_CREATED_AT_LIMIT = '-1 month';
    const MAX_SHOW_RESULTS = 3;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\AutomaticBestsellingProductRepository
     */
    private $automaticBestsellingProductRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductRepository
     */
    private $manualBestsellingProductRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetailFactory
     */
    private $productDetailFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\BestsellingProductService
     */
    private $bestsellingProductService;

    public function __construct(
        AutomaticBestsellingProductRepository $automaticBestsellingProductRepository,
        ManualBestsellingProductRepository $manualBestsellingProductRepository,
        ProductDetailFactory $productDetailFactory,
        BestsellingProductService $bestsellingProductService
    ) {
        $this->automaticBestsellingProductRepository = $automaticBestsellingProductRepository;
        $this->manualBestsellingProductRepository = $manualBestsellingProductRepository;
        $this->productDetailFactory = $productDetailFactory;
        $this->bestsellingProductService = $bestsellingProductService;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetail[]
     */
    public function getAllOfferedProductDetails($domainId, Category $category, PricingGroup $pricingGroup)
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

        $combinedProducts = $this->bestsellingProductService->combineManualAndAutomaticProducts(
            $manualProductsIndexedByPosition,
            $automaticProducts,
            self::MAX_RESULTS
        );

        return $this->productDetailFactory->getDetailsForProducts($combinedProducts);
    }
}
