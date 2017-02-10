<?php

namespace Shopsys\ShopBundle\Model\Product\Accessory;

use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\Product;

class ProductAccessoryFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository
     */
    private $productAccessoryRepository;

    public function __construct(ProductAccessoryRepository $productAccessoryRepository) {
        $this->productAccessoryRepository = $productAccessoryRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int $limit
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getTopOfferedAccessories(Product $product, $domainId, PricingGroup $pricingGroup, $limit) {
        return $this->productAccessoryRepository->getTopOfferedAccessories($product, $domainId, $pricingGroup, $limit);
    }
}
