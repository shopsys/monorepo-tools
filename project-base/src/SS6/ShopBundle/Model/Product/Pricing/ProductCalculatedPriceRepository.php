<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use SS6\ShopBundle\Model\Product\Product;

class ProductCalculatedPriceRepository {

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
	private function getProductCalculatedPriceRepository() {
		return $this->em->getRepository(ProductCalculatedPrice::class);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param string $priceWithVat
	 */
	public function saveCalculatedPrice(Product $product, $priceWithVat) {
		$productCalculatedPrice = $this->getProductCalculatedPriceRepository()->findOneBy([
			'product' => $product
		]);

		if ($productCalculatedPrice === null) {
			$productCalculatedPrice = new ProductCalculatedPrice($product, $priceWithVat);
			$this->em->persist($productCalculatedPrice);
		} else {
			$productCalculatedPrice->setPriceWithVat($priceWithVat);
		}
	}

}
