<?php

namespace SS6\ShopBundle\Component\Doctrine\Exception;

use Exception;

class InvalidCountOfAliasesException extends Exception {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Query builder has invalid count of root aliases ' . Debug::export($criteria), 0, $previous);
	}
}
