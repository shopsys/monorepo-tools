<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductCalculatedPriceFactory implements ProductCalculatedPriceFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    private $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string|null $priceWithVat
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPrice
     */
    public function create(
        Product $product,
        PricingGroup $pricingGroup,
        ?string $priceWithVat
    ): ProductCalculatedPrice {
        $classData = $this->entityNameResolver->resolve(ProductCalculatedPrice::class);

        return new $classData($product, $pricingGroup, $priceWithVat);
    }
}
