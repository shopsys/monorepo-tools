<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueCollection extends Constraint
{
    public $message = 'Values are duplicate.';

    public $fields = null;

    public $allowEmpty = false;
}
