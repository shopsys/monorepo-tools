<?php

namespace SS6\ShopBundle\Component\Javascript\Compiler\Constant\Exception;

use Exception;
use SS6\ShopBundle\Component\Javascript\Compiler\Constant\Exception\JsConstantCompilerException;

class ConstantNotFoundException extends Exception implements JsConstantCompilerException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
