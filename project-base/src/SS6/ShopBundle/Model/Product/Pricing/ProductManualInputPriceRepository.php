<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPrice;
use SS6\ShopBundle\Model\Product\Product;

class ProductManualInputPriceRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getProductManualInputPriceRepository() {
		return $this->em->getRepository(ProductManualInputPrice::class);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPrice[]
	 */
	public function getByProduct(Product $product) {
		return $this->getProductManualInputPriceRepository()->findBy(['product' => $product]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPrice|null
	 */
	public function findByProductAndPricingGroup(Product $product, PricingGroup $pricingGroup) {
		return $this->getProductManualInputPriceRepository()->findOneBy([
			'product' => $product,
			'pricingGroup' => $pricingGroup,
		]);
	}

}
