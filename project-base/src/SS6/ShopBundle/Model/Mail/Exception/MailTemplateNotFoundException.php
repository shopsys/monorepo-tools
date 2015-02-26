<?php

namespace SS6\ShopBundle\Model\Mail\Exception;

use Exception;
use SS6\ShopBundle\Model\Mail\Exception\MailException;

class MailTemplateNotFoundException extends Exception implements MailException {

	/**
	 * @param string|null $message
	 * @param \Exception $previous
	 */
	public function __construct($message = null, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
