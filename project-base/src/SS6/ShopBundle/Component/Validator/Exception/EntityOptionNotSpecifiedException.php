<?php

namespace SS6\ShopBundle\Component\Validator\Exception;

use Exception;

class EntityOptionNotSpecifiedException extends Exception implements AnnotationException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
