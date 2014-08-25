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
	 * @param \SS6\ShopBundle\Model\Product\PriceCalculation $priceCalculation
	 */
	public function __construct(EntityManager $em, PriceCalculation $priceCalculation) {
		$this->em = $em;
		$this->priceCalculation = $priceCalculation;
	}

	public function recalculateToInputPricesWithoutVat() {
		$this->recalculateInputPrice(PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT);
	}

	public function recalculateToInputPricesWithVat() {
		$this->recalculateInputPrice(PricingSetting::INPUT_PRICE_TYPE_WITH_VAT);
	}

	/**
	 * @param string $toInputPriceType
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	private function recalculateInputPrice($toInputPriceType) {
		$query = $this->em->createQueryBuilder()
			->select('p')
			->from(Product::class, 'p')
			->getQuery();

		foreach ($query->iterate() as $row) {
			$product = $row[0];
			/* @var $product \SS6\ShopBundle\Model\Product\Product */
			$productPrice = $this->priceCalculation->calculatePrice($product);

			if ($toInputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT) {
				$product->setPrice($productPrice->getBasePriceWithVat() - $productPrice->getBasePriceVatAmount());
			} elseif ($toInputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITH_VAT) {
				$product->setPrice($productPrice->getBasePriceWithVat());
			} else {
				throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
			}
		}

		$this->em->flush();
	}

}
