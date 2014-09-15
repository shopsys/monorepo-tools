<?php

namespace SS6\ShopBundle\Model\Setting\Exception;

use Exception;

class SettingValueNotFoundException extends Exception implements SettingException {
	
	/**
	 * @param mixed $message
	 * @param \Exception $previous
	 */
	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
	
}
