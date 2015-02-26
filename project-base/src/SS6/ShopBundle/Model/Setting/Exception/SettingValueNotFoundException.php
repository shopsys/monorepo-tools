<?php

namespace SS6\ShopBundle\Model\Setting\Exception;

use Exception;

class SettingValueNotFoundException extends Exception implements SettingException {

	/**
	 * @param string|null $message
	 * @param \Exception $previous
	 */
	public function __construct($message = null, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
