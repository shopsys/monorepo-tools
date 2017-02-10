<?php

namespace Shopsys\ShopBundle\Component\Setting\Exception;

use Exception;

class SettingValueNotFoundException extends Exception implements SettingException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
