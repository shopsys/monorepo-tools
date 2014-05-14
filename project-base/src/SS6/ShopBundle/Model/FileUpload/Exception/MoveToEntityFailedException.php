<?php

namespace SS6\ShopBundle\Model\FileUpload\Exception;

use Exception;

class MoveToEntityFailedException extends Exception implements FileUploadException {
	
	/**
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct($message = null, $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}