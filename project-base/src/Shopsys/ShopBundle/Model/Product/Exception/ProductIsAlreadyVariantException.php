<?php

namespace SS6\ShopBundle\Model\Product\Exception;

use Exception;
use SS6\ShopBundle\Model\Product\Exception\VariantException;

class ProductIsAlreadyVariantException extends Exception implements VariantException {

	/**
	 * @param int $productId
	 * @param \Exception|null $previous
	 */
	public function __construct($productId, Exception $previous = null) {
		$message = 'Product with ID ' . $productId . ' is already variant.';
		parent::__construct($message, 0, $previous);
	}

}
