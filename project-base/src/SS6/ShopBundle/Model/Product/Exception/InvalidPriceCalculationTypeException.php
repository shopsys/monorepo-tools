<?php

namespace SS6\ShopBundle\Model\Product\Exception;

use Exception;

class InvalidPriceCalculationTypeException extends Exception implements ProductException {

	/**
	 * @param mixed $message
	 * @param \Exception $previous
	 */
	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
