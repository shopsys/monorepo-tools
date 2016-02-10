<?php

namespace SS6\GeneratorBundle\Model\Exception;

use Exception;

class GeneratorTargetFileAlreadyExistsExpception extends Exception implements GeneratorException {

	/**
	 * @param string $filepath
	 * @param \Exception|null $previous
	 */
	public function __construct($filepath, Exception $previous = null) {
		$message = 'File "' . $filepath . '" already exists';
		parent::__construct($message, 0, $previous);
	}

}
