<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPrice;
use SS6\ShopBundle\Model\Product\Product;

class ProductInputPriceRepository {

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
	private function getProductInputPriceRepository() {
		return $this->em->getRepository(ProductInputPrice::class);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductInputPrice[]
	 */
	public function getByProduct(Product $product) {
		return $this->getProductInputPriceRepository()->findBy(['product' => $product]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductInputPrice|null
	 */
	public function findByProductAndPricingGroup(Product $product, PricingGroup $pricingGroup) {
		return $this->getProductInputPriceRepository()->findOneBy([
			'product' => $product,
			'pricingGroup' => $pricingGroup,
		]);
	}

}
