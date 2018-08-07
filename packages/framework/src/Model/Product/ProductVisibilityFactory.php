<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;

class ProductVisibilityFactory implements ProductVisibilityFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductVisibility
     */
    public function create(
        Product $product,
        PricingGroup $pricingGroup,
        int $domainId
    ): ProductVisibility {
        return new ProductVisibility($product, $pricingGroup, $domainId);
    }
}
