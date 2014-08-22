<?php

namespace SS6\ShopBundle\Model\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\PriceCalculation;
use SS6\ShopBundle\Model\Product\Product;

class InputPriceRepository {
	
	/** 
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\PriceCalculation
	 */
	private $priceCalculation;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em, PriceCalculation $priceCalculation) {
		$this->em = $em;
		$this->priceCalculation = $priceCalculation;
	}

	public function recalculateInputPricesWithoutVat() {
		$query = $this->em->createQueryBuilder()
			->select('p')
			->from(Product::class, 'p')
			->getQuery();

		foreach ($query->iterate() as $row) {
			$product = $row[0];
			/* @var $product \SS6\ShopBundle\Model\Product\Product */
			$productPrice = $this->priceCalculation->calculatePrice($product);
			$product->setPrice($productPrice->getBasePriceWithVat() - $productPrice->getBasePriceVatAmount());
		}

		$this->em->flush();
	}

	public function recalculateInputPricesWithVat() {
		$query = $this->em->createQueryBuilder()
			->select('p')
			->from(Product::class, 'p')
			->getQuery();

		foreach ($query->iterate() as $row) {
			$product = $row[0];
			/* @var $product \SS6\ShopBundle\Model\Product\Product */
			$productPrice = $this->priceCalculation->calculatePrice($product);
			$product->setPrice($productPrice->getBasePriceWithVat());
		}
		
		$this->em->flush();
	}

}
