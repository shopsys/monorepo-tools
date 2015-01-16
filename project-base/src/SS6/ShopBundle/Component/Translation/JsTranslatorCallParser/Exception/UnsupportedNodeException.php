<?php

namespace SS6\ShopBundle\Component\Translation\JsTranslatorCallParser\Exception;

use Exception;
use SS6\ShopBundle\Component\Translation\JsTranslatorCallParser\Exception\JsTranslatorCallParserException;

class UnsupportedNodeException extends Exception implements JsTranslatorCallParserException {

	/**
	 * @param string|null $message
	 * @param \Exception $previous
	 */
	public function __construct($message = null, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
