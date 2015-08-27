<?php

namespace SS6\ShopBundle\Model\Product\Exception;

use Exception;
use SS6\ShopBundle\Model\Product\Exception\ProductException;
use SS6\ShopBundle\Model\Product\Exception\VariantException;

class VariantCannotBeVariantException extends Exception implements ProductException, VariantException {

	/**
	 * @param int $productId
	 * @param \Exception $previous
	 */
	public function __construct($productId, Exception $previous = null) {
		$message = 'Product with ID ' . $productId . ' is already variant.';
		parent::__construct($message, 0, $previous);
	}

}
