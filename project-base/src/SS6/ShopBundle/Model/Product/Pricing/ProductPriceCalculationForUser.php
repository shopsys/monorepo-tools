<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\Security\Core\SecurityContextInterface;

class ProductPriceCalculationForUser {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \Symfony\Component\Security\Core\SecurityContextInterface
	 */
	private $securityContext;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	public function __construct(
		ProductPriceCalculation $productPriceCalculation,
		SecurityContextInterface $securityContext,
		PricingGroupFacade $pricingGroupFacade
	) {
		$this->productPriceCalculation = $productPriceCalculation;
		$this->securityContext = $securityContext;
		$this->pricingGroupFacade = $pricingGroupFacade;
	}


	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePriceByCurrentUser(Product $product) {
		$user = $this->getCurrentUser();
		if ($user === null) {
			$pricingGroup = $this->pricingGroupFacade->getDefaultPricingGroupByCurrentDomain();
		} else {
			$pricingGroup = $user->getPricingGroup();
		}

		return $this->productPriceCalculation->calculatePrice($product, $pricingGroup);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function calculatePriceByUserAndDomainId(Product $product, $domainId, User $user = null) {
		if ($user === null) {
			$pricingGroup = $this->pricingGroupFacade->getDefaultPricingGroupByDomainId($domainId);
		} else {
			$pricingGroup = $user->getPricingGroup();
		}

		return $this->productPriceCalculation->calculatePrice($product, $pricingGroup);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	private function getCurrentUser() {
		$token = $this->securityContext->getToken();
		if ($token === null) {
			return null;
		}

		$user = $token->getUser();
		if (!$user instanceof User) {
			return null;
		}

		return $user;
	}

}
