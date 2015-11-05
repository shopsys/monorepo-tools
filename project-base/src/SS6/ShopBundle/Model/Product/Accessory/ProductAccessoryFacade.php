<?php

namespace SS6\ShopBundle\Model\Product\Accessory;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Product;

class ProductAccessoryFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository
	 */
	private $productAccessoryRepository;

	public function __construct(ProductAccessoryRepository $productAccessoryRepository) {
		$this->productAccessoryRepository = $productAccessoryRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int $limit
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getTopOfferedAccessories(Product $product, $domainId, PricingGroup $pricingGroup, $limit) {
		return $this->productAccessoryRepository->getTopOfferedAccessories($product, $domainId, $pricingGroup, $limit);
	}

}
