<?php

namespace SS6\ShopBundle\Model\Product\Accessory;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;

class AccessoryRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	public function __construct(ProductRepository $productRepository) {
		$this->productRepository = $productRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getTop3ListableAccessories(Product $product, $domainId, PricingGroup $pricingGroup) {
		$accessories = $product->getAccessories();

		$accessoriesIds = [];
		foreach ($accessories as $accessory) {
			$accessoriesIds[] = $accessory->getId();
		}

		$queryBuilder = $this->productRepository->getAllListableQueryBuilder($domainId, $pricingGroup);
		$queryBuilder->andWhere('p.id IN (:accessoriesIds)')
			->setParameter('accessoriesIds', $accessoriesIds)
			->setMaxResults(3);

		return $queryBuilder->getQuery()->getResult();
	}

}
