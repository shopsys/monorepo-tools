<?php

namespace SS6\ShopBundle\Model\Product\BestsellingProduct\Exception;

use Exception;
use SS6\ShopBundle\Model\Product\BestsellingProduct\Exception\BestsellingProductException;

class BestsellingProductAlreadyExistsException extends Exception implements BestsellingProductException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
