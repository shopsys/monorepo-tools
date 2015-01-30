<?php

namespace SS6\ShopBundle\Component\Javascript\Translator\Exception;

use Exception;
use SS6\ShopBundle\Component\Javascript\Translator\Exception\JsTranslatorCallParserException;

class UnsupportedNodeException extends Exception implements JsTranslatorCallParserException {

	/**
	 * @param string|null $message
	 * @param \Exception $previous
	 */
	public function __construct($message = null, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
