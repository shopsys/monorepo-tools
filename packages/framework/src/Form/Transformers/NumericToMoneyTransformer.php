<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Transformers;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NumericToMoneyTransformer implements DataTransformerInterface
{
    /**
     * @var int
     */
    protected $floatScale;

    /**
     * @param int $floatScale
     */
    public function __construct(int $floatScale)
    {
        $this->floatScale = $floatScale;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $value
     * @return string|null
     */
    public function transform($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Money) {
            return $value->getAmount();
        }

        throw new TransformationFailedException('Money or null must be provided.');
    }

    /**
     * @param string|float|int|null $value
     * @return \Shopsys\FrameworkBundle\Component\Money\Money|null $value
     */
    public function reverseTransform($value): ?Money
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            try {
                return Money::create($value);
            } catch (\Shopsys\FrameworkBundle\Component\Money\Exception\MoneyException $e) {
                $message = sprintf('Unable to create Money from the string "%s".', $value);

                throw new TransformationFailedException($message, 0, $e);
            }
        }

        if (is_int($value)) {
            return Money::create($value);
        }

        if (is_float($value)) {
            return Money::createFromFloat($value, $this->floatScale);
        }

        throw new TransformationFailedException('A string, a number or null must be provided.');
    }
}
