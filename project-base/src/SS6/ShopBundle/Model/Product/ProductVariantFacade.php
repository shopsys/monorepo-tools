<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;

class ProductVariantFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainProduct
	 * @param \SS6\ShopBundle\Model\Product\Product[] $variants
	 */
	public function createVariant(Product $mainProduct, array $variants) {
		$mainProduct->setVariants($variants);
		$this->em->flush();
	}

}
