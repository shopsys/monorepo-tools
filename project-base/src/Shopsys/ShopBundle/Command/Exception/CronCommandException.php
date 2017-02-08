<?php

namespace SS6\ShopBundle\Command\Exception;

use Exception;
use SS6\ShopBundle\Command\Exception\CommandException;

class CronCommandException extends Exception implements CommandException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
