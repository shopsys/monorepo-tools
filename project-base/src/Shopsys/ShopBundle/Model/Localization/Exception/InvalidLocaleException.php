<?php

namespace SS6\ShopBundle\Model\Localization\Exception;

use Exception;

class InvalidLocaleException extends Exception implements LocalizationException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
