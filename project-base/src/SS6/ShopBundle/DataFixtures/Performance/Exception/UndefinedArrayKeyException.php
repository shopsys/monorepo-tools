<?php

namespace SS6\ShopBundle\DataFixtures\Performance\Exception;

use Exception;
use SS6\ShopBundle\DataFixtures\Performance\Exception\PerformanceException;

class UndefinedArrayKeyException extends Exception implements PerformanceException {

	/**
	 * @param string|int $key
	 * @param \Exception|null $previous
	 */
	public function __construct($key, Exception $previous = null) {
		parent::__construct('Key "' . $key . '" does not exists.', 0, $previous);
	}

}
