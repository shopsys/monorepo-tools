<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductPriceCalculationForUser
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation
     */
    private $productPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        ProductPriceCalculation $productPriceCalculation,
        CurrentCustomer $currentCustomer,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        Domain $domain
    ) {
        $this->productPriceCalculation = $productPriceCalculation;
        $this->currentCustomer = $currentCustomer;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    public function calculatePriceForCurrentUser(Product $product)
    {
        return $this->productPriceCalculation->calculatePrice(
            $product,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    public function calculatePriceForUserAndDomainId(Product $product, $domainId, User $user = null)
    {
        if ($user === null) {
            $pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainId);
        } else {
            $pricingGroup = $user->getPricingGroup();
        }

        return $this->productPriceCalculation->calculatePrice($product, $domainId, $pricingGroup);
    }
}
