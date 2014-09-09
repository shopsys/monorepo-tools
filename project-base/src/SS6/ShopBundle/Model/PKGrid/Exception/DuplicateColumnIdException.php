<?php

namespace SS6\ShopBundle\Model\PKGrid\Exception;

use Exception;

class DuplicateColumnIdException extends Exception implements GridException {
	
	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
	
}
