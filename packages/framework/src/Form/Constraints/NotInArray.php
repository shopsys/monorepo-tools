<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotInArray extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Value must not be neither of following: {{ array }}';

    /**
     * @var array
     */
    public $array = [];

    public function getRequiredOptions()
    {
        return [
            'array',
        ];
    }
}
