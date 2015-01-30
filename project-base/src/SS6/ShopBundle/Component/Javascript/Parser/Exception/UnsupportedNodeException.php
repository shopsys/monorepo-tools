<?php

namespace SS6\ShopBundle\Component\Javascript\Parser\Exception;

use Exception;
use SS6\ShopBundle\Component\Javascript\Parser\Exception\JsParserException;

class UnsupportedNodeException extends Exception implements JsParserException {

	/**
	 * @param string|null $message
	 * @param \Exception $previous
	 */
	public function __construct($message = null, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
