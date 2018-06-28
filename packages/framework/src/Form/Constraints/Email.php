<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Email extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This value is not a valid email address.';
}
