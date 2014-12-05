<?php

namespace SS6\ShopBundle\Component\Validator\Exception;

use Exception;

class TranslationsEntityNotSpecifiedException extends Exception implements AnnotationException {

	/**
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct($message = null, $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
