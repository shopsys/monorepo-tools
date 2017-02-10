<?php

namespace Shopsys\ShopBundle\Component\Javascript\Compiler\Constant\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Javascript\Compiler\Constant\Exception\JsConstantCompilerException;

class CannotConvertToJsonException extends Exception implements JsConstantCompilerException
{

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }

}
