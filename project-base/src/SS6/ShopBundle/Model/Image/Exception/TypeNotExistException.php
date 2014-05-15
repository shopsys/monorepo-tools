<?php

namespace SS6\ShopBundle\Model\Image\Exception;

use Exception;

class TypeNotExistException extends Exception implements ImageException {
	
	/**
	 * @param string $category
	 * @param string|null $type
	 * @param Exception $previous
	 */
	public function __construct($category, $type, $previous = null) {
		if ($type === null) {
			$message = sprintf('Default image type does not exist in %s category', $category);
		} else {
			$message = sprintf('Image type "%s" does not exist in "%s" category', $type, $category);
		}
		parent::__construct($message, 0, $previous);
	}
}