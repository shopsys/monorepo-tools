<?php

namespace SS6\ShopBundle\Model\Product\TopProduct\Exception;

use Exception;
use SS6\ShopBundle\Model\Product\Exception\ProductException;
use SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductException;

class TopProductAlreadyExistsException extends Exception implements TopProductException {

	/**
	 * @param \Exception $previous
	 */
	public function __construct(Exception $previous = null) {
		parent::__construct('Top product already exists.', 0, $previous);
	}

}
