<?php

namespace SS6\ShopBundle\Component\Form\Exception;

use Exception;
use SS6\ShopBundle\Component\Form\Exception\FormException;

class InvertedChoiceNotMultipleException extends Exception implements FormException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
