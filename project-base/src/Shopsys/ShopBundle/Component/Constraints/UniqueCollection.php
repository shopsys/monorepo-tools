<?php

namespace Shopsys\ShopBundle\Component\Constraints;

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
