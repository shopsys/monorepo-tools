<?php

namespace SS6\ShopBundle\Model\Image\Exception;

use Exception;

class EntityMultipleImageException extends Exception implements ImageException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
