<?php

namespace SS6\ShopBundle\Model\FileUpload\Exception;

use Exception;
use SS6\ShopBundle\Component\Debug;

class InvalidFileKeyException extends Exception implements FileUploadException {

	/**
	 * @param mixed $key
	 * @param Exception $previous
	 */
	public function __construct($key, $previous = null) {
		parent::__construct('Upload file key ' . Debug::export($key) . ' is invalid', 0, $previous);
	}
}
