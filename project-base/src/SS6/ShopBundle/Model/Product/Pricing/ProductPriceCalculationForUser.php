<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Product\Product;

class ProductPriceCalculationForUser {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	public function __construct(
		ProductPriceCalculation $productPriceCalculation,
		CurrentCustomer $currentCustomer,
		PricingGroupFacade $pricingGroupFacade
	) {
		$this->productPriceCalculation = $productPriceCalculation;
		$this->currentCustomer = $currentCustomer;
		$this->pricingGroupFacade = $pricingGroupFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePriceForCurrentUser(Product $product) {
		return $this->productPriceCalculation->calculatePrice(
			$product,
			$this->currentCustomer->getPricingGroup()
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePriceForUserAndDomainId(Product $product, $domainId, User $user = null) {
		if ($user === null) {
			$pricingGroup = $this->pricingGroupFacade->getDefaultPricingGroupByDomainId($domainId);
		} else {
			$pricingGroup = $user->getPricingGroup();
		}

		return $this->productPriceCalculation->calculatePrice($product, $pricingGroup);
	}

}
