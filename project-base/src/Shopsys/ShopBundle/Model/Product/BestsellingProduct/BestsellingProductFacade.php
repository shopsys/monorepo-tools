<?php

namespace Shopsys\ShopBundle\Model\Product\BestsellingProduct;

use DateTime;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory;

class BestsellingProductFacade
{
    const MAX_RESULTS = 10;
    const ORDERS_CREATED_AT_LIMIT = '-1 month';
    const MAX_SHOW_RESULTS = 3;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\BestsellingProduct\AutomaticBestsellingProductRepository
     */
    private $automaticBestsellingProductRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\BestsellingProduct\ManualBestsellingProductRepository
     */
    private $manualBestsellingProductRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory
     */
    private $productDetailFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductService
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
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\Detail\ProductDetail[]
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
