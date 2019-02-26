<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MoneyRange extends Constraint
{
    /**
     * @var string
     */
    public $minMessage = 'The amount of money should be {{ limit }} or more.';

    /**
     * @var string
     */
    public $maxMessage = 'The amount of money should be {{ limit }} or less.';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $min;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $max;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->validateMoneyOrNullOption('min', $options);
        $this->validateMoneyOrNullOption('max', $options);

        parent::__construct($options);

        if ($this->min === null && $this->max === null) {
            $message = sprintf('Either option "min" or "max" must be given for constraint "%s".', __CLASS__);

            throw new \Symfony\Component\Validator\Exception\MissingOptionsException($message, ['min', 'max']);
        }
    }

    /**
     * @param string $optionName
     * @param array $options
     */
    protected function validateMoneyOrNullOption(string $optionName, array $options): void
    {
        if (!isset($options[$optionName])) {
            return;
        }

        $value = $options[$optionName];
        if ($value !== null && !($value instanceof Money)) {
            $message = sprintf('The "%s" constraint requires the "%s" options to be either "%s" or null', __CLASS__, $optionName, Money::class);
            $message .= sprintf(', "%s" given.', \is_object($value) ? \get_class($value) : \gettype($value));

            throw new \Symfony\Component\Validator\Exception\ConstraintDefinitionException($message);
        }
    }
}
