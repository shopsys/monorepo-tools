<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

class FieldsAreNotIdentical extends Constraint
{
    public $field1;

    public $field2;

    public $errorPath;

    public $message = 'Fields must not be identical';
}
