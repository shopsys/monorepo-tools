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
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getTop3OfferedAccessories(Product $product, $domainId, PricingGroup $pricingGroup) {
		return $this->productAccessoryRepository->getTop3OfferedAccessories($product, $domainId, $pricingGroup);
	}

}
