<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice;
use SS6\ShopBundle\Model\Order\Item\QuantifiedProduct;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Pricing\PriceCalculation;
use SS6\ShopBundle\Model\Pricing\Rounding;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;

class QuantifiedProductPriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Rounding
	 */
	private $rounding;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\QuantifiedProduct
	 */
	private $quantifiedProduct;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $productPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PriceCalculation
	 */
	private $priceCalculation;

	public function __construct(
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		Rounding $rounding,
		PriceCalculation $priceCalculation
	) {
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->rounding = $rounding;
		$this->priceCalculation = $priceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedProduct $quantifiedProduct
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice
	 */
	public function calculatePrice(QuantifiedProduct $quantifiedProduct, $domainId, User $user = null) {
		$product = $quantifiedProduct->getProduct();
		if (!$product instanceof Product) {
			$message = 'Object "' . get_class($product) . '" is not valid for QuantifiedProductPriceCalculation.';
			throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidQuantifiedProductException($message);
		}

		$this->quantifiedProduct = $quantifiedProduct;
		$this->product = $product;
		$this->productPrice = $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
			$product,
			$domainId,
			$user
		);

		$quantifiedItemPrice = new QuantifiedItemPrice(
			$this->productPrice,
			new Price(
				$this->getTotalPriceWithoutVat(),
				$this->getTotalPriceWithVat()
			),
			$product->getVat()
		);

		return $quantifiedItemPrice;
	}

	/**
	 * @return string
	 */
	private function getTotalPriceWithoutVat() {
		return $this->getTotalPriceWithVat() - $this->getTotalPriceVatAmount();
	}

	/**
	 * @return string
	 */
	private function getTotalPriceWithVat() {
		return $this->productPrice->getPriceWithVat() * $this->quantifiedProduct->getQuantity();
	}

	/**
	 * @return string
	 */
	private function getTotalPriceVatAmount() {
		$vatPercent = $this->product->getVat()->getPercent();

		return $this->rounding->roundVatAmount(
			$this->getTotalPriceWithVat() * $this->priceCalculation->getVatCoefficientByPercent($vatPercent)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedProduct[quantifiedProductIndex] $quantifiedProducts
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice[quantifiedItemIndex]
	 */
	public function calculatePrices(array $quantifiedProducts, $domainId, User $user = null) {
		$quantifiedItemsPrices = [];
		foreach ($quantifiedProducts as $index => $quantifiedProduct) {
			$quantifiedItemsPrices[$index] = $this->calculatePrice($quantifiedProduct, $domainId, $user);
		}

		return $quantifiedItemsPrices;
	}

}
