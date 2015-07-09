<?php

namespace SS6\ShopBundle\Model\Product\Exception;

use Exception;
use SS6\ShopBundle\Model\Product\Exception\ProductException;

class VariantCannotBeMainVariantException extends Exception implements ProductException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
