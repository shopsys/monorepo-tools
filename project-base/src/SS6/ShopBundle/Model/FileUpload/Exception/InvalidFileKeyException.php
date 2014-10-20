<?php

namespace SS6\ShopBundle\Model\FileUpload\Exception;

use Exception;

class InvalidFileKeyException extends Exception implements FileUploadException {

	/**
	 * @param mixed $key
	 * @param Exception $previous
	 */
	public function __construct($key, $previous = null) {
		parent::__construct('Upload file key ' . var_export($key) . ' is invalid', 0, $previous);
	}
}
