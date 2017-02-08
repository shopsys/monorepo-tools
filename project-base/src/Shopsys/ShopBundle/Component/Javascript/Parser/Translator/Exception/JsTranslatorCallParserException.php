<?php

namespace Shopsys\ShopBundle\Component\Javascript\Parser\Translator\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Javascript\Parser\Exception\JsParserException;

class JsTranslatorCallParserException extends Exception implements JsParserException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
