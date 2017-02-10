<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ShopBundle\Model\Product\Product;

class ProductPriceCalculationForUser
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
     */
    private $productPriceCalculation;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
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
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice
     */
    public function calculatePriceForCurrentUser(Product $product) {
        return $this->productPriceCalculation->calculatePrice(
            $product,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Customer\User|null $user
     * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice
     */
    public function calculatePriceForUserAndDomainId(Product $product, $domainId, User $user = null) {
        if ($user === null) {
            $pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainId);
        } else {
            $pricingGroup = $user->getPricingGroup();
        }

        return $this->productPriceCalculation->calculatePrice($product, $domainId, $pricingGroup);
    }
}
