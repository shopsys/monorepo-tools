<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Product;

class ProductInputPriceService {

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
	 * @return string[]
	 */
	public function getDefaultIndexedByPricingGroupId(array $pricingGroups) {
		foreach ($pricingGroups as $pricingGroup) {
			$defaultInputPrices[$pricingGroup->getId()] = '0';
		}

		return $defaultInputPrices;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductInputPrice[] $inputPrices
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
	 * @return string[]
	 */
	public function getAllIndexedByPricingGroupId($inputPrices, $pricingGroups) {
		if (count($inputPrices) !== 0) {
			foreach ($inputPrices as $inputPrice) {
				$allInputPricesIndexedByPricingGroupId[$inputPrice->getPricingGroup()->getId()] = $inputPrice->getInputPrice();
			}
			return $allInputPricesIndexedByPricingGroupId;
		} else {
			return $this->getDefaultIndexedByPricingGroupId($pricingGroups);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $inputPrice
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductInputPrice $productInputPrice
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductInputPrice
	 */
	public function refresh(Product $product, PricingGroup $pricingGroup, $inputPrice, $productInputPrice) {
		if ($productInputPrice === null) {
			$productInputPrice = new ProductInputPrice($product, $pricingGroup, $inputPrice);
		} else {
			$productInputPrice->setInputPrice($inputPrice);
		}
		return $productInputPrice;
	}

}
