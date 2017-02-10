<?php

namespace Shopsys\ShopBundle\Component\Entity\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Entity\Exception\EntityException;

class UnexpectedTypeException extends Exception implements EntityException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
