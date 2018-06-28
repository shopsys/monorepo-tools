<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueProductParameters extends Constraint
{
    public $message = 'Product parameters are duplicate.';
}
