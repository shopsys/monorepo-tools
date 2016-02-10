<?php

namespace SS6\AutoServicesBundle\Compiler\Exception;

use Exception;
use SS6\AutoServicesBundle\Compiler\Exception\CompilerException;

class CannotResolveParameterException extends Exception implements CompilerException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
