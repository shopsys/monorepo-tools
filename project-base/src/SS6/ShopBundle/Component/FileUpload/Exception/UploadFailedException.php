<?php

namespace SS6\ShopBundle\Component\FileUpload\Exception;

use Exception;

class UploadFailedException extends Exception implements FileUploadException {

	/**
	 * @param string $message
	 * @param int $code
	 * @param Exception $previous
	 */
	public function __construct($message = null, $code = 0, $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}
