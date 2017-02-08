<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
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
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
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
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductPrice
	 */
	public function calculatePriceForCurrentUser(Product $product) {
		return $this->productPriceCalculation->calculatePrice(
			$product,
			$this->domain->getId(),
			$this->currentCustomer->getPricingGroup()
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductPrice
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
