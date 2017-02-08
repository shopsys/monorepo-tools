<?php

namespace Shopsys\ShopBundle\Component\Grid\Ordering\Exception;

use Exception;

class EntityIsNotOrderableException extends Exception implements OrderingException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
