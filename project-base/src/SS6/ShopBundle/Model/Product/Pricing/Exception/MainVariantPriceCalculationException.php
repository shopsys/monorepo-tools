<?php

namespace SS6\ShopBundle\Model\Product\Pricing\Exception;

use Exception;
use SS6\ShopBundle\Model\Product\Pricing\Exception\ProductPricingException;
use SS6\ShopBundle\Model\Product\Product;

class MainVariantPriceCalculationException extends Exception implements ProductPricingException {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainVariant
	 * @param \Exception $previous
	 */
	public function __construct(Product $mainVariant, Exception $previous = null) {
		parent::__construct('Main variant ID = ' . $mainVariant->getId() . ' has no sellable variants.', 0, $previous);
	}

}
