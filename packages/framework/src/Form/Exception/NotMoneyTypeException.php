<?php

namespace Shopsys\FrameworkBundle\Form\Exception;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Form\Transformers\NumericToMoneyTransformer;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotMoneyTypeException extends UnexpectedTypeException
{
    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct($value, Money::class);

        $this->message .= sprintf(' - maybe you want to use "%s" as a model data transformer.', NumericToMoneyTransformer::class);
    }
}
