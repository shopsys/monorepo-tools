<?php

namespace SS6\ShopBundle\Model\Product\Accessory;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Product;

class AccessoryFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Accessory\AccessoryRepository
	 */
	private $accessoryRepository;

	public function __construct(AccessoryRepository $accessoryRepository) {
		$this->accessoryRepository = $accessoryRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getTop3ListableAccessories(Product $product, $domainId, PricingGroup $pricingGroup) {
		return $this->accessoryRepository->getTop3ListableAccessories($product, $domainId, $pricingGroup);
	}

}
