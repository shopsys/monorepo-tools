<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotNegativeMoneyAmount extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Amount of money should be greater than or equal to zero.';
}
