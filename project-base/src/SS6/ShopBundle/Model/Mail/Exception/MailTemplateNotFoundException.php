<?php

namespace SS6\ShopBundle\Model\Mail\Exception;

use Exception;

class MailTemplateNotFoundException extends Exception implements MailException {

	/**
	 * @param mixed $message
	 * @param \Exception $previous
	 */
	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
