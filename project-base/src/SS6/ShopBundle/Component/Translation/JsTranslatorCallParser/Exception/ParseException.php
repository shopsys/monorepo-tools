<?php

namespace SS6\ShopBundle\Component\Translation\JsTranslatorCallParser\Exception;

use Exception;
use SS6\ShopBundle\Component\Translation\JsTranslatorCallParser\Exception\JsTranslatorCallParserException;

class ParseException extends Exception implements JsTranslatorCallParserException {

	/**
	 * @param string $message
	 * @param \Exception $previous
	 */
	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
